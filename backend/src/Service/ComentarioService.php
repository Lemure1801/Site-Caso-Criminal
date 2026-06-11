<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Comentario;
use App\Repository\IComentarioRepository;
use App\Exception\BusinessRuleException;

/**
 * ComentarioService — Regras de negócio de comentários.
 *
 * - NÃO instancia o Repository internamente (DI via construtor).
 * - Lança BusinessRuleException para falhas de regra.
 * - Não conhece PDO, HTML, $_POST nem headers HTTP.
 */
final class ComentarioService
{
    public function __construct(
        private readonly IComentarioRepository $repository
    ) {}

    /**
     * Lista comentários aprovados (usado pelo Controller como listarAprovados).
     * @return Comentario[]
     */
    public function listarAprovados(): array
    {
        return $this->repository->findAllAprovados();
    }

    /**
     * Cria e persiste um novo comentário (usado pelo Controller como criar).
     * @throws BusinessRuleException
     */
    public function criar(string $nome, string $texto, ?int $usuarioId = null, ?int $parentId = null): Comentario
    {
        if (mb_strlen(trim($texto)) < 3) {
            throw new BusinessRuleException('O comentário é muito curto (mínimo 3 caracteres).', 400);
        }

        if (mb_strlen($texto) > 5000) {
            throw new BusinessRuleException('O comentário excede 5000 caracteres.', 400);
        }

        if ($texto !== strip_tags($texto)) {
            throw new BusinessRuleException('Conteúdo inválido: tags HTML não são permitidas.', 422);
        }

        // Se parent_id for fornecido, verifica se o comentário pai existe
        if ($parentId !== null) {
            $parent = $this->repository->findById($parentId);
            if ($parent === null) {
                throw new BusinessRuleException('Comentário pai não encontrado.', 404);
            }
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $comentario = new Comentario(
            id:        0,
            nome:      mb_substr(trim($nome) ?: 'Anônimo', 0, 100),
            texto:     $texto,
            aprovado:  true,
            criadoEm:  date('Y-m-d H:i:s'),
            expiraEm:  null,
            ipHash:    hash('sha256', $ip),
            usuarioId: $usuarioId,
            parentId:  $parentId,
        );

        return $this->repository->save($comentario);
    }

    /**
     * Remove um comentário ou resposta após verificar a permissão de posse.
     * @throws BusinessRuleException
     */
    public function deletar(int $id, int $currentUsuarioId): void
    {
        $comentario = $this->repository->findById($id);
        if ($comentario === null) {
            throw new BusinessRuleException('Comentário não encontrado.', 404);
        }

        if ($comentario->getUsuarioId() !== $currentUsuarioId) {
            throw new BusinessRuleException('Você não tem permissão para apagar este comentário.', 403);
        }

        $this->repository->delete($id);
    }

    /**
     * Remove comentários expirados (mantido por retrocompatibilidade, agora sem efeito prático).
     */
    public function purgar(): int
    {
        return $this->repository->purgarExpirados();
    }
}