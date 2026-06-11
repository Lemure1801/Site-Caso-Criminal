<?php

declare(strict_types=1);

session_start();

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

require_once __DIR__ . '/../src/Model/Usuario.php';
require_once __DIR__ . '/../src/Repository/IUsuarioRepository.php';
require_once __DIR__ . '/../src/Repository/UsuarioRepository.php';
require_once __DIR__ . '/../src/Service/UsuarioService.php';
require_once __DIR__ . '/../src/Controller/UsuarioController.php';

use App\Database;
use App\Middleware\Middleware;
use App\Controller\ComentarioController;
use App\Repository\ComentarioRepository;
use App\Service\ComentarioService;
use App\Exception\BusinessRuleException;

use App\Repository\UsuarioRepository;
use App\Service\UsuarioService;
use App\Controller\UsuarioController;

try {
    // ── Container de DI ───────────────────────────────────
    $pdo               = Database::connect();
    $repository        = new ComentarioRepository($pdo);
    $service           = new ComentarioService($repository);
    $controller        = new ComentarioController($service);

    $usuarioRepository = new UsuarioRepository($pdo);
    $usuarioService    = new UsuarioService($usuarioRepository);
    $usuarioController = new UsuarioController($usuarioService);

    $action = $_GET['action'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];

    $response = match (true) {

        // Lista comentários aprovados
        $action === 'get_comments' && $method === 'GET'
            => $controller->index(),

        // Publica novo comentário
        $action === 'post_comment' && $method === 'POST'
            => (function () use ($controller): array {
                if (!isset($_SESSION['usuario_id'])) {
                    throw new BusinessRuleException('Você precisa estar logado para comentar.', 401);
                }
                $data = Middleware::sanitizeCommentInput();
                
                $body = json_decode(file_get_contents('php://input'), true) ?? [];
                $parentId = isset($body['parent_id']) ? (int)$body['parent_id'] : null;

                return $controller->store([
                    'nome'       => $_SESSION['username'],
                    'texto'      => $data['texto'],
                    'usuario_id' => (int) $_SESSION['usuario_id'],
                    'parent_id'  => $parentId,
                ]);
            })(),

        // Exclui um comentário próprio
        $action === 'delete_comment' && $method === 'POST'
            => (function () use ($controller): array {
                if (!isset($_SESSION['usuario_id'])) {
                    throw new BusinessRuleException('Você precisa estar logado para excluir um comentário.', 401);
                }
                $body = json_decode(file_get_contents('php://input'), true) ?? [];
                $commentId = isset($body['id']) ? (int)$body['id'] : 0;
                if ($commentId <= 0) {
                    throw new BusinessRuleException('ID do comentário inválido.', 400);
                }
                return $controller->destroy($commentId, (int)$_SESSION['usuario_id']);
            })(),

        // Registro de usuário
        $action === 'register' && $method === 'POST'
            => $usuarioController->register(),

        // Login de usuário
        $action === 'login' && $method === 'POST'
            => $usuarioController->login(),

        // Logout de usuário
        $action === 'logout' && $method === 'POST'
            => $usuarioController->logout(),

        // Checar usuário logado
        $action === 'current_user' && $method === 'GET'
            => $usuarioController->currentUser(),

        // Lista threads de discussão + respostas (tabela antiga/semente)
        $action === 'get_threads' && $method === 'GET'
            => getThreads($pdo),

        // Purga comentários expirados manualmente (mantido por retrocompatibilidade)
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