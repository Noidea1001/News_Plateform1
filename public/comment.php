<?php
/**
 * Public Reader Comments AJAX JSON Endpoint
 * news-platform / public / comment.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';
\App\Core\Auth::startSession();

header('Content-Type: application/json; charset=UTF-8');

use App\Controllers\PublicController;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method. POST required.']);
    exit;
}

try {
    $controller = new PublicController();
    $action = $_POST['action'] ?? 'add';

    if ($action === 'like') {
        $commentId = (int)($_POST['comment_id'] ?? 0);
        $response = $controller->likeComment($commentId);
    } else {
        $response = $controller->addComment($_POST);
    }

    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
}
