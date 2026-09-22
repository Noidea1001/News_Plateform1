<?php
/**
 * POST Processor for Article Save/Update (CMA Control Panel Action)
 * news-platform / admin / actions / save-article.php
 */


require_once __DIR__ . '/../../src/Core/helpers.php';
require_once __DIR__ . '/../../src/Core/Database.php';
require_once __DIR__ . '/../../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../../src/Core/Auth.php';
require_once __DIR__ . '/../../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/dashboard.php');
    exit;
}

try {
    $controller = new AdminController();
    $controller->saveArticle($_POST, $_FILES);
} catch (Throwable $e) {
    header('Location: /admin/dashboard.php?error=' . urlencode('Save Error: ' . $e->getMessage()));
    exit;
}
?>
