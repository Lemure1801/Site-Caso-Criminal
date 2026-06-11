<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Comentario;
use App\Service\ComentarioService;

final class ComentarioController
{
    public function __construct(
        private readonly ComentarioService $service
    ) {}

    /**
     * Lista todos os comentarios aprovados.
     *
     * @return array{success: true, data: array<int, array{id: int, nome: string, texto: string, aprovado: bool, criado_em: string}>}
     */
    public function index(): array
    {
        $comentarios = $this->service->listarAprovados();

        $data = array_map(
            fn(Comentario $c): array => [
                'id'         => $c->getId(),
                'nome'       => htmlspecialchars($c->getNome(), ENT_QUOTES, 'UTF-8'),
                'texto'      => htmlspecialchars($c->getTexto(), ENT_QUOTES, 'UTF-8'),
                'aprovado'   => $c->isAprovado(),
                'criado_em'  => $c->getCriadoEm(),
                'usuario_id' => $c->getUsuarioId(),
                'parent_id'  => $c->getParentId(),
            ],
            $comentarios
        );

        return ['success' => true, 'data' => $data];
    }

    /**
     * Armazena um novo comentario.
     */
    public function store(array $data): array
    {
        $nome      = $data['nome'] ?? '';
        $texto     = $data['texto'] ?? '';
        $usuarioId = $data['usuario_id'] ?? null;
        $parentId  = $data['parent_id'] ?? null;

        $comentario = $this->service->criar($nome, $texto, $usuarioId, $parentId);

        return [
            'success' => true,
            'message' => 'Comentário publicado com sucesso!',
            'data'    => [
                'id'         => $comentario->getId(),
                'nome'       => htmlspecialchars($comentario->getNome(), ENT_QUOTES, 'UTF-8'),
                'texto'      => htmlspecialchars($comentario->getTexto(), ENT_QUOTES, 'UTF-8'),
                'aprovado'   => $comentario->isAprovado(),
                'criado_em'  => $comentario->getCriadoEm(),
                'usuario_id' => $comentario->getUsuarioId(),
                'parent_id'  => $comentario->getParentId(),
            ],
        ];
    }

    /**
     * Exclui um comentário.
     */
    public function destroy(int $id, int $currentUsuarioId): array
    {
        $this->service->deletar($id, $currentUsuarioId);

        return [
            'success' => true,
            'message' => 'Comentário excluído com sucesso!'
        ];
    }
}
