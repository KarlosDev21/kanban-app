<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Helpers\Auth;

header('Location: ' . (Auth::check() ? 'dashboard.php' : 'login.php'));
exit;