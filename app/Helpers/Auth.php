<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Guarda de autenticação baseada em sessão nativa do PHP.
 */
final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function login(int $userId, string $userName): void
    {
        // Regenera o ID de sessão para prevenir Session Fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
        $_SESSION['login_attempts'] = 0;
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Bloqueia o acesso à página atual caso o usuário não esteja autenticado.
     * Deve ser chamado no topo de toda página/endpoint privado.
     */
    public static function requireAuth(string $redirectTo = 'login.php'): void
    {
        if (!self::check()) {
            header("Location: {$redirectTo}");
            exit;
        }
    }
}