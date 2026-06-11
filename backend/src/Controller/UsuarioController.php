<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UsuarioService;

final class UsuarioController
{
    public function __construct(
        private readonly UsuarioService $service
    ) {}

    public function register(): array
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        $usuario = $this->service->cadastrar($username, $password);

        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['username']   = $usuario->getUsername();

        return [
            'success' => true,
            'message' => 'Cadastro realizado com sucesso!',
            'user'    => [
                'id'       => $usuario->getId(),
                'username' => $usuario->getUsername(),
            ]
        ];
    }

    public function login(): array
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        $usuario = $this->service->login($username, $password);

        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['username']   = $usuario->getUsername();

        return [
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'user'    => [
                'id'       => $usuario->getId(),
                'username' => $usuario->getUsername(),
            ]
        ];
    }

    public function logout(): array
    {
        unset($_SESSION['usuario_id']);
        unset($_SESSION['username']);
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logout realizado com sucesso!'
        ];
    }

    public function currentUser(): array
    {
        if (isset($_SESSION['usuario_id'])) {
            return [
                'success' => true,
                'logged_in' => true,
                'user' => [
                    'id'       => (int) $_SESSION['usuario_id'],
                    'username' => $_SESSION['username'],
                ]
            ];
        }

        return [
            'success' => true,
            'logged_in' => false
        ];
    }
}
