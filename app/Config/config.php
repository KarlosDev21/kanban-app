<?php

declare(strict_types=1);

/**
 * Carrega variáveis de ambiente do arquivo .env
 * Implementação manual e simples (sem dependências externas).
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        throw new RuntimeException(
            "Arquivo .env não encontrado em: {$path}. Copie .env.example para .env e configure."
        );
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Ignora comentários e linhas vazias
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim(trim($value), "\"'");

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("{$name}={$value}");
        }
    }
}

/**
 * Helper para acessar variáveis de ambiente com valor padrão.
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

// Carrega o .env a partir da raiz do projeto (dois níveis acima de app/Config)
loadEnv(dirname(__DIR__, 2) . '/.env');

// Configurações gerais da aplicação
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_URL', env('APP_URL', 'http://localhost'));
define('SESSION_LIFETIME', (int) env('SESSION_LIFETIME', 7200));

// Exibição de erros conforme o ambiente (nunca exibir detalhes em produção)
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Log de erros sempre ativo, independente do ambiente
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__, 2) . '/storage/logs/app.log');