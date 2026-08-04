<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Geração e validação de token CSRF vinculado à sessão do usuário.
 * Todo formulário de POST deve incluir Csrf::field() e todo processamento
 * de POST deve chamar Csrf::validate() antes de qualquer ação de escrita.
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\">";
    }

    public static function validate(?string $token): bool
    {
        if (empty($_SESSION[self::SESSION_KEY]) || $token === null || $token === '') {
            return false;
        }
        // hash_equals evita timing attacks na comparação
        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}