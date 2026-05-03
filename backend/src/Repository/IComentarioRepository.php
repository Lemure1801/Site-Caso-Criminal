<?php
/**
 * IComentarioRepository.php — Passo 2 (Interface / Contrato)
 * Define os métodos obrigatórios para qualquer implementação de repositório
 * de comentários. O Service depende desta interface, nunca da implementação.
 * Isso garante desacoplamento e facilita testes (mock / stub).
 */

declare(strict_types=1);

interface IComentarioRepository
{
    /**
     * Persiste um Comentario no banco.
     * Deve preencher $comentario->id após inserção.
     */
    public function save(Comentario $comentario): bool;

    /**
     * Busca um comentário por ID.
     * Retorna null se não encontrado.
     */
    public function find(int $id): ?Comentario;

    /**
     * Remove um comentário pelo ID.
     */
    public function delete(int $id): bool;

    /**
     * Lista todos os comentários ainda ativos (não expirados).
     *
     * @return Comentario[]
     */
    public function listAtivos(): array;

    /**
     * Remove do banco todos os comentários temporários expirados.
     * Retorna o número de registros deletados.
     */
    public function purgarExpirados(): int;
}