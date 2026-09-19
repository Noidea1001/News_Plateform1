<?php
/**
 * CMS Admin Categories Entrypoint
 * news-platform / admin / categories.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

$controller = new AdminController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->saveCategory($_POST);
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $csrfToken = $_GET['csrf_token'] ?? '';
    $controller->deleteCategory($id, $csrfToken);
} else {
    $controller->categories();
}
