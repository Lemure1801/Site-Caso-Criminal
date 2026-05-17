<?php

declare(strict_types=1);

namespace App;

use PDO;
use RuntimeException;

final class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $configPath = self::findConfigPath();
            $config     = parse_ini_file($configPath);

            if ($config === false) {
                throw new RuntimeException('Falha ao ler config.ini.');
            }

            // Suporta tanto DB_HOST (formato antigo) quanto host (formato novo)
            $host    = $config['DB_HOST']    ?? $config['host']     ?? '127.0.0.1';
            $port    = $config['DB_PORT']    ?? $config['port']     ?? '3306';
            $dbname  = $config['DB_NAME']    ?? $config['name']     ?? '';
            $user    = $config['DB_USER']    ?? $config['user']     ?? '';
            $pass    = $config['DB_PASS']    ?? $config['password'] ?? '';

            // SOLUÇÃO DO ERRO 2002 (socket não encontrado):
            // Forçar TCP com 127.0.0.1 e porta explícita.
            // Nunca usar 'localhost' pois o PHP tenta socket Unix.
            if ($host === 'localhost') {
                $host = '127.0.0.1';
            }

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $host,
                $port,
                $dbname
            );

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$instance;
    }

    private static function findConfigPath(): string
    {
        // __DIR__ = backend/src
        $candidates = [
            __DIR__ . '/../config/config.ini',           // backend/config/config.ini
            __DIR__ . '/../../backend/config/config.ini', // fallback se chamado de outro nivel
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new RuntimeException(
            'config.ini não encontrado. Caminhos tentados: ' .
            implode(', ', $candidates) .
            ' — Copie config.ini.example para config.ini e preencha as credenciais.'
        );
    }
}
