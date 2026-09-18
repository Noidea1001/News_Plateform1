<?php
/**
 * Delete Article Action Handler
 * news-platform / admin / actions / delete-article.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../src/Core/helpers.php';
require_once __DIR__ . '/../../src/Core/Database.php';
require_once __DIR__ . '/../../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../../src/Core/Auth.php';
require_once __DIR__ . '/../../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['csrf_token'] ?? '';

try {
    $controller = new AdminController();
    $controller->deleteArticle($id, $token);
} catch (Throwable $e) {
    header('Location: /admin/dashboard.php?error=' . urlencode('Delete Error: ' . $e->getMessage()));
    exit;
}
