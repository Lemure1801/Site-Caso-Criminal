<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Comentario;
use PDO;

final class ComentarioRepository implements IComentarioRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    public function findAllAprovados(): array
    {
        // Purga expirados antes de listar (mantido por retrocompatibilidade)
        $this->purgarExpirados();

        $stmt = $this->pdo->query(
            'SELECT c.id, c.nome, c.texto, c.aprovado, c.criado_em, c.expira_em, c.ip_hash, c.usuario_id, c.parent_id, u.username
             FROM comentarios c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             ORDER BY c.criado_em DESC'
        );

        return array_map(
            fn(array $row): Comentario => new Comentario(
                id:        (int)  $row['id'],
                nome:             $row['username'] ?? $row['nome'],
                texto:            $row['texto'],
                aprovado:  (bool) $row['aprovado'],
                criadoEm:         $row['criado_em'],
                expiraEm:         $row['expira_em']  ?? null,
                ipHash:           $row['ip_hash']    ?? null,
                usuarioId: $row['usuario_id'] !== null ? (int)$row['usuario_id'] : null,
                parentId:  $row['parent_id'] !== null ? (int)$row['parent_id'] : null,
            ),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findById(int $id): ?Comentario
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.nome, c.texto, c.aprovado, c.criado_em, c.expira_em, c.ip_hash, c.usuario_id, c.parent_id, u.username
             FROM comentarios c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             WHERE c.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Comentario(
            id:        (int)  $row['id'],
            nome:             $row['username'] ?? $row['nome'],
            texto:            $row['texto'],
            aprovado:  (bool) $row['aprovado'],
            criadoEm:         $row['criado_em'],
            expiraEm:         $row['expira_em']  ?? null,
            ipHash:           $row['ip_hash']    ?? null,
            usuarioId: $row['usuario_id'] !== null ? (int)$row['usuario_id'] : null,
            parentId:  $row['parent_id'] !== null ? (int)$row['parent_id'] : null,
        );
    }

    public function save(Comentario $comentario): Comentario
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO comentarios (nome, texto, aprovado, criado_em, expira_em, ip_hash, usuario_id, parent_id)
             VALUES (:nome, :texto, :aprovado, :criado_em, :expira_em, :ip_hash, :usuario_id, :parent_id)'
        );

        $stmt->execute([
            ':nome'       => $comentario->getNome(),
            ':texto'      => $comentario->getTexto(),
            ':aprovado'   => $comentario->isAprovado() ? 1 : 0,
            ':criado_em'  => $comentario->getCriadoEm(),
            ':expira_em'  => $comentario->getExpiraEm(),
            ':ip_hash'    => $comentario->getIpHash(),
            ':usuario_id' => $comentario->getUsuarioId(),
            ':parent_id'  => $comentario->getParentId(),
        ]);

        return new Comentario(
            id:        (int) $this->pdo->lastInsertId(),
            nome:            $comentario->getNome(),
            texto:           $comentario->getTexto(),
            aprovado:        $comentario->isAprovado(),
            criadoEm:        $comentario->getCriadoEm(),
            expiraEm:        $comentario->getExpiraEm(),
            ipHash:          $comentario->getIpHash(),
            usuarioId:       $comentario->getUsuarioId(),
            parentId:        $comentario->getParentId(),
        );
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM comentarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function purgarExpirados(): int
    {
        return (int) $this->pdo->exec(
            "DELETE FROM comentarios
             WHERE aprovado = 0
               AND expira_em IS NOT NULL
               AND expira_em < NOW()"
        );
    }
}