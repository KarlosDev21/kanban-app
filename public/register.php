<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Validator;
use App\Models\User;

// Se já estiver logado, não faz sentido ver a tela de cadastro
if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Proteção CSRF: primeira coisa a validar, antes de qualquer processamento ---
    if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
        $errors['csrf'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        $old = ['name' => $name, 'email' => $email];

        $validator = new Validator();
        $validator
            ->required($name, 'name', 'nome')
            ->maxLength($name, 100, 'name', 'nome')
            ->required($email, 'email', 'e-mail')
            ->email($email)
            ->maxLength($email, 150, 'email', 'e-mail')
            ->required($password, 'password', 'senha')
            ->minLength($password, 8, 'password', 'senha')
            ->matches($password, $passwordConfirm, 'password_confirm', 'As senhas não coincidem.');

        $errors = $validator->errors();

        // Só consulta o banco se as validações de formato já passaram (evita query desnecessária)
        if (!isset($errors['email']) && User::emailExists($email)) {
            $errors['email'] = 'Este e-mail já está cadastrado.';
        }

        if (empty($errors)) {
            $userId = User::create($name, $email, $password);
            Auth::login($userId, $name);

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — Kanban App</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <h1>Criar conta</h1>

        <?php if (!empty($errors['csrf'])): ?>
            <p class="alert alert-error"><?= htmlspecialchars($errors['csrf'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <?= Csrf::field() ?>

            <label for="name">Nome</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars($old['name'], ENT_QUOTES, 'UTF-8') ?>"
                required
                maxlength="100"
                autofocus
            >
            <?php if (!empty($errors['name'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>"
                required
                maxlength="150"
            >
            <?php if (!empty($errors['email'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required minlength="8">
            <?php if (!empty($errors['password'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <label for="password_confirm">Confirmar senha</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
            <?php if (!empty($errors['password_confirm'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['password_confirm'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <button type="submit">Criar conta</button>
        </form>

        <p class="auth-switch">Já tem conta? <a href="login.php">Entrar</a></p>
    </main>

    <script src="assets/js/app.js" defer></script>
</body>
</html>