<?php

declare(strict_types=1);

/**
 * Ponto de entrada compartilhado por toda a aplicação.
 * Responsável por: autoload de classes, configuração de sessão segura
 * e carregamento das configurações globais.
 *
 * Todo arquivo em public/ deve começar com:
 *   require_once __DIR__ . '/../app/bootstrap.php';
 */

// --- Autoload manual (PSR-4 simplificado, sem Composer) ---
spl_autoload_register(function (string $class): void {
    // Namespace raiz "App\" mapeia para a pasta app/
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// --- Configuração segura de sessão (DEVE vir antes de session_start) ---
ini_set('session.cookie_httponly', '1');   // impede acesso ao cookie via JavaScript (mitiga XSS)
ini_set('session.use_strict_mode', '1');   // rejeita IDs de sessão não gerados pelo servidor
ini_set('session.cookie_samesite', 'Lax'); // mitiga CSRF em navegação cross-site

// Ativa cookie "secure" automaticamente quando servido via HTTPS
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

// Carrega variáveis de ambiente e constantes globais (define SESSION_LIFETIME, etc.)
require_once __DIR__ . '/Config/config.php';

session_set_cookie_params(SESSION_LIFETIME);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Expira sessão por inatividade, mesmo que o cookie ainda seja válido
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();