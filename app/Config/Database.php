<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Gerencia a conexão única (Singleton) com o banco de dados via PDO.
 *
 * Usei PDO em vez de mysqli por permitir prepared statements de forma
 * mais consistente e por facilitar uma eventual troca de SGBD no futuro.
 */
final class Database
{
    private static ?PDO $instance = null;

    // Impede instanciação direta da classe
    private function __construct()
    {
    }

    // Impede clonagem da instância (garante Singleton real)
    private function __clone()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = (string) env('DB_HOST', 'localhost');
            $dbName = (string) env('DB_NAME', 'kanban_db');
            $user = (string) env('DB_USER', 'root');
            $pass = (string) env('DB_PASS', '');

            $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // usa prepared statements nativos do MySQL
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Nunca expor detalhes de conexão/credenciais ao usuário final
                error_log('[DB CONNECTION ERROR] ' . $e->getMessage());
                throw new RuntimeException('Não foi possível conectar ao banco de dados.');
            }
        }

        return self::$instance;
    }
}