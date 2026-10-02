<?php
/**
 * Web Push Subscription API Endpoint
 * news-platform / public / api / v1 / push-subscription.php
 */
ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../../../src/Core/bootstrap.php';
    require_once __DIR__ . '/../../../src/Core/WebPush.php';
    require_once __DIR__ . '/../../../src/Core/Auth.php';

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $publicKey = \App\Core\WebPush::getPublicKey();
        if (ob_get_length()) ob_end_clean();
        echo json_encode([
            'success' => true,
            'publicKey' => $publicKey,
            'subject' => \App\Core\WebPush::getSubject()
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($method === 'POST') {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data || !is_array($data)) {
            http_response_code(400);
            if (ob_get_length()) ob_end_clean();
            echo json_encode(['success' => false, 'error' => 'Invalid JSON payload.']);
            exit;
        }

        $action = $data['action'] ?? 'subscribe';
        $endpoint = trim($data['endpoint'] ?? '');

        if (empty($endpoint)) {
            http_response_code(400);
            if (ob_get_length()) ob_end_clean();
            echo json_encode(['success' => false, 'error' => 'Missing push endpoint.']);
            exit;
        }

        if ($action === 'unsubscribe') {
            \App\Core\WebPush::unsubscribe($endpoint);
            if (ob_get_length()) ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Unsubscribed from browser push alerts.']);
            exit;
        }

        $keys = $data['keys'] ?? [];
        $p256dh = trim($keys['p256dh'] ?? '');
        $auth = trim($keys['auth'] ?? '');

        if (empty($p256dh) || empty($auth)) {
            http_response_code(400);
            if (ob_get_length()) ob_end_clean();
            echo json_encode(['success' => false, 'error' => 'Missing subscription crypto keys (p256dh or auth).']);
            exit;
        }

        $reader = \App\Core\Auth::reader();
        $readerId = $reader ? (int)$reader['id'] : null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $ok = \App\Core\WebPush::subscribe($endpoint, $p256dh, $auth, $readerId, $ua);

        if ($ok) {
            // Send instant test push notification to confirm connection
            try {
                $testPayload = [
                    'title' => '🚨 Breaking News Alerts Enabled!',
                    'body' => 'You will now receive real-time breaking news notifications on this device.',
                    'icon' => url('assets/icons/icon-192.png'),
                    'badge' => url('assets/icons/icon-192.png'),
                    'tag' => 'welcome-push-' . time(),
                    'data' => [
                        'url' => url('index.php')
                    ]
                ];
                \App\Core\WebPush::sendPush([
                    'endpoint' => $endpoint,
                    'p256dh' => $p256dh,
                    'auth' => $auth
                ], json_encode($testPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            } catch (\Throwable $te) {
                error_log('[WebPush] Welcome test push error: ' . $te->getMessage());
            }
        }

        if (ob_get_length()) ob_end_clean();
        echo json_encode([
            'success' => $ok,
            'message' => 'Browser push notifications successfully enabled for breaking news alerts.'
        ]);
        exit;
    }

    http_response_code(405);
    if (ob_get_length()) ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;

} catch (\Throwable $e) {
    if (ob_get_length()) ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
