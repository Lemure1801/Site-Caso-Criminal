-- ============================================================
--  BANCO DE DADOS — Caso 91-1821
--  Compatível com MariaDB 10.3+ e MySQL 8+
--
--  SETUP COMPLETO (execute no terminal):
--
--  1. Criar banco:
--       mariadb -u root -p -e "CREATE DATABASE caso91
--         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
--
--  2. Importar schema:
--       mariadb -u root -p caso91 < caso91_db.sql
--
--  3. Ativar event_scheduler (MariaDB):
--     Opção A — temporário (só dura até reiniciar):
--       mariadb -u root -p -e "SET GLOBAL event_scheduler = ON;"
--     Opção B — permanente (recomendado):
--       Edite /etc/mysql/mariadb.conf.d/50-server.cnf (Linux)
--       ou my.ini (Windows) e adicione na seção [mysqld]:
--         event_scheduler = ON
--       Depois: sudo systemctl restart mariadb
--
--  4. Verificar se o event_scheduler está ativo:
--       mariadb -u root -p -e "SHOW VARIABLES LIKE 'event_scheduler';"
--       Deve mostrar: Value = ON
--
--  NOTA MariaDB: O comando de conexão é `mariadb` (não `mysql`),
--  mas o PDO continua usando o driver 'mysql:' no DSN — isso é normal.
-- ============================================================

USE caso91;

-- ── Tabela de comentários públicos ──────────────────────────
CREATE TABLE IF NOT EXISTS comentarios (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome          VARCHAR(120) NOT NULL DEFAULT 'Anônimo',
  texto         TEXT         NOT NULL,
  aprovado      TINYINT(1)   NOT NULL DEFAULT 0,
  criado_em     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expira_em     DATETIME     NULL,
  ip_hash       CHAR(64)     NULL,
  PRIMARY KEY   (id),
  INDEX idx_aprovado (aprovado),
  INDEX idx_expira   (expira_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabela de threads de discussão ──────────────────────────
CREATE TABLE IF NOT EXISTS threads (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario   VARCHAR(120) NOT NULL,
  handle    VARCHAR(60)  NOT NULL,
  iniciais  CHAR(4)      NOT NULL,
  data_post VARCHAR(30)  NOT NULL,
  texto     TEXT         NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabela de respostas às threads ──────────────────────────
CREATE TABLE IF NOT EXISTS respostas (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  thread_id INT UNSIGNED NOT NULL,
  usuario   VARCHAR(120) NOT NULL,
  handle    VARCHAR(60)  NOT NULL,
  iniciais  CHAR(4)      NOT NULL,
  data_post VARCHAR(30)  NOT NULL,
  texto     TEXT         NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_thread (thread_id),
  FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabela de administradores ────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username      VARCHAR(60)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  criado_em     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ultimo_login  DATETIME     NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Evento: limpeza automática de comentários expirados ──────
-- MariaDB aceita DELIMITER normalmente via CLI.
-- Se importar via phpMyAdmin, cole apenas o bloco CREATE EVENT
-- diretamente no executor de SQL (sem o DELIMITER).
DROP EVENT IF EXISTS limpar_comentarios_expirados;

DELIMITER $$

CREATE EVENT limpar_comentarios_expirados
  ON SCHEDULE EVERY 1 MINUTE
  COMMENT 'Remove comentarios temporarios expirados'
  DO
  BEGIN
    DELETE FROM comentarios
    WHERE aprovado = 0
      AND expira_em IS NOT NULL
      AND expira_em < NOW();
  END $$

DELIMITER ;

-- ── Dados iniciais: threads de discussão ─────────────────────
INSERT INTO threads (usuario, handle, iniciais, data_post, texto) VALUES
  ('Helena Voss', '@editora_voss', 'HV', '14 Mar 1991',
   'Iremos continuar noticiando. Achamos que possivelmente temos noção de quem é o cabeça do hospital.'),
  ('Padre Elias Monteverde', '@paroquia_cariara', 'PE', '22 Mar 1991',
   'É imperdoável o uso do nome Levíticos para um lugar profano. Meu arcanjo Miguel é o que me mantém são, pois a minha decisão seria já ter posto o lugar abaixo!'),
  ('Guilherme Azevedo', '@caso91_1821', 'GA', '01 Abr 1991',
   'Por favor, estou aceitando qualquer forma de ajuda. Tá acabando os remédios da Antônia, então preciso receber a indenização do hospital... O email é guilherme_azevedo@hotmail.com');

-- ── Dados iniciais: respostas ────────────────────────────────
INSERT INTO respostas (thread_id, usuario, handle, iniciais, data_post, texto) VALUES
  (1, 'Helena Voss', '@editora_voss', 'HV', '15 Mar 1991',
   'Enviado.'),
  (3, 'Anônimo', '@---', '?', '03 Abr 1991',
   'Trabalhei como zelador e sei que algumas mulheres ficavam zonzas, andavam pelo hospital sem falar com ninguém e sem acompanhamento. Não consigo assinar meu nome aqui.');