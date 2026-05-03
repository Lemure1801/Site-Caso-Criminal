<?php
/**
 * Database.php — Passo 1
 * Único responsável por ler config.ini e retornar a instância PDO.
 * Padrão Singleton: garante uma única conexão durante o ciclo da requisição.
 * Nenhum outro arquivo do sistema conhece as credenciais ou sabe conectar.
 */

declare(strict_types=1);

class Database
{
    private static ?PDO $instance = null;

    /** Impede instanciação externa */
    private function __construct() {}

    /** Impede clonagem */
    private function __clone() {}

    /**
     * Retorna a instância única do PDO.
     * Lê as credenciais exclusivamente do config.ini.
     *
     * @throws RuntimeException se o config.ini não for encontrado
     * @throws PDOException     se a conexão falhar
     */
    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $configPath = __DIR__ . '/../config/config.ini';

        if (!file_exists($configPath)) {
            throw new RuntimeException(
                'Arquivo config.ini não encontrado em: ' . $configPath
            );
        }

        $cfg = parse_ini_file($configPath, true);
        $db  = $cfg['database'];

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['port'] ?? '3306',
            $db['name'],
            $db['charset'] ?? 'utf8mb4'
        );

        self::$instance = new PDO($dsn, $db['user'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$instance;
    }
}