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

    /**
     * @inheritDoc
     */
    public function findAllAprovados(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nome, texto, aprovado, criado_em 
             FROM comentarios 
             WHERE aprovado = 1 
             ORDER BY criado_em DESC'
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $row): Comentario => new Comentario(
                id: (int) $row['id'],
                nome: $row['nome'],
                texto: $row['texto'],
                aprovado: (bool) $row['aprovado'],
                criadoEm: $row['criado_em']
            ),
            $rows
        );
    }

    /**
     * @inheritDoc
     */
    public function save(Comentario $comentario): Comentario
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO comentarios (nome, texto, aprovado, criado_em) 
             VALUES (:nome, :texto, :aprovado, :criado_em)'
        );

        $stmt->execute([
            ':nome'      => $comentario->getNome(),
            ':texto'     => $comentario->getTexto(),
            ':aprovado'  => $comentario->isAprovado() ? 1 : 0,
            ':criado_em' => $comentario->getCriadoEm(),
        ]);

        return new Comentario(
            id: (int) $this->pdo->lastInsertId(),
            nome: $comentario->getNome(),
            texto: $comentario->getTexto(),
            aprovado: $comentario->isAprovado(),
            criadoEm: $comentario->getCriadoEm()
        );
    }
}
