-- ============================================================
-- Sistema de Gestão de Tarefas / Kanban
-- Schema inicial — MySQL 8+
-- Charset: utf8mb4 (suporte completo a acentos e emojis)
-- ============================================================

CREATE DATABASE IF NOT EXISTS kanban_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kanban_db;

-- ------------------------------------------------------------
-- Tabela: users
-- Armazena os dados de autenticação e perfil dos usuários
-- ------------------------------------------------------------
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL,
    password_hash   VARCHAR(255)        NOT NULL,  -- gerado via password_hash() (bcrypt)
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_users_email UNIQUE (email)  -- impede cadastro duplicado
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabela: tasks
-- Armazena as tarefas do Kanban, vinculadas a um usuário
-- ------------------------------------------------------------
CREATE TABLE tasks (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED        NOT NULL,
    title           VARCHAR(150)        NOT NULL,
    description     TEXT                NULL,

    status          ENUM('todo', 'in_progress', 'review', 'done')
                                         NOT NULL DEFAULT 'todo',

    priority        ENUM('baixa', 'media', 'alta')
                                         NOT NULL DEFAULT 'media',

    position        INT UNSIGNED        NOT NULL DEFAULT 0,
                    -- ordena os cards dentro de uma mesma coluna (drag-and-drop)

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_tasks_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE   -- se o usuário for removido, suas tarefas também são
        ON UPDATE CASCADE,

    -- Índice composto: a query mais comum do sistema é
    -- "buscar tarefas de UM usuário filtradas por status" (montar o board)
    INDEX idx_tasks_user_status (user_id, status),

    -- Útil para ordenação por prioridade dentro de listagens
    INDEX idx_tasks_priority (priority)

) ENGINE=InnoDB;