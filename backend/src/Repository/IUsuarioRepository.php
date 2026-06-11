<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Usuario;

interface IUsuarioRepository
{
    public function findById(int $id): ?Usuario;
    public function findByUsername(string $username): ?Usuario;
    public function save(Usuario $usuario): Usuario;
}
