<?php
namespace App\Services;

/**
 * Service: Telegram Channel Auto-Broadcast Bot
 * news-platform / src / Services / TelegramService.php
 * Automatically broadcasts published news articles to Telegram Channels.
 */
class TelegramService
{
    private static function getBotToken(): string
    {
        return getenv('TELEGRAM_BOT_TOKEN') ?: '7412345678:AAExampleTelegramBotTokenPlaceholder';
    }

    private static function getChannelId(): string
    {
        return getenv('TELEGRAM_CHANNEL_ID') ?: '@newsplatform_kh';
    }

    /**
     * Broadcast an article to Telegram Channel
     */
    public static function broadcastArticle(array $article, string $articleUrl): array
    {
        $token = self::getBotToken();
        $channelId = self::getChannelId();

        if (empty($token) || str_contains($token, 'Placeholder')) {
            return [
                'success' => false,
                'message' => 'Telegram Bot Token not configured. Set TELEGRAM_BOT_TOKEN environment variable.'
            ];
        }

        $title = $article['title_kh'] ?? ($article['title'] ?? 'ព័ត៌មានថ្មី');
        $summary = $article['summary_kh'] ?? ($article['summary'] ?? '');
        $category = $article['category_name'] ?? 'ព័ត៌មានទូទៅ';
        $imageUrl = !empty($article['featured_image']) ? $article['featured_image'] : null;

        $caption = "📢 <b>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</b>\n\n";
        if (!empty($summary)) {
            $caption .= htmlspecialchars($summary, ENT_QUOTES, 'UTF-8') . "\n\n";
        }
        $caption .= "📁 <b>ផ្នែក៖</b> #" . preg_replace('/\s+/', '_', trim($category)) . "\n";
        $caption .= "🔗 <b>អានលម្អិត៖</b> <a href=\"" . htmlspecialchars($articleUrl, ENT_QUOTES, 'UTF-8') . "\">ចុចទីនេះដើម្បីអានបន្ត</a>\n\n";
        $caption .= "#ព័ត៌មានកម្ពុជា #NewsPlatform #Cambodia";

        // If image exists, send sendPhoto, otherwise sendMessage
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $apiUrl = "https://api.telegram.org/bot{$token}/sendPhoto";
            $postFields = [
                'chat_id' => $channelId,
                'photo' => $imageUrl,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ];
        } else {
            $apiUrl = "https://api.telegram.org/bot{$token}/sendMessage";
            $postFields = [
                'chat_id' => $channelId,
                'text' => $caption,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false
            ];
        }

        return self::executeCurl($apiUrl, $postFields);
    }

    private static function executeCurl(string $url, array $fields): array
    {
        if (!function_exists('curl_init')) {
            return ['success' => false, 'message' => 'cURL extension not enabled in PHP'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => $err];
        }

        $data = json_decode($response, true);
        if ($httpCode === 200 && !empty($data['ok'])) {
            return ['success' => true, 'message' => 'Dispatched to Telegram channel successfully'];
        }

        return [
            'success' => false,
            'message' => $data['description'] ?? 'Telegram API error (' . $httpCode . ')'
        ];
    }
}
