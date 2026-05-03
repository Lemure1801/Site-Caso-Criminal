<?php
/**
 * ComentarioService.php — Passo 3 (Service / Regra de Negócio)
 *
 * Regras de Ouro aplicadas:
 *  - O Service NÃO instancia o Repository internamente.
 *  - Recebe a Interface via __construct() (Injeção de Dependência).
 *  - Lança BusinessRuleException para qualquer falha de regra.
 *  - Não conhece PDO, HTML, $_POST nem headers HTTP.
 */

declare(strict_types=1);

require_once __DIR__ . '/../Model/Comentario.php';
require_once __DIR__ . '/../Repository/IComentarioRepository.php';
require_once __DIR__ . '/../Exception/BusinessRuleException.php';

class ComentarioService
{
    // ── Palavras-chave que tornam um comentário permanente ────
    private const KEYWORDS = [
        '1991','1990','1989','1988','1987','1986','1985','1984','1983','1982',
        '1981','1980','1979','1978','1965','1960','1958','1956','1999',
        'março','setembro','fevereiro','novembro','madrugada',
        'santos levíticos','santos leviticos','hospital','programa',
        'sem dor às mães','sem dor as maes','enfermeira',
        'antônia','antonia','guilherme','padre elias','elias',
        'helena voss','voss','valentino','bernardo','silas',
        'oswaldo','miguel','maria clara','guilherme_azevedo',
        'ypy','yvy pyahu','yvy','pyahu','ipbm','piab',
        'lírio','lirio','moloque','corpídeo','corpideo','composto',
        'cariará','cariara','tocantins','palmas','sino','igreja',
        'investigação','investigacao','processo',
        'indenização','indenizacao','sequela',
        'vegetativo','catatônico','catatonico',
        'testemunho','depoimento','zelador','lavanderia',
        'trabalhei','trabalhava','91-1821','1821',
    ];

    // ── Injeção de Dependência via construtor ─────────────────
    public function __construct(
        private readonly IComentarioRepository $repository
    ) {}

    /**
     * Valida e persiste um novo comentário.
     * Lança BusinessRuleException se alguma regra falhar.
     *
     * @throws BusinessRuleException
     */
    public function publicar(string $nome, string $texto, string $ip): Comentario
    {
        // ── Regras de negócio ──
        if (mb_strlen(trim($texto)) < 3) {
            throw new BusinessRuleException('O comentário é muito curto (mínimo 3 caracteres).', 400);
        }

        if (mb_strlen($texto) > 2000) {
            throw new BusinessRuleException('O comentário excede 2000 caracteres.', 400);
        }

        if ($this->contemXSS($texto)) {
            throw new BusinessRuleException('Conteúdo inválido detectado no comentário.', 422);
        }

        // ── Análise de keyword ──
        $aprovado = $this->contemKeyword($texto);
        $expiraEm = $aprovado ? null : date('Y-m-d H:i:s', time() + 600);

        $comentario = new Comentario(
            nome:      mb_substr(trim($nome) ?: 'Anônimo', 0, 120),
            texto:     $texto,
            aprovado:  $aprovado,
            expiraEm:  $expiraEm,
            ipHash:    hash('sha256', $ip),
        );

        if (!$this->repository->save($comentario)) {
            throw new BusinessRuleException('Não foi possível salvar o comentário.', 500);
        }

        return $comentario;
    }

    /**
     * Lista comentários ativos (não expirados).
     *
     * @return Comentario[]
     */
    public function listar(): array
    {
        return $this->repository->listAtivos();
    }

    /**
     * Remove comentários expirados manualmente.
     */
    public function purgar(): int
    {
        return $this->repository->purgarExpirados();
    }

    // ── Helpers privados ──────────────────────────────────────

    private function contemKeyword(string $text): bool
    {
        $lower = mb_strtolower($text);
        foreach (self::KEYWORDS as $kw) {
            if (str_contains($lower, mb_strtolower($kw))) {
                return true;
            }
        }
        return false;
    }

    private function contemXSS(string $text): bool
    {
        // Detecta tags HTML / scripts injetados
        return $text !== strip_tags($text);
    }
}