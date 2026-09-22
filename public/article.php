<?php
/**
 * Public Reader Article Renderer Endpoint (CDA Entrypoint)
 * news-platform / public / article.php
 */


require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Controllers/PublicController.php';

use App\Controllers\PublicController;

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

try {
    $controller = new PublicController();
    $controller->article($slug);
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>500 Internal Server Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>
