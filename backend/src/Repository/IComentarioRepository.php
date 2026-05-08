<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Comentario;

interface IComentarioRepository
{
    /**
     * Retorna todos os comentarios aprovados, ordenados do mais recente para o mais antigo.
     *
     * @return Comentario[]
     */
    public function findAllAprovados(): array;

    /**
     * Persiste um comentario e retorna a entidade com o ID gerado.
     */
    public function save(Comentario $comentario): Comentario;
}
