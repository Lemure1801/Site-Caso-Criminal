<?php
/**
 * Middleware.php — Passo 5 (Middleware de Sanitização e Segurança)
 *
 * Responsabilidades:
 *  - Verificar campos obrigatórios antes de chegar ao Controller.
 *  - Sanitizar inputs contra XSS usando filter_input / htmlspecialchars.
 *  - Barrar requisições suspeitas (Content-Type incorreto, body vazio).
 *  - Configurar headers de segurança HTTP.
 */

declare(strict_types=1);

class Middleware
{
    /**
     * Aplica headers de segurança em toda resposta.
     * Chamado no início de cada requisição.
     */
    public static function securityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: no-referrer');
        // Em produção com HTTPS, descomente:
        // header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    /**
     * Valida e sanitiza o corpo JSON de uma requisição POST de comentário.
     * Aborta com 400 se o input for inválido ou conter tentativa de XSS.
     *
     * @return array{nome: string, texto: string}
     */
    public static function sanitizeCommentInput(): array
    {
        // Verifica Content-Type
        $ct = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!str_contains($ct, 'application/json')) {
            self::abort(415, 'Content-Type deve ser application/json.');
        }

        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, true);

        if (!is_array($body)) {
            self::abort(400, 'Corpo da requisição inválido.');
        }

        // Sanitização com filter_var + strip_tags (barrar XSS)
        $nome  = self::sanitizeString($body['nome']  ?? '');
        $texto = self::sanitizeString($body['texto'] ?? '');

        // Validação de presença
        if (empty(trim($texto))) {
            self::abort(400, 'O campo "texto" é obrigatório.');
        }

        // Detecção de tentativa de injeção de HTML/Script
        if ($nome !== strip_tags($nome) || $texto !== strip_tags($texto)) {
            self::abort(422, 'Conteúdo inválido: tags HTML não são permitidas.');
        }

        return ['nome' => $nome, 'texto' => $texto];
    }

    // ── Helpers privados ──────────────────────────────────────

    /**
     * Sanitiza uma string: remove tags, codifica entidades.
     */
    private static function sanitizeString(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }
        // Remove tags HTML e entidades perigosas
        $clean = strip_tags(trim($value));
        // Codifica caracteres especiais restantes
        return htmlspecialchars($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Termina a execução com resposta JSON de erro.
     */
    private static function abort(int $code, string $message): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}