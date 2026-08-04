<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

/**
 * Camada de acesso a dados da tabela `users`.
 * Toda query usa prepared statements — nunca concatenar input do usuário em SQL.
 */
final class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function emailExists(string $email): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Cria um novo usuário. A senha em texto puro só existe em memória
     * durante esta chamada — é convertida em hash antes de qualquer persistência.
     */
    public static function create(string $name, string $email, string $plainPassword): int
    {
        $pdo = Database::getConnection();

        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
        );

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        return (int) $pdo->lastInsertId();
    }
}