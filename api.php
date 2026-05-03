<?php
/**
 * api.php — Ponto de entrada da API (raiz do projeto)
 * Delega para a arquitetura em backend/public/index.php.
 *
 * Coloque este arquivo na mesma pasta do index.html.
 * A pasta backend/ fica fora do webroot por segurança.
 *
 * ═══════════════════════════════════════════════════════════
 *  SOLUÇÃO DO ERRO {"error":"Conexão falhou."}
 * ═══════════════════════════════════════════════════════════
 *
 *  O erro ocorre porque o MySQL não está acessível com as
 *  credenciais fornecidas. Siga estes passos:
 *
 *  1. Verifique se o MySQL está rodando:
 *       Windows: Serviços → MySQL (ou XAMPP/Laragon painel)
 *       Linux:   sudo systemctl status mysql
 *       Mac:     brew services list | grep mysql
 *
 *  2. Crie o banco e importe o schema (se ainda não fez):
 *       mysql -u root -p
 *       CREATE DATABASE caso91 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
 *       exit;
 *       mysql -u root -p caso91 < caso91_db.sql
 *
 *  3. Confirme que consegue conectar:
 *       mysql -u root -p caso91
 *       (digitar a senha e ver o prompt mysql>)
 *
 *  4. Se estiver no XAMPP/WAMP com root sem senha:
 *       Deixe DB_PASS vazio: password =   (no config.ini)
 *
 *  5. Se quiser um usuário dedicado (recomendado):
 *       CREATE USER 'caso91_user'@'localhost' IDENTIFIED BY 'SuaSenha';
 *       GRANT SELECT, INSERT, DELETE ON caso91.* TO 'caso91_user'@'localhost';
 *       FLUSH PRIVILEGES;
 *       Depois atualize config.ini com esse usuário e senha.
 *
 *  6. Ative o event_scheduler para limpeza automática:
 *       SET GLOBAL event_scheduler = ON;
 *       (ou adicione event_scheduler=ON no my.ini/my.cnf em [mysqld])
 * ═══════════════════════════════════════════════════════════
 */

declare(strict_types=1);

// Caminho para o front controller da arquitetura
$frontController = __DIR__ . '/backend/public/index.php';

if (!file_exists($frontController)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Arquitetura backend não encontrada.'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once $frontController;