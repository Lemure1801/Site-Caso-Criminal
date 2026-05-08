<?php

declare(strict_types=1);

namespace App\Model;

final class Comentario
{
    public function __construct(
        private readonly int $id,
        private readonly string $nome,
        private readonly string $texto,
        private readonly bool $aprovado,
        private readonly string $criadoEm
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getTexto(): string
    {
        return $this->texto;
    }

    public function isAprovado(): bool
    {
        return $this->aprovado;
    }

    public function getCriadoEm(): string
    {
        return $this->criadoEm;
    }
}
