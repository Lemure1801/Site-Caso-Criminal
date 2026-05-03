<?php
/**
 * Comentario.php — Passo 2 (Model / Entidade)
 * Objeto simples com propriedades. Não contém SQL.
 * Todo acesso ao banco fica no Repository.
 */

declare(strict_types=1);

class Comentario
{
    public int     $id        = 0;
    public string  $nome      = 'Anônimo';
    public string  $texto     = '';
    public bool    $aprovado  = false;
    public ?string $expiraEm  = null;   // null = permanente
    public ?string $criadoEm  = null;
    public ?string $ipHash    = null;

    public function __construct(
        string  $nome,
        string  $texto,
        bool    $aprovado  = false,
        ?string $expiraEm  = null,
        ?string $ipHash    = null,
    ) {
        $this->nome     = $nome;
        $this->texto    = $texto;
        $this->aprovado = $aprovado;
        $this->expiraEm = $expiraEm;
        $this->ipHash   = $ipHash;
    }

    /** Retorna true se o comentário já expirou */
    public function expirou(): bool
    {
        if ($this->expiraEm === null) {
            return false;
        }
        return new DateTime() > new DateTime($this->expiraEm);
    }

    /** Representação para serialização / API */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'nome'      => $this->nome,
            'texto'     => $this->texto,
            'aprovado'  => $this->aprovado,
            'expira_em' => $this->expiraEm,
            'criado_em' => $this->criadoEm,
        ];
    }
}