<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Helpers\Auth;

Auth::logout();

header('Location: login.php');
exit;