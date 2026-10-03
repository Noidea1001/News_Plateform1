<?php
/**
 * Web Push Diagnostic & Automated System Verification Tool
 * news-platform / public / test_push_diag.php
 */
ob_start();
ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../src/Core/bootstrap.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/WebPush.php';

use App\Core\Database;
use App\Core\WebPush;

$results = [];

// Test 1: Database & Table Check
try {
    $db = Database::getInstance();
    $results['db_connection'] = '✅ Connected successfully to MySQL';

    $tables = ['articles', 'users', 'categories', 'notifications', 'push_subscriptions', 'readers', 'reader_subscriptions'];
    $missingTables = [];
    foreach ($tables as $tbl) {
        $check = $db->fetchColumn("SHOW TABLES LIKE '{$tbl}'");
        if (!$check) {
            $missingTables[] = $tbl;
        }
    }

    if (empty($missingTables)) {
        $results['tables_check'] = '✅ All 7 required database tables exist (including push_subscriptions)';
    } else {
        $results['tables_check'] = '❌ Missing tables: ' . implode(', ', $missingTables);
    }

    $subCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM push_subscriptions");
    $results['subscription_count'] = "ℹ️ Active device subscriptions in database: {$subCount}";

} catch (Throwable $e) {
    $results['db_connection'] = '❌ Database Error: ' . $e->getMessage();
}

// Test 2: VAPID Keys Check
try {
    $config = WebPush::getConfig();
    if (!empty($config['public_key']) && !empty($config['private_key'])) {
        $results['vapid_keys'] = '✅ VAPID Keys loaded successfully';
        $results['vapid_public_key'] = '🔑 Public Key: ' . substr($config['public_key'], 0, 30) . '...';
        $results['vapid_subject'] = '📧 Subject: ' . ($config['subject'] ?? 'mailto:alerts@newsplatform.local');
    } else {
        $results['vapid_keys'] = '❌ VAPID keys missing or empty';
    }
} catch (Throwable $e) {
    $results['vapid_keys'] = '❌ VAPID Key Error: ' . $e->getMessage();
}

// Test 3: OpenSSL Check
if (extension_loaded('openssl')) {
    $results['openssl'] = '✅ OpenSSL extension is active in PHP';
} else {
    $results['openssl'] = '❌ OpenSSL extension NOT loaded in php.ini';
}

// Test 4: Trigger Test Push if requested
$testStatus = null;
if (isset($_GET['action']) && $_GET['action'] === 'send_test_push') {
    try {
        $testPayload = [
            'title' => '🚨 TEST BREAKING NEWS: Diagnostics Push',
            'body' => 'This is a real-time test notification sent at ' . date('H:i:s'),
            'icon' => url('assets/icons/icon-192.png'),
            'badge' => url('assets/icons/icon-192.png'),
            'tag' => 'diag-push-' . time(),
            'data' => [
                'url' => url('index.php')
            ]
        ];
        $testStatus = WebPush::sendToAll($testPayload);
    } catch (Throwable $e) {
        $testStatus = ['error' => $e->getMessage()];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Web Push System Diagnostic | NewsPlatform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: system-ui, -apple-system, sans-serif; padding-top: 2rem; }
        .diag-card { max-width: 720px; margin: 0 auto; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
<div class="container">
    <div class="card diag-card border-0 bg-white p-4">
        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
            <h4 class="fw-bold mb-0 text-dark">🧪 Web Push Diagnostics Tool</h4>
            <span class="badge bg-danger">v2.6 System Check</span>
        </div>

        <h6 class="fw-bold text-secondary text-uppercase text-xs mb-3">System Health Checks:</h6>
        <ul class="list-group mb-4">
            <?php foreach ($results as $key => $val) { ?>
                <li class="list-group-item d-flex align-items-center justify-content-between py-2.5">
                    <span class="text-secondary small fw-semibold"><?= htmlspecialchars($key) ?></span>
                    <span class="small font-monospace"><?= htmlspecialchars($val) ?></span>
                </li>
            <?php } ?>
        </ul>

        <?php if ($testStatus !== null) { ?>
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <h6 class="fw-bold mb-2">🚀 Test Push Dispatch Result:</h6>
                <pre class="mb-0 text-xs font-monospace"><?= htmlspecialchars(print_r($testStatus, true)) ?></pre>
            </div>
        <?php } ?>

        <div class="d-flex gap-2">
            <a href="test_push_diag.php?action=send_test_push" class="btn btn-danger btn-sm fw-bold">
                📲 Broadcast Test Push Notification To All Registered Devices
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                &larr; Back to Website
            </a>
        </div>
    </div>
</div>
</body>
</html>
