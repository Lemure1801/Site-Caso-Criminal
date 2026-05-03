<?php
/**
 * ComentarioRepository.php — Passo 2 (Repository)
 * Implementa IComentarioRepository.
 * É o ÚNICO lugar do sistema onde existe SQL relacionado a comentários.
 * Recebe o PDO via construtor (Injeção de Dependência).
 */

declare(strict_types=1);

require_once __DIR__ . '/../Model/Comentario.php';
require_once __DIR__ . '/IComentarioRepository.php';

class ComentarioRepository implements IComentarioRepository
{
    public function __construct(private readonly PDO $pdo) {}

    // ── save ──────────────────────────────────────────────────
    public function save(Comentario $comentario): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO comentarios (nome, texto, aprovado, expira_em, ip_hash)
             VALUES (:nome, :texto, :aprovado, :expira, :ip)'
        );

        $ok = $stmt->execute([
            ':nome'     => mb_substr($comentario->nome, 0, 120),
            ':texto'    => $comentario->texto,
            ':aprovado' => $comentario->aprovado ? 1 : 0,
            ':expira'   => $comentario->expiraEm,
            ':ip'       => $comentario->ipHash,
        ]);

        if ($ok) {
            $comentario->id = (int) $this->pdo->lastInsertId();
        }

        return $ok;
    }

    // ── find ──────────────────────────────────────────────────
    public function find(int $id): ?Comentario
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM comentarios WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    // ── delete ────────────────────────────────────────────────
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM comentarios WHERE id = :id'
        );
        return $stmt->execute([':id' => $id]);
    }

    // ── listAtivos ────────────────────────────────────────────
    public function listAtivos(): array
    {
        // Purga antes de listar para garantir consistência
        $this->purgarExpirados();

        $stmt = $this->pdo->query(
            'SELECT * FROM comentarios
             ORDER BY criado_em DESC
             LIMIT 200'
        );

        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    // ── purgarExpirados ───────────────────────────────────────
    public function purgarExpirados(): int
    {
        $stmt = $this->pdo->exec(
            "DELETE FROM comentarios
             WHERE aprovado = 0
               AND expira_em IS NOT NULL
               AND expira_em < NOW()"
        );
        return (int) $stmt;
    }

    // ── Hydration privada ─────────────────────────────────────
    private function hydrate(array $row): Comentario
    {
        $c            = new Comentario($row['nome'], $row['texto']);
        $c->id        = (int)  $row['id'];
        $c->aprovado  = (bool) $row['aprovado'];
        $c->expiraEm  = $row['expira_em'];
        $c->criadoEm  = $row['criado_em'];
        $c->ipHash    = $row['ip_hash'];
        return $c;
    }
}