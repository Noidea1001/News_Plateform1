<?php
/**
 * CMS Admin Users Entrypoint
 * news-platform / admin / users.php
 */


require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

$controller = new AdminController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->saveUser($_POST);
} else {
    $controller->users();
}
?>
