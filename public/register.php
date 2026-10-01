<?php
/**
 * Public Reader Registration
 * news-platform / public / register.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';

use App\Controllers\PublicController;

$controller = new PublicController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->registerReader($_POST);
} else {
    $controller->showRegister();
}
