<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Middleware para sanitizacao e validacao de entrada.
 */
final class Middleware
{
    private const MAX_NOME_LENGTH  = 100;
    private const MAX_TEXTO_LENGTH = 5000;

    /**
     * Sanitiza os dados de entrada para comentarios.
     * Retorna array com 'nome' e 'texto' sanitizados.
     *
     * @return array{nome: string, texto: string}
     */
    public static function sanitizeCommentInput(): array
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        $nome  = self::sanitizeString($body['nome'] ?? '', self::MAX_NOME_LENGTH);
        $texto = self::sanitizeString($body['texto'] ?? '', self::MAX_TEXTO_LENGTH);

        return [
            'nome'  => $nome,
            'texto' => $texto,
        ];
    }

    /**
     * Sanitiza uma string: trim e limite de tamanho.
     * Nao aplica htmlspecialchars aqui - isso deve ser feito na exibicao.
     */
    private static function sanitizeString(mixed $value, int $maxLength): string
    {
        if (!is_string($value)) {
            return '';
        }

        $value = trim($value);
        
        if (mb_strlen($value) > $maxLength) {
            $value = mb_substr($value, 0, $maxLength);
        }

        return $value;
    }
}
