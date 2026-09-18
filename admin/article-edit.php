<?php
/**
 * Edit Article Endpoint
 * news-platform / admin / article-edit.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $controller = new AdminController();
    $controller->editArticleForm($id);
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>500 Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
