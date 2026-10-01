<?php
/**
 * NewsPlatform RESTful Public API v1 — Categories Endpoint
 * news-platform / public / api / v1 / categories.php
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Only GET is supported.']);
    exit;
}

require_once __DIR__ . '/../../../src/Core/helpers.php';
require_once __DIR__ . '/../../../src/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();

    $categories = $db->fetchAll(
        "SELECT c.id, c.name, c.slug, c.description,
                COUNT(a.id) as articles_count,
                COALESCE(SUM(a.views_count), 0) as total_views
         FROM categories c
         LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'published'
         GROUP BY c.id, c.name, c.slug, c.description
         ORDER BY articles_count DESC, c.name ASC"
    );

    $items = [];
    foreach ($categories as $cat) {
        $items[] = [
            'id' => (int)$cat['id'],
            'name' => cat_name($cat['name']),
            'raw_name' => $cat['name'],
            'slug' => $cat['slug'],
            'description' => $cat['description'] ?? '',
            'articles_count' => (int)$cat['articles_count'],
            'total_views' => (int)$cat['total_views'],
            'feed_url' => url('index.php?category=' . $cat['id'])
        ];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $items,
        'total' => count($items)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
