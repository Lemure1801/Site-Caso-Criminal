<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Usuario;
use PDO;

final class UsuarioRepository implements IUsuarioRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    public function findById(int $id): ?Usuario
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, username, password_hash, criado_em
             FROM usuarios
             WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Usuario(
            id:           (int) $row['id'],
            username:           $row['username'],
            passwordHash:       $row['password_hash'],
            criadoEm:           $row['criado_em'],
        );
    }

    public function findByUsername(string $username): ?Usuario
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, username, password_hash, criado_em
             FROM usuarios
             WHERE LOWER(username) = LOWER(:username)'
        );
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Usuario(
            id:           (int) $row['id'],
            username:           $row['username'],
            passwordHash:       $row['password_hash'],
            criadoEm:           $row['criado_em'],
        );
    }

    public function save(Usuario $usuario): Usuario
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (username, password_hash, criado_em)
             VALUES (:username, :password_hash, :criado_em)'
        );
        $stmt->execute([
            ':username'      => $usuario->getUsername(),
            ':password_hash' => $usuario->getPasswordHash(),
            ':criado_em'     => $usuario->getCriadoEm(),
        ]);

        return new Usuario(
            id:           (int) $this->pdo->lastInsertId(),
            username:           $usuario->getUsername(),
            passwordHash:       $usuario->getPasswordHash(),
            criadoEm:           $usuario->getCriadoEm(),
        );
    }
}
