<?php
/**
 * Public Reader Comments AJAX JSON & Graceful Form Endpoint
 * news-platform / public / comment.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';
\App\Core\Auth::startSession();

use App\Controllers\PublicController;

// Check if incoming request is a genuine asynchronous AJAX call
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

// Fallback redirect destination for standard form submissions
// Validate redirect_to against own origin to prevent open redirect attacks
$articleId = (int)($_POST['article_id'] ?? 0);
$rawRedirect = $_POST['redirect_to'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
$defaultFallback = $articleId > 0 ? url("article.php?id={$articleId}") : url('index.php');

// Only allow same-origin redirects
$redirectTo = $defaultFallback;
if (!empty($rawRedirect)) {
    $parsedRedirect = parse_url($rawRedirect);
    $parsedSelf = parse_url(url(''));
    $sameHost = empty($parsedRedirect['host']) || $parsedRedirect['host'] === ($parsedSelf['host'] ?? '');
    if ($sameHost) {
        $redirectTo = $rawRedirect;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Invalid request method. POST required.']);
        exit;
    }
    header('Location: ' . $redirectTo);
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

    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response);
        exit;
    }

    // Standard non-AJAX POST fallback: redirect back to page without exposing raw JSON
    if (!empty($response['message'])) {
        $_SESSION['flash_msg'] = $response['message'];
    }
    header('Location: ' . $redirectTo . '#comments');
    exit;

} catch (Throwable $e) {
    error_log('[comment.php] ' . $e->getMessage());
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again.']);
        exit;
    }
    $_SESSION['flash_error'] = 'An error occurred while saving your comment.';
    header('Location: ' . $redirectTo . '#comments');
    exit;
}
