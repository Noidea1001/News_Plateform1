<?php
/**
 * Public Reader Login
 * news-platform / public / login.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';

use App\Controllers\PublicController;

$controller = new PublicController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->loginReader($_POST);
} else {
    $controller->showLogin();
}
