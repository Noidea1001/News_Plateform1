<?php
/**
 * HTML Sanitizer (XSS Prevention for Quill WYSIWYG Content)
 * news-platform / src / Core / Sanitizer.php
 */


namespace App\Core;

class Sanitizer
{
    /**
     * Clean HTML content allowing standard formatting tags while stripping script tags, onload/onerror handlers, and dangerous attributes.
     */
    public static function cleanHtml(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // 1. Remove dangerous script tags and inline event handlers (onclick, onerror, onload, etc.)
        $html = preg_replace('#<script[^>]*?>.*?</script>#is', '', $html);
        $html = preg_replace('#<style[^>]*?>.*?</style>#is', '', $html);
        $html = preg_replace('#on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
        $html = preg_replace('#href\s*=\s*["\']?\s*javascript:[^"\'>]*["\']?#i', 'href="#"', $html);

        // 2. Allowed HTML tags for article rendering
        $allowedTags = '<p><br><h1><h2><h3><h4><h5><h6><strong><b><em><i><u><s><del><blockquote><pre><code><ul><ol><li><a><img><iframe><div><span><hr><table><thead><tbody><tr><th><td><figure><figcaption>';

        $sanitized = strip_tags($html, $allowedTags);

        // 3. Ensure iframe embeds only allow trusted domains (YouTube, Spotify, SoundCloud, Vimeo)
        $sanitized = preg_replace_callback('/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>.*?<\/iframe>/i', function ($matches) {
            $src = $matches[1];
            if (preg_match('#^(https?:)?\/\/(www\.)?(youtube\.com|youtube-nocookie\.com|youtu\.be|player\.vimeo\.com|open\.spotify\.com|w\.soundcloud\.com)#i', $src)) {
                return $matches[0];
            }
            return ''; // Strip untrusted iframe sources
        }, $sanitized);

        return $sanitized;
    }

    /**
     * Convert YouTube/Vimeo watch URLs into embed URLs
     */
    public static function formatVideoEmbedUrl(string $url): string
    {
        $url = trim($url);
        // YouTube watch or short URL -> embed URL
        if (preg_match('#(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_\-]+)#i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        // Vimeo watch URL -> embed URL
        if (preg_match('#vimeo\.com\/(?:video\/)?([0-9]+)#i', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }
        return $url;
    }

    /**
     * Helper to parse alignment (left, right, center, full) and caption from shortcode parameter string
     */
    private static function parseMediaParams(?string $paramStr): array
    {
        $align = 'center';
        $caption = null;

        if ($paramStr === null || trim($paramStr) === '') {
            return ['align' => $align, 'caption' => $caption];
        }

        $parts = array_map('trim', explode(':', $paramStr));
        $validAligns = ['left', 'right', 'center', 'full'];

        $textParts = [];
        foreach ($parts as $p) {
            $lower = strtolower($p);
            if (in_array($lower, $validAligns, true)) {
                $align = $lower;
            } else {
                $textParts[] = $p;
            }
        }

        if (!empty($textParts)) {
            $caption = implode(':', $textParts);
        }

        return ['align' => $align, 'caption' => $caption];
    }

    /**
     * Render article content and replace image shortcodes ([image:1], [image:1:left], [image:1:Caption:right]) and video shortcodes ([video:1], [video:1:left])
     */
    public static function parseArticleMedia(?string $html, string|array|null $galleryImages = null, ?string $featuredImage = null, string|array|null $videoEmbedUrls = null): string
    {
        $clean = self::cleanHtml($html);
        if ($clean === '') {
            return '';
        }

        // Process images
        $images = [];
        if (is_array($galleryImages)) {
            $images = array_values(array_filter(array_map('trim', $galleryImages)));
        } elseif (is_string($galleryImages) && trim($galleryImages) !== '') {
            $images = array_values(array_filter(array_map('trim', explode("\n", $galleryImages))));
        }

        // Image shortcodes regex pattern (matching optional wrapping <p>...</p>)
        $imgPattern = '/(?:<p\b[^>]*>\s*)?\[(?:image|img)[:\-]([0-9]+)(?::([^\]]+))?\](?:\s*<\/p>)?/i';

        $parsed = preg_replace_callback($imgPattern, function ($matches) use ($images, $featuredImage) {
            $index = (int) $matches[1];
            $params = self::parseMediaParams($matches[2] ?? null);
            $align = $params['align'];
            $customCaption = $params['caption'];

            $imgUrl = null;
            if (isset($images[$index - 1]) && !empty($images[$index - 1])) {
                $imgUrl = $images[$index - 1];
            } elseif ($index === 1 && !empty($featuredImage)) {
                $imgUrl = $featuredImage;
            }

            if (!$imgUrl) {
                return '<div class="alert alert-warning text-xs py-1 px-2.5 my-3 d-inline-block rounded-2 border"><i class="bi bi-exclamation-circle me-1"></i>[Image #' . $index . ' not found]</div>';
            }

            $isKh = (($_SESSION['lang'] ?? 'en') === 'kh' || ($_SESSION['lang'] ?? 'en') === 'km');
            $defaultCaption = $isKh ? ('រូបភាពទី ' . km_num($index)) : ('Image #' . $index);
            $captionText = $customCaption ?: $defaultCaption;
            $badgeLabel = $isKh ? 'រូបភាព' : 'Photo';

            $safeUrl = htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8');
            $safeCaption = htmlspecialchars($captionText, ENT_QUOTES, 'UTF-8');
            $alignClass = 'align-' . $align;

            return '
            <figure class="article-content-embedded-image ' . $alignClass . '">
                <div class="embedded-img-wrapper position-relative">
                    <a href="' . $safeUrl . '" target="_blank" rel="noopener" class="d-block text-decoration-none media-img-zoom rounded-4 overflow-hidden shadow-sm border">
                        <img src="' . $safeUrl . '" alt="' . $safeCaption . '" class="img-fluid w-100 h-auto object-fit-cover d-block" loading="lazy">
                    </a>
                </div>
                <figcaption class="figure-caption text-muted mt-2 text-center small fw-semibold">
                    <span class="badge bg-danger text-white px-2 py-0.5 text-2xs text-uppercase me-1 fw-bold rounded-1" style="font-size: 0.65rem;">
                        <i class="bi bi-camera-fill me-1"></i>' . $badgeLabel . '
                    </span>
                    <span>' . $safeCaption . '</span>
                </figcaption>
            </figure>
            ';
        }, $clean);

        // Process videos
        $videos = [];
        if (is_array($videoEmbedUrls)) {
            $videos = array_values(array_filter(array_map('trim', $videoEmbedUrls)));
        } elseif (is_string($videoEmbedUrls) && trim($videoEmbedUrls) !== '') {
            $videos = array_values(array_filter(array_map('trim', explode("\n", $videoEmbedUrls))));
        }

        // Video shortcodes regex pattern (matching optional wrapping <p>...</p>)
        $videoPattern = '/(?:<p\b[^>]*>\s*)?\[(?:video|vid)[:\-]([0-9]+|https?:\/\/[^\s\]]+)(?::([^\]]+))?\](?:\s*<\/p>)?/i';

        $parsed = preg_replace_callback($videoPattern, function ($matches) use ($videos) {
            $target = trim($matches[1]);
            $params = self::parseMediaParams($matches[2] ?? null);
            $align = $params['align'];
            $customCaption = $params['caption'];

            $videoUrl = null;
            if (is_numeric($target)) {
                $index = (int) $target;
                if (isset($videos[$index - 1]) && !empty($videos[$index - 1])) {
                    $videoUrl = $videos[$index - 1];
                }
            } else {
                $videoUrl = $target;
            }

            if (!$videoUrl) {
                return '<div class="alert alert-warning text-xs py-1 px-2.5 my-3 d-inline-block rounded-2 border"><i class="bi bi-play-btn me-1"></i>[Video #' . $target . ' not found]</div>';
            }

            $embedUrl = self::formatVideoEmbedUrl($videoUrl);
            $isKh = (($_SESSION['lang'] ?? 'en') === 'kh' || ($_SESSION['lang'] ?? 'en') === 'km');
            $numStr = is_numeric($target) ? (' #' . ($isKh ? km_num($target) : $target)) : '';
            $defaultCaption = $isKh ? ('វីដេអូរាយការណ៍' . $numStr) : ('Video Report' . $numStr);
            $captionText = $customCaption ?: $defaultCaption;
            $badgeLabel = $isKh ? 'វីដេអូ' : 'Video';

            $safeUrl = htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8');
            $safeCaption = htmlspecialchars($captionText, ENT_QUOTES, 'UTF-8');
            $alignClass = 'align-' . $align;

            return '
            <div class="article-content-embedded-video ' . $alignClass . '">
                <div class="video-card-wrapper shadow-sm rounded-4 overflow-hidden border bg-black position-relative">
                    <div class="video-container" style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">
                        <iframe src="' . $safeUrl . '" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" loading="lazy" style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"></iframe>
                    </div>
                </div>
                <div class="figure-caption text-muted mt-2 text-center small fw-semibold">
                    <span class="badge bg-danger text-white px-2 py-0.5 text-2xs text-uppercase me-1 fw-bold rounded-1" style="font-size: 0.65rem;">
                        <i class="bi bi-play-circle-fill me-1"></i>' . $badgeLabel . '
                    </span>
                    <span>' . $safeCaption . '</span>
                </div>
            </div>
            ';
        }, $parsed);

        // Final cleanup: unwrap any stray <p> tags enclosing embedded figures or videos
        $parsed = preg_replace('#<p>\s*(<(?:figure|div)\s+class="article-content-embedded-[^"]*">.*?<\/(?:figure|div)>)\s*<\/p>#is', '$1', $parsed);

        return $parsed;
    }

    /**
     * Check if article body content already contains video shortcodes or embedded video iframe
     */
    public static function hasVideoInContent(?string $content, ?string $videoEmbedUrl = null): bool
    {
        if ($content === null || trim($content) === '') {
            return false;
        }

        // Check for video shortcodes: [video:1], [vid:1], [video:https://...]
        if (preg_match('/\[(?:video|vid)[:\-]/i', $content)) {
            return true;
        }

        // Check for embedded iframe tags in content
        if (str_contains($content, '<iframe')) {
            return true;
        }

        // Check if video URL filename or ID is directly in content
        if (!empty($videoEmbedUrl)) {
            $videoFilename = basename(trim($videoEmbedUrl));
            if (!empty($videoFilename) && str_contains($content, $videoFilename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter out gallery images that are already referenced via [image:N] shortcodes or embedded in article body content
     */
    public static function getUnusedGalleryImages(string|array|null $galleryImages, ?string $content): array
    {
        if (empty($galleryImages)) {
            return [];
        }

        $urls = is_array($galleryImages)
            ? array_values(array_filter(array_map('trim', $galleryImages)))
            : array_values(array_filter(array_map('trim', explode("\n", (string) $galleryImages))));

        if (empty($urls) || $content === null || trim($content) === '') {
            return $urls;
        }

        // Collect 1-based index numbers referenced in shortcodes: [image:1], [img:2], etc.
        $usedIndices = [];
        if (preg_match_all('/\[(?:image|img)[:\-]([0-9]+)(?::[^\]]+)?\]/i', $content, $matches)) {
            foreach ($matches[1] as $idxStr) {
                $usedIndices[(int) $idxStr] = true;
            }
        }

        $unused = [];
        foreach ($urls as $i => $url) {
            $oneBasedIndex = $i + 1;
            // Skip if index referenced via shortcode
            if (isset($usedIndices[$oneBasedIndex])) {
                continue;
            }
            // Skip if exact image URL/filename is directly inside content HTML
            $filename = basename($url);
            if (!empty($filename) && str_contains($content, $filename)) {
                continue;
            }
            $unused[] = $url;
        }

        return $unused;
    }

    /**
     * Backward-compatible alias for parseArticleMedia
     */
    public static function parseArticleImages(?string $html, string|array|null $galleryImages = null, ?string $featuredImage = null, string|array|null $videoEmbedUrls = null): string
    {
        return self::parseArticleMedia($html, $galleryImages, $featuredImage, $videoEmbedUrls);
    }
}