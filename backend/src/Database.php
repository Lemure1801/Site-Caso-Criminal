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

    /**
     * Retorna a instancia unica do PDO.
     *
     * @throws RuntimeException Se nao conseguir conectar ou encontrar o config.
     */
    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $configPath = self::findConfigPath();
            $config = parse_ini_file($configPath);

            if ($config === false) {
                throw new RuntimeException('Falha ao ler arquivo de configuracao.');
            }

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['DB_HOST'],
                $config['DB_PORT'] ?? '3306',
                $config['DB_NAME']
            );

            self::$instance = new PDO(
                $dsn,
                $config['DB_USER'],
                $config['DB_PASS'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }

        return self::$instance;
    }

    /**
     * Localiza o arquivo config.ini.
     *
     * @throws RuntimeException Se nao encontrar o arquivo.
     */
    private static function findConfigPath(): string
    {
        // __DIR__ = backend/src
        // config esta em backend/config/config.ini
        $candidates = [
            __DIR__ . '/../config/config.ini',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new RuntimeException(
            'Arquivo config.ini nao encontrado. Caminhos tentados: ' . implode(', ', $candidates)
        );
    }
}
