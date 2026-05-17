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
    public function criar(string $nome, string $texto): Comentario
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

        $aprovado = $this->contemKeyword($texto);
        $expiraEm = $aprovado ? null : date('Y-m-d H:i:s', time() + 600);
        $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $comentario = new Comentario(
            id:       0,
            nome:     mb_substr(trim($nome) ?: 'Anônimo', 0, 100),
            texto:    $texto,
            aprovado: $aprovado,
            criadoEm: date('Y-m-d H:i:s'),
            expiraEm: $expiraEm,
            ipHash:   hash('sha256', $ip),
        );

        return $this->repository->save($comentario);
    }

    /**
     * Remove comentários expirados manualmente.
     */
    public function purgar(): int
    {
        return $this->repository->purgarExpirados();
    }

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
}