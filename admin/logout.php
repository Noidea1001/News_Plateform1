<?php
/**
 * CMS Staff Session Termination
 * news-platform / admin / logout.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';

use App\Core\Auth;

Auth::logout();

header('Location: ' . url('admin/login.php?msg=' . urlencode('You have been safely logged out.')));
exit;
