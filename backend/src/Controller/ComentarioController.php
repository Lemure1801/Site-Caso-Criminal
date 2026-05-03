<?php
/**
 * ComentarioController.php — Passo 4 (Controller Enxuto)
 *
 * Regras aplicadas:
 *  - Recebe dependências via __construct() (Injeção de Dependência).
 *  - O método store() contém APENAS um bloco try-catch.
 *  - Não valida regras de negócio — isso é responsabilidade do Service.
 *  - Não conhece SQL, PDO ou a implementação do Repository.
 */

declare(strict_types=1);

class ComentarioController
{
    // ── DI via construtor ─────────────────────────────────────
    public function __construct(
        private readonly ComentarioService $service
    ) {}

    // ── GET /api.php?action=get_comments ──────────────────────
    public function index(): void
    {
        $comentarios = $this->service->listar();
        $this->json(200, [
            'comments' => array_map(
                fn(Comentario $c) => $c->toArray(),
                $comentarios
            ),
        ]);
    }

    // ── POST /api.php?action=post_comment ─────────────────────
    public function store(): void
    {
        $body  = json_decode(file_get_contents('php://input'), true) ?? [];
        $nome  = (string) ($body['nome']  ?? '');
        $texto = (string) ($body['texto'] ?? '');
        $ip    = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        try {
            $comentario = $this->service->publicar($nome, $texto, $ip);

            $this->json(201, [
                'approved'  => $comentario->aprovado,
                'expira_em' => $comentario->expiraEm,
                'message'   => $comentario->aprovado
                    ? 'Comentário aprovado e publicado permanentemente.'
                    : 'Sem palavras-chave válidas. Será removido em 10 minutos.',
            ]);

        } catch (BusinessRuleException $e) {
            // Erro de regra de negócio — retorna ao cliente
            $this->json($e->getCode() ?: 422, ['error' => $e->getMessage()]);

        } catch (Throwable $e) {
            // Erro técnico inesperado — não expõe detalhes ao cliente
            error_log('[CASO91] Erro inesperado: ' . $e->getMessage());
            $this->json(500, ['error' => 'Erro interno. Tente novamente.']);
        }
    }

    // ── Helper de resposta JSON ───────────────────────────────
    private function json(int $code, array $data): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}