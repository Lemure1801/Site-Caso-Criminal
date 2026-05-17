<?php

declare(strict_types=1);

namespace App\Model;

final class Comentario
{
    public function __construct(
        private readonly int     $id,
        private readonly string  $nome,
        private readonly string  $texto,
        private readonly bool    $aprovado,
        private readonly string  $criadoEm,
        private readonly ?string $expiraEm = null,
        private readonly ?string $ipHash   = null,
    ) {}

    public function getId(): int           { return $this->id; }
    public function getNome(): string      { return $this->nome; }
    public function getTexto(): string     { return $this->texto; }
    public function isAprovado(): bool     { return $this->aprovado; }
    public function getCriadoEm(): string  { return $this->criadoEm; }
    public function getExpiraEm(): ?string { return $this->expiraEm; }
    public function getIpHash(): ?string   { return $this->ipHash; }
}