<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Usuario;
use App\Repository\IUsuarioRepository;
use App\Exception\BusinessRuleException;

final class UsuarioService
{
    public function __construct(
        private readonly IUsuarioRepository $repository
    ) {}

    public function cadastrar(string $username, string $password): Usuario
    {
        $username = trim($username);
        if (mb_strlen($username) < 3) {
            throw new BusinessRuleException('O nome de usuário deve ter pelo menos 3 caracteres.', 400);
        }
        if (mb_strlen($username) > 50) {
            throw new BusinessRuleException('O nome de usuário não deve exceder 50 caracteres.', 400);
        }
        if (!preg_match('/^[a-zA-Z0-9_áéíóúâêîôûãõçÁÉÍÓÚÂÊÎÔÛÃÕÇ\s\-]+$/u', $username)) {
            throw new BusinessRuleException('O nome de usuário possui caracteres inválidos.', 400);
        }
        if (mb_strlen($password) < 4) {
            throw new BusinessRuleException('A senha deve ter pelo menos 4 caracteres.', 400);
        }

        // Verifica se já existe
        if ($this->repository->findByUsername($username) !== null) {
            throw new BusinessRuleException('Este nome de usuário já está em uso.', 400);
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $usuario = new Usuario(
            id:           0,
            username:           $username,
            passwordHash:       $passwordHash,
            criadoEm:           date('Y-m-d H:i:s'),
        );

        return $this->repository->save($usuario);
    }

    public function login(string $username, string $password): Usuario
    {
        $username = trim($username);
        if (empty($username) || empty($password)) {
            throw new BusinessRuleException('Usuário e senha são obrigatórios.', 400);
        }

        $usuario = $this->repository->findByUsername($username);
        if ($usuario === null || !password_verify($password, $usuario->getPasswordHash())) {
            throw new BusinessRuleException('Usuário ou senha incorretos.', 400);
        }

        return $usuario;
    }
}
