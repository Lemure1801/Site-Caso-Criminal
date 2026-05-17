<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Autoload ──────────────────────────────────────────────
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Middleware/Middleware.php';
require_once __DIR__ . '/../src/Exception/BusinessRuleException.php';
require_once __DIR__ . '/../src/Model/Comentario.php';
require_once __DIR__ . '/../src/Repository/IComentarioRepository.php';
require_once __DIR__ . '/../src/Repository/ComentarioRepository.php';
require_once __DIR__ . '/../src/Service/ComentarioService.php';
require_once __DIR__ . '/../src/Controller/ComentarioController.php';

use App\Database;
use App\Middleware\Middleware;
use App\Controller\ComentarioController;
use App\Repository\ComentarioRepository;
use App\Service\ComentarioService;
use App\Exception\BusinessRuleException;

try {
    // ── Container de DI ───────────────────────────────────
    $pdo        = Database::connect();
    $repository = new ComentarioRepository($pdo);
    $service    = new ComentarioService($repository);
    $controller = new ComentarioController($service);

    $action = $_GET['action'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];

    $response = match (true) {

        // Lista comentários aprovados
        $action === 'get_comments' && $method === 'GET'
            => $controller->index(),

        // Publica novo comentário
        $action === 'post_comment' && $method === 'POST'
            => (function () use ($controller): array {
                $data = Middleware::sanitizeCommentInput();
                return $controller->store($data);
            })(),

        // Lista threads de discussão + respostas
        $action === 'get_threads' && $method === 'GET'
            => getThreads($pdo),

        // Purga comentários expirados manualmente
        $action === 'purge' && $method === 'GET'
            => ['success' => true, 'deleted' => $service->purgar()],

        default => throw new BusinessRuleException('Ação não encontrada.', 404),
    };

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (BusinessRuleException $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor.',
        'debug'   => $e->getMessage(),   // remover em produção
    ], JSON_UNESCAPED_UNICODE);
}

// ── Threads de discussão ──────────────────────────────────
function getThreads(PDO $pdo): array
{
    $threads = $pdo->query(
        'SELECT id, usuario, handle, iniciais, data_post, texto
         FROM threads ORDER BY id'
    )->fetchAll(PDO::FETCH_ASSOC);

    foreach ($threads as &$t) {
        $stmt = $pdo->prepare(
            'SELECT usuario, handle, iniciais, data_post, texto
             FROM respostas WHERE thread_id = :id ORDER BY id'
        );
        $stmt->execute([':id' => $t['id']]);
        $t['replies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return ['success' => true, 'threads' => $threads];
}