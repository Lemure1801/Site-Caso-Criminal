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
                'id'        => $c->getId(),
                'nome'      => htmlspecialchars($c->getNome(), ENT_QUOTES, 'UTF-8'),
                'texto'     => htmlspecialchars($c->getTexto(), ENT_QUOTES, 'UTF-8'),
                'aprovado'  => $c->isAprovado(),
                'criado_em' => $c->getCriadoEm(),
            ],
            $comentarios
        );

        return ['success' => true, 'data' => $data];
    }

    /**
     * Armazena um novo comentario.
     *
     * @param array{nome: string, texto: string} $data Dados ja sanitizados pelo Middleware
     * @return array{success: true, message: string, data: array{id: int, nome: string, texto: string, aprovado: bool, criado_em: string}}
     */
    public function store(array $data): array
    {
        $nome  = $data['nome'] ?? '';
        $texto = $data['texto'] ?? '';

        $comentario = $this->service->criar($nome, $texto);

        return [
            'success' => true,
            'message' => $comentario->isAprovado()
                ? 'Comentario publicado com sucesso!'
                : 'Comentario enviado para moderacao.',
            'data'    => [
                'id'        => $comentario->getId(),
                'nome'      => htmlspecialchars($comentario->getNome(), ENT_QUOTES, 'UTF-8'),
                'texto'     => htmlspecialchars($comentario->getTexto(), ENT_QUOTES, 'UTF-8'),
                'aprovado'  => $comentario->isAprovado(),
                'criado_em' => $comentario->getCriadoEm(),
            ],
        ];
    }
}
