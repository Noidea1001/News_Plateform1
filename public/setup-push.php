<?php
/**
 * Push Notification Setup & Test
 * Run this once to create the push_subscriptions table and test the system.
 * DELETE this file after running it!
 */
if (!isset($_GET['secret']) || $_GET['secret'] !== 'setup2024') {
    http_response_code(403);
    die('Forbidden. Add ?secret=setup2024 to the URL.');
}

require_once __DIR__ . '/../src/Core/bootstrap.php';
$db = App\Core\Database::getInstance();

echo "<pre style='font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:20px'>\n";
echo "=== Push Notifications Setup ===\n\n";

// 1. Create push_subscriptions table
try {
    $db->execute("CREATE TABLE IF NOT EXISTS `push_subscriptions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `endpoint` TEXT NOT NULL,
        `endpoint_hash` VARCHAR(64) NOT NULL UNIQUE,
        `p256dh` VARCHAR(255) NOT NULL,
        `auth` VARCHAR(255) NOT NULL,
        `reader_id` INT NULL,
        `user_agent` VARCHAR(255) NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `last_notified_at` DATETIME NULL,
        INDEX `idx_push_sub_reader` (`reader_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✅ push_subscriptions table created (or already exists)\n";
} catch (Exception $e) {
    echo "❌ Table creation error: " . $e->getMessage() . "\n";
}

// 2. Check subscription count
try {
    $count = $db->fetchColumn("SELECT COUNT(*) FROM push_subscriptions");
    echo "📊 Current push subscriptions in DB: {$count}\n";
} catch (Exception $e) {
    echo "❌ Count error: " . $e->getMessage() . "\n";
}

// 3. Check VAPID keys
try {
    $cfg = App\Core\WebPush::getConfig();
    $pubKey = $cfg['public_key'] ?? '';
    echo "🔑 VAPID Public Key: " . ($pubKey ? substr($pubKey, 0, 20) . '...' . substr($pubKey, -10) . " (len=" . strlen($pubKey) . ")" : "MISSING!") . "\n";
    echo "🔑 VAPID Private Key: " . (!empty($cfg['private_key']) ? 'SET (' . strlen($cfg['private_key']) . ' chars)' : 'MISSING!') . "\n";
    echo "📧 VAPID Subject: " . ($cfg['subject'] ?? 'N/A') . "\n";
} catch (Exception $e) {
    echo "❌ VAPID key error: " . $e->getMessage() . "\n";
}

// 4. If we have subscriptions and ?sendtest=1, fire a test push
if (!empty($_GET['sendtest'])) {
    try {
        $subs = $db->fetchAll("SELECT * FROM push_subscriptions LIMIT 5");
        echo "\n🚀 Sending test push to " . count($subs) . " subscriptions...\n";
        foreach ($subs as $sub) {
            $payload = json_encode([
                'title' => '🚨 Test Push Notification',
                'title_kh' => '🚨 ការជូនដំណឹង: សាកល្បង',
                'title_en' => '🚨 Test Push Notification',
                'body' => 'If you see this, background push is WORKING!',
                'body_kh' => 'ប្រសិនបើអ្នកឃើញនេះ ការជូនដំណឹងដំណើរការ!',
                'body_en' => 'If you see this, background push is WORKING!',
                'icon' => url('assets/icons/icon-192.png'),
                'badge' => url('assets/icons/icon-192.png'),
                'tag' => 'test-push-' . time(),
                'data' => ['url' => url('index.php')]
            ], JSON_UNESCAPED_UNICODE);
            $code = App\Core\WebPush::sendPush([
                'endpoint' => $sub['endpoint'],
                'p256dh' => $sub['p256dh'],
                'auth' => $sub['auth'],
            ], $payload);
            echo "  Sub ID {$sub['id']}: HTTP {$code} (" . ($code >= 200 && $code < 300 ? '✅ SUCCESS' : ($code === 410 ? '⚠️ Expired' : '❌ Failed')) . ")\n";
        }
    } catch (Exception $e) {
        echo "❌ Push send error: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "✅ Setup complete!\n";
echo "\nNext steps:\n";
echo "1. Open your app and click 'Enable Notifications' / bell icon\n";
echo "2. Come back here with ?secret=setup2024&sendtest=1 to test\n";
echo "3. DELETE this file (public/setup-push.php) after testing!\n";
echo "</pre>";
