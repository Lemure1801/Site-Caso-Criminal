<?php

declare(strict_types=1);

namespace App\Model;

final class Usuario
{
    public function __construct(
        private readonly int    $id,
        private readonly string $username,
        private readonly string $passwordHash,
        private readonly string $criadoEm
    ) {}

    public function getId(): int           { return $this->id; }
    public function getUsername(): string  { return $this->username; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getCriadoEm(): string  { return $this->criadoEm; }
}
