<?php
/**
 * BusinessRuleException.php — Passo 3 (Exceção de Negócio)
 * Lançada pelo Service quando uma regra de negócio é violada.
 * Permite que o Controller diferencie erros de negócio de erros técnicos
 * sem usar if/else de validação no Controller.
 */

declare(strict_types=1);

class BusinessRuleException extends RuntimeException
{
    /**
     * @param string          $message  Mensagem amigável para exibir ao usuário
     * @param int             $code     Código HTTP sugerido (400, 422, etc.)
     * @param Throwable|null  $previous Exceção original encadeada, se houver
     */
    public function __construct(
        string     $message  = 'Regra de negócio violada.',
        int        $code     = 422,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}