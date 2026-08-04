<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Validator;
use App\Models\User;

if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

// --- Rate limiting simples contra brute-force (baseado em sessão) ---
const MAX_LOGIN_ATTEMPTS = 5;
const LOCKOUT_SECONDS = 300; // 5 minutos

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_locked_until'] = 0;
}

$isLocked = $_SESSION['login_locked_until'] > time();

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isLocked) {

    if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
        $errors['csrf'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $old = ['email' => $email];

        $validator = new Validator();
        $validator
            ->required($email, 'email', 'e-mail')
            ->email($email)
            ->required($password, 'password', 'senha');

        $errors = $validator->errors();

        if (empty($errors)) {
            $user = User::findByEmail($email);

            // Mensagem genérica proposital: não revela se o e-mail existe ou a senha está errada
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $_SESSION['login_attempts']++;

                if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
                    $_SESSION['login_locked_until'] = time() + LOCKOUT_SECONDS;
                    $errors['auth'] = 'Muitas tentativas. Tente novamente em alguns minutos.';
                } else {
                    $errors['auth'] = 'E-mail ou senha inválidos.';
                }
            } else {
                Auth::login((int) $user['id'], $user['name']);

                header('Location: dashboard.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — Kanban App</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <h1>Entrar</h1>

        <?php if ($isLocked): ?>
            <p class="alert alert-error">
                Muitas tentativas de login. Tente novamente em
                <?= (int) ceil(($_SESSION['login_locked_until'] - time()) / 60) ?> minuto(s).
            </p>
        <?php endif; ?>

        <?php if (!empty($errors['csrf'])): ?>
            <p class="alert alert-error"><?= htmlspecialchars($errors['csrf'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if (!empty($errors['auth'])): ?>
            <p class="alert alert-error"><?= htmlspecialchars($errors['auth'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <?= Csrf::field() ?>

            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>"
                required
                autofocus
            >
            <?php if (!empty($errors['email'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
            <?php if (!empty($errors['password'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <button type="submit" <?= $isLocked ? 'disabled' : '' ?>>Entrar</button>
        </form>

        <p class="auth-switch">Não tem conta? <a href="register.php">Cadastre-se</a></p>
    </main>

    <script src="assets/js/app.js" defer></script>
</body>
</html>