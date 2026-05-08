<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

/**
 * Excecao para regras de negocio violadas.
 */
final class BusinessRuleException extends Exception
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
