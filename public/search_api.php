<?php
/**
 * Real-Time Search API JSON Endpoint
 * news-platform / public / search_api.php
 */

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Controllers/PublicController.php';

use App\Controllers\PublicController;

try {
    $q = $_GET['q'] ?? '';
    $controller = new PublicController();
    $response = $controller->searchApi((string)$q);
    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'articles' => [], 'error' => $e->getMessage()]);
}
?>
