<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Helpers\Auth;

// Bloqueia acesso de qualquer usuário não autenticado
Auth::requireAuth();

$userName = $_SESSION['user_name'] ?? 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Kanban App</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <h1>Kanban App</h1>
        <div class="topbar-user">
            <span>Olá, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
            <a href="logout.php" class="btn-logout">Sair</a>
        </div>
    </header>

    <main class="dashboard-placeholder">
        <p>🚧 O board Kanban será implementado na Fase 3.</p>
        <p>✅ Autenticação da Fase 1 concluída com sucesso.</p>
    </main>

    <script src="assets/js/app.js" defer></script>
</body>
</html>