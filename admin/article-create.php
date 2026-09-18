<?php
/**
 * Draft Article Editor Endpoint
 * news-platform / admin / article-create.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Language.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

try {
    $controller = new AdminController();
    $controller->createArticleForm();
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>500 Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
