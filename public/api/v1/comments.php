<?php
/**
 * NewsPlatform RESTful Public API v1 — Comments Endpoint
 * news-platform / public / api / v1 / comments.php
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../../src/Core/helpers.php';
require_once __DIR__ . '/../../../src/Core/Database.php';
require_once __DIR__ . '/../../../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../../../src/Controllers/PublicController.php';

use App\Core\Database;
use App\Core\TemplateEngine;
use App\Controllers\PublicController;

try {
    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    // ── GET: List comments for an article ────────────────────────────────────
    if ($method === 'GET') {
        $articleId = (int)($_GET['article_id'] ?? 0);
        if ($articleId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'article_id query parameter is required.']);
            exit;
        }

        $comments = $db->fetchAll(
            "SELECT id, parent_id, user_name, content, likes_count, created_at
             FROM comments
             WHERE article_id = :aid AND status = 'approved'
             ORDER BY created_at ASC",
            ['aid' => $articleId]
        );

        foreach ($comments as &$c) {
            $c['time_ago'] = TemplateEngine::timeAgo($c['created_at']);
        }
        unset($c);

        echo json_encode([
            'status' => 'success',
            'data' => $comments,
            'total' => count($comments)
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ── POST: Submit comment ────────────────────────────────────────────────
    if ($method === 'POST') {
        // Support both JSON body and standard Form POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $postData = $_POST;
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $postData = array_merge($postData, $json);
            }
        }

        $controller = new PublicController();
        $res = $controller->addComment($postData);

        if (!$res['success']) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $res['message']]);
            exit;
        }

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => $res['message'],
            'data' => $res['comment']
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed.']);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
