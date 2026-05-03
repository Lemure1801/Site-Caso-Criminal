<?php
/**
 * index.php (public/index.php) — Passo 5
 * Front Controller + Container de Injeção de Dependência simplificado.
 *
 * É aqui — e APENAS aqui — que as dependências são instanciadas e montadas:
 *   PDO  →  Repository  →  Service  →  Controller
 *
 * Nenhuma outra classe instancia suas próprias dependências.
 *
 * Para rodar localmente:
 *   cd backend/public && php -S localhost:8000
 *
 * Endpoints disponíveis:
 *   GET  /?action=get_comments   → lista comentários ativos
 *   POST /?action=post_comment   → publica novo comentário
 *   GET  /?action=get_threads    → lista threads de discussão
 *   GET  /?action=purge          → remove comentários expirados
 */

declare(strict_types=1);

// ── Autoload manual (sem Composer para simplicidade) ─────────
$srcPath = __DIR__ . '/../src';

require_once $srcPath . '/Database.php';
require_once $srcPath . '/Model/Comentario.php';
require_once $srcPath . '/Repository/IComentarioRepository.php';
require_once $srcPath . '/Repository/ComentarioRepository.php';
require_once $srcPath . '/Exception/BusinessRuleException.php';
require_once $srcPath . '/Service/ComentarioService.php';
require_once $srcPath . '/Controller/ComentarioController.php';
require_once $srcPath . '/Middleware/Middleware.php';

// ── Headers de segurança — aplicados em toda requisição ──────
Middleware::securityHeaders();

// ── CORS — descomente para desenvolvimento local ─────────────
// header('Access-Control-Allow-Origin: http://localhost:8080');
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type');
// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

// ── Montagem do grafo de dependências (Container DI) ─────────
// PDO vem do Singleton — nunca instanciado diretamente aqui
try {
    $pdo = Database::getInstance();
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuração do servidor ausente.'], JSON_UNESCAPED_UNICODE);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    // Em produção, nunca exponha $e->getMessage() ao cliente
    error_log('[CASO91] Falha na conexão com o banco: ' . $e->getMessage());
    echo json_encode(['error' => 'Serviço temporariamente indisponível.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Injeção de Dependência: Repository ← PDO
$repository = new ComentarioRepository($pdo);

// Injeção de Dependência: Service ← Repository (via Interface)
$service = new ComentarioService($repository);

// Injeção de Dependência: Controller ← Service
$controller = new ComentarioController($service);

// ── Roteamento ────────────────────────────────────────────────
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

match (true) {
    // GET: listar comentários ativos
    $action === 'get_comments' && $method === 'GET'
        => $controller->index(),

    // POST: publicar comentário (com middleware de sanitização)
    $action === 'post_comment' && $method === 'POST'
        => (function () use ($controller): void {
            Middleware::sanitizeCommentInput(); // valida antes de passar ao controller
            $controller->store();
        })(),

    // GET: listar threads de discussão (lê direto do banco via PDO)
    $action === 'get_threads' && $method === 'GET'
        => getThreads($pdo),

    // GET: purgar manualmente comentários expirados
    $action === 'purge' && $method === 'GET'
        => respond(200, ['deleted' => $service->purgar()]),

    // Fallback
    default => respond(404, ['error' => 'Rota não encontrada.']),
};

// ── Helpers globais ───────────────────────────────────────────

function getThreads(PDO $pdo): void
{
    $threads = $pdo->query(
        'SELECT id, usuario, handle, iniciais, data_post, texto FROM threads ORDER BY id'
    )->fetchAll(PDO::FETCH_ASSOC);

    foreach ($threads as &$t) {
        $stmt = $pdo->prepare(
            'SELECT usuario, handle, iniciais, data_post, texto
             FROM respostas WHERE thread_id = :id ORDER BY id'
        );
        $stmt->execute([':id' => $t['id']]);
        $t['replies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    respond(200, ['threads' => $threads]);
}

function respond(int $code, array $data): never
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}