<?php
/**
 * Global Helper Functions & Dynamic URL Resolver
 * news-platform / src / Core / helpers.php
 */

use App\Core\TemplateEngine;
use App\Core\Sanitizer;

require_once __DIR__ . '/Sanitizer.php';

if (!function_exists('e')) {
    /**
     * Escape output string safely for HTML context (XSS Protection)
     */
    function e(?string $value): string
    {
        return TemplateEngine::e($value);
    }
}

if (!function_exists('km_num')) {
    /**
     * Convert ASCII digits (0-9) to Khmer digits (០-៩) when active language is Khmer.
     * Preserves numbers inside HTML tags, attributes (e.g. src="..."), and media shortcodes ([image:N]).
     */
    function km_num($num): string
    {
        $currentLang = $_SESSION['lang'] ?? 'en';
        $str = (string) $num;
        if ($str === '' || ($currentLang !== 'kh' && $currentLang !== 'km')) {
            return $str;
        }

        $digitsKm = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];

        // If string contains HTML tags or media shortcodes, only convert numbers in plain text content
        if (str_contains($str, '<') || str_contains($str, '[')) {
            $pattern = '/(<[^>]+>|\[(?:image|img|video|vid|audio)[:\-][^\]]+\])/iu';
            $tokens = preg_split($pattern, $str, -1, PREG_SPLIT_DELIM_CAPTURE);
            $result = '';
            foreach ($tokens as $token) {
                if ($token === '') continue;
                if (preg_match('/^(<[^>]+>|\[(?:image|img|video|vid|audio)[:\-][^\]]+\])$/iu', $token)) {
                    $result .= $token;
                } else {
                    $result .= strtr($token, $digitsKm);
                }
            }
            return $result;
        }

        return strtr($str, $digitsKm);
    }
}

if (!function_exists('__')) {
    /**
     * Translate UI string key into active language using common.php
     */
    function __(string $key, array $replace = []): string
    {
        static $lang = null;
        if ($lang === null) {
            $lang = require __DIR__ . '/../../languages/common.php';
        }

        $text = $lang[$key] ?? $key;
        $currentLang = $_SESSION['lang'] ?? 'en';

        foreach ($replace as $k => $v) {
            $val = ($currentLang === 'kh' || $currentLang === 'km') && (is_int($v) || is_float($v) || (is_string($v) && is_numeric($v)))
                ? km_num($v)
                : (string) $v;
            $text = str_replace(':' . $k, $val, $text);
        }

        return $text;
    }
}

if (!function_exists('tmpl_name')) {
    /**
     * Template Type Name Language Translation Resolver
     */
    function tmpl_name(?string $type): string
    {
        if (empty($type)) {
            return '';
        }
        $t = strtolower(trim($type));
        $key = 'tmpl_' . $t;
        return __($key);
    }
}

if (!function_exists('get_translation_maps')) {
    /**
     * Centralized Translation Dictionary Map for both PHP and Client JS
     */
    function get_translation_maps(): array
    {
        return [
            'categories' => [
                'Technology & AI' => 'បច្ចេកវិទ្យា & AI',
                'Global Politics' => 'នយោបាយសកល',
                'Climate & Science' => 'បរិស្ថាន & វិទ្យាសាស្ត្រ',
                'Economy & Markets' => 'សេដ្ឋកិច្ច & ទីផ្សារ',
                'Infrastructure' => 'ហេដ្ឋារចនាសម្ព័ន្ធ & ដឹកជញ្ជូន',
                'Infrastructure & Logistics' => 'ហេដ្ឋារចនាសម្ព័ន្ធ & ដឹកជញ្ជូន',
                'Education & Health' => 'អប់រំ & សុខាភិបាល',
                'National News' => 'ព័ត៌មានជាតិ',
                'International' => 'ព័ត៌មានអន្តរជាតិ',
                'Business' => 'អាជីវកម្ម',
                'Sports' => 'កីឡា',
                'Entertainment' => 'កម្សាន្ត',
                'Culture' => 'វប្បធម៌'
            ],
            'titles' => [
                'ការអភិវឌ្ឍប្រព័ន្ធ AI និងសេដ្ឋកិច្ចឌីជីថលនៅកម្ពុជាឆ្នាំ២០២៦' => 'Cambodia Digital Economy and AI Transformation Roadmap 2026',
                'ការស៊ើបអង្កេត៖ ភាពធន់នៃបណ្តាញខ្សែកាបបាតសមុទ្រ និងសន្តិសុខអ៊ីនធឺណិតតំបន់' => 'Investigation: Subsea Fiber Optic Resilience and Regional Cyber Infrastructure',
                'បទវិចារណកថា៖ សុចរិតភាពសារព័ត៌មាន និងការប្រយុទ្ធប្រឆាំងព័ត៌មានមិនពិត' => 'Editorial: Journalistic Integrity and the Strategic Fight Against Misinformation',
                'កំណើនសេដ្ឋកិច្ចកម្ពុជាឆ្នាំ២០២៦៖ ការកើនឡើងនៃការនាំចេញ និងការវិនិយោគបរទេស' => 'Cambodia Economic Growth 2026: Export Surge & Foreign Direct Investment',
                'គម្រោងថាមពលព្រះអាទិត្យ និងថាមពលបៃតងនៅតំបន់ទន្លេមេគង្គ' => 'Mekong Renewable Solar Energy Projects and Clean Grid Infrastructure',
                'កិច្ចប្រជុំកំពូលអាស៊ាន៖ ការពង្រឹងកិច្ចសហប្រតិបត្តិការសន្តិសុខ និងពាណិជ្ជកម្មសេរី' => 'ASEAN Summit: Strategic Regional Security & RCEP Free Trade Expansion',
                'ប្រព័ន្ធទូទាត់បាគង (Bakong FinTech) បន្តពង្រីកការភ្ជាប់ទំនាក់ទំនងហិរញ្ញវត្ថុអន្តរជាតិ' => 'Bakong FinTech System Expands Regional Cross-Border Payment Integration',
                'ការគាំទ្រអាហារូបករណ៍ STEM និងការបណ្តុះបណ្តាលជំនាញបច្ចេកវិទ្យាដល់យុវជន' => 'National STEM Scholarships and Advanced Tech Skills for Cambodian Youth',
                'ការអភិរក្សបេតិកភណ្ឌប្រាសាទអង្គរ និងការអភិវឌ្ឍទេសចរណ៍វប្បធម៌ជានិរន្តរភាព' => 'Angkor Wat Heritage Preservation and Sustainable Cultural Tourism Development',
                'ការប្រែក្លាយប្រព័ន្ធសុខាភិបាលឌីជីថល និងសេវាថែទាំសុខភាពទំនើប' => 'Digital Healthcare Transformation and Modern Telemedicine Infrastructure',
                'គម្រោងពង្រីកកំពង់ផែស្វយ័តព្រះសីហនុ និងការសម្រួលពាណិជ្ជកម្មអន្តរជាតិ' => 'Sihanoukville Autonomous Port Deep-Water Expansion for Global Shipping',
                'បទវិចារណកថា៖ ស្ថាបត្យកម្មទីក្រុងឆ្លាតវៃ និងការរស់នៅប្រកបដោយនិរន្តរភាព' => 'Opinion: Smart City Architecture, Electric Transit & Sustainable Living',
                'ប្រព័ន្ធ AI ស្វ័យតជំនាន់ថ្មីផ្លាស់ប្តូរស្ថាបត្យកម្មសូហ្វវែរសហគ្រាស' => 'Next-Generation Autonomous AI Systems Reshape Enterprise Architecture',
                'ការស៊ើបអង្កេតជម្រៅលើបណ្តាញខ្សែកាបបាតសមុទ្រពិភពលោក' => 'Silent Subsea Cable Revolution: Deep-Dive into Global Fiber Optics',
                'ហេតុអ្វីបានជាវិចារណញាណរបស់មនុស្សនៅតែមានសារៈសំខាន់ក្នុងយុគសម័យស្វ័យប្រវត្តិកម្ម' => 'Why Human Intuition Remains Imperative in an Automated Era'
            ]
        ];
    }
}

if (!function_exists('cat_name')) {
    /**
     * Category Name Language Translation & Formatting Resolver
     * Ensures pure Khmer when Khmer is selected, and pure English when English is selected.
     * Strips leading index numbers (e.g., '5 ') and removes parenthetical translation labels.
     */
    function cat_name(?string $categoryName, ?string $targetLang = null): string
    {
        if (empty($categoryName)) {
            return '';
        }

        $currentLang = $targetLang ?? ($_SESSION['lang'] ?? 'en');

        // 1. Strip leading digits / bullet numbers (e.g. "5 ", "5. ", "05- ")
        $str = preg_replace('/^[\d\.\-\s]+/u', '', trim($categoryName));

        // 2. Mapping dictionaries for exact lookup fallback
        $maps = get_translation_maps();
        $enToKm = $maps['categories'];
        $kmToEn = array_flip($enToKm);

        // 3. Extract parts if format is "KhmerText (EnglishText)" or "EnglishText (KhmerText)"
        if (preg_match('/^([^()]+)\s*\(([^()]+)\)$/u', $str, $matches)) {
            $part1 = trim($matches[1]);
            $part2 = trim($matches[2]);

            $isPart1Khmer = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $part1);
            $isPart2Khmer = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $part2);

            if ($currentLang === 'kh' || $currentLang === 'km') {
                if ($isPart1Khmer)
                    return $part1;
                if ($isPart2Khmer)
                    return $part2;
                return $enToKm[$part1] ?? $enToKm[$part2] ?? $part1;
            } else {
                // English requested
                if (!$isPart1Khmer && !empty($part1))
                    return $part1;
                if (!$isPart2Khmer && !empty($part2))
                    return $part2;
                return $kmToEn[$part1] ?? $kmToEn[$part2] ?? $part2;
            }
        }

        // 4. Single-string without parentheses
        $hasKhmer = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $str);

        if ($currentLang === 'kh' || $currentLang === 'km') {
            if ($hasKhmer) {
                return $str;
            }
            return $enToKm[$str] ?? $str;
        } else {
            // English requested
            if (!$hasKhmer) {
                return $str;
            }
            return $kmToEn[$str] ?? $str;
        }
    }
}

if (!function_exists('url')) {
    /**
     * Dynamic URL generator that automatically detects base subdirectories and deployment environments.
     * Prevents path duplication (/News-platform-1/News-platform-1/...) and resolves relative upload/asset paths safely.
     */
    function url(string $path = ''): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        // Return external URLs, protocol-relative URLs, and data URIs unchanged
        if (preg_match('#^(https?:)?\/\/#i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($scriptName));
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');

        // Detect base folder (e.g. /News-platform-1 or /News-platform-submission)
        $base = '';
        $dirNorm = '/' . ltrim($dir, '/');
        if (str_contains($dirNorm, '/public')) {
            $base = substr($dirNorm, 0, strpos($dirNorm, '/public'));
        } elseif (str_contains($dirNorm, '/admin')) {
            $base = substr($dirNorm, 0, strpos($dirNorm, '/admin'));
        } elseif (str_contains($dirNorm, '/api')) {
            $base = substr($dirNorm, 0, strpos($dirNorm, '/api'));
        } else {
            $base = ($dir === '/' || $dir === '\\') ? '' : $dir;
        }
        $base = rtrim($base, '/');

        // Strip base folder or any project directory prefix if $path already starts with it
        if ($base !== '') {
            $basePattern = '#^' . preg_quote($base, '#') . '(/|$)#i';
            if (preg_match($basePattern, $path)) {
                $path = preg_replace($basePattern, '', $path);
            }
        }
        $path = preg_replace('#^/?(?:News-platform-1|News-platform-submission|News-platform-[a-zA-Z0-9_\-]+)(/|$)#i', '', $path);

        $path = ltrim($path, '/');

        // Detect if server DocumentRoot is set directly to /public or if running from public folder
        $isPublicDocRoot = str_ends_with(rtrim($docRoot, '/'), '/public') 
                        || ($dir === '/' && !file_exists(($docRoot !== '' ? $docRoot : '.') . '/public'));

        if ($isPublicDocRoot) {
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, 7);
            }
        } else {
            if (!str_starts_with($path, 'public/') && !str_starts_with($path, 'admin/')) {
                if (str_starts_with($path, 'assets/') || str_starts_with($path, 'uploads/')) {
                    $path = 'public/' . $path;
                }
            }
        }

        return ($base !== '' ? $base : '') . '/' . $path;
    }
}

if (!function_exists('image_url')) {
    /**
     * Safely resolve image asset URLs whether they are local uploads, relative paths, or external HTTP/HTTPS links.
     */
    function image_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }
        $path = trim($path);
        if (preg_match('#^(https?:)?\/\/#i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }
        return url($path);
    }
}


if (!function_exists('lang_url')) {
    /**
     * Build language switcher URL preserving current page route and query parameters
     */
    function lang_url(string $lang): string
    {
        $params = $_GET;
        $params['lang'] = $lang;
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
        return $uri . '?' . http_build_query($params);
    }
}

if (!function_exists('split_dual_language')) {
    /**
     * Intelligent Bilingual Content Extractor (Khmer & English)
     * Accurately parses dual-language inputs separated by:
     * - Delimiters: ' / ', ' | ', ' - ', '---', '///', '|||', '<hr class="lang-separator">'
     * - Markup: [kh]...[/kh] [en]...[/en], <!-- kh -->...<!-- en -->
     * - Parentheses: "Khmer (English)" or "English (Khmer)"
     * - Brackets: "Khmer [English]" or "English [Khmer]"
     * - Newlines: Double line breaks separating language versions
     *
     * @param string|null $text
     * @return array{kh: string, en: string}
     */
    function split_dual_language(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return ['kh' => '', 'en' => ''];
        }
        $str = trim($text);

        // 1. Explicit BBCode-style markers [kh]...[/kh] and [en]...[/en]
        $hasKhTag = preg_match('/\[kh\](.*?)\[\/kh\]/is', $str, $mKh);
        $hasEnTag = preg_match('/\[en\](.*?)\[\/en\]/is', $str, $mEn);
        if ($hasKhTag || $hasEnTag) {
            return [
                'kh' => $hasKhTag ? trim($mKh[1]) : '',
                'en' => $hasEnTag ? trim($mEn[1]) : ''
            ];
        }

        // 2. HTML comments <!-- kh -->...<!-- en -->
        if (preg_match('/<!--\s*(?:lang:)?kh\s*-->(.*?)<!--\s*(?:lang:)?en\s*-->(.*?)$/is', $str, $m)) {
            return ['kh' => trim($m[1]), 'en' => trim($m[2])];
        }
        if (preg_match('/<!--\s*(?:lang:)?en\s*-->(.*?)<!--\s*(?:lang:)?kh\s*-->(.*?)$/is', $str, $m)) {
            return ['kh' => trim($m[2]), 'en' => trim($m[1])];
        }

        $hasKhmerChar = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $str);
        $hasLatinChar = (bool) preg_match('/[a-zA-Z]/', $str);

        // 3. Delimiters (check from longest to shortest)
        $delimiters = [
            '<hr class="lang-separator">', '<hr class="lang-separator"/>', '<hr class="lang-separator" />',
            "\n---\n", "\n///\n", "\n|||\n", ' --- ', ' /// ', ' ||| ',
            " | ", " / ", " - ", "\r\n\r\n", "\n\n"
        ];

        foreach ($delimiters as $delim) {
            if (str_contains($str, $delim)) {
                $parts = explode($delim, $str, 2);
                $p1 = trim($parts[0]);
                $p2 = trim($parts[1]);

                if ($p1 !== '' && $p2 !== '') {
                    $p1Kh = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $p1);
                    $p2Kh = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $p2);
                    $p1En = (bool) preg_match('/[a-zA-Z]/', $p1);
                    $p2En = (bool) preg_match('/[a-zA-Z]/', $p2);

                    if ($p1Kh && $p2En && !$p1En) {
                        return ['kh' => $p1, 'en' => $p2];
                    }
                    if ($p2Kh && $p1En && !$p2En) {
                        return ['kh' => $p2, 'en' => $p1];
                    }
                    if ($p1Kh && !$p2Kh) {
                        return ['kh' => $p1, 'en' => $p2];
                    }
                    if ($p2Kh && !$p1Kh) {
                        return ['kh' => $p2, 'en' => $p1];
                    }
                }
            }
        }

        // 4. Parentheses: "Part1 (Part2)" or "Part1 [Part2]"
        if (preg_match('/^(.+?)\s*[\(\[](.+?)[\)\]]$/us', $str, $matches)) {
            $p1 = trim($matches[1]);
            $p2 = trim($matches[2]);
            $p1Kh = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $p1);
            $p2Kh = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $p2);
            if ($p1Kh && !$p2Kh) {
                return ['kh' => $p1, 'en' => $p2];
            }
            if (!$p1Kh && $p2Kh) {
                return ['kh' => $p2, 'en' => $p1];
            }
        }

        // 5. Single script without delimiter
        if ($hasKhmerChar && !$hasLatinChar) {
            return ['kh' => $str, 'en' => ''];
        }
        if ($hasLatinChar && !$hasKhmerChar) {
            return ['kh' => '', 'en' => $str];
        }

        return ['kh' => $str, 'en' => $str];
    }
}

if (!function_exists('article_title')) {
    /**
     * Dynamic Article Title Language Translation Resolver
     * Accepts either string or article array.
     */
    function article_title($title, ?string $targetLang = null): string
    {
        $currentLang = $targetLang ?? ($_SESSION['lang'] ?? 'en');
        $isKhmer = ($currentLang === 'kh' || $currentLang === 'km');

        if (is_array($title)) {
            $tKh = trim((string)($title['title_kh'] ?? ''));
            $tEn = trim((string)($title['title_en'] ?? ''));
            $tRaw = trim((string)($title['title'] ?? ''));

            $tEnHasLatin = (bool) preg_match('/[a-zA-Z]/', $tEn);
            $tKhHasKhmer = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $tKh);

            if ($isKhmer) {
                if (!empty($tKh) && $tKhHasKhmer) {
                    $split = split_dual_language($tKh);
                    return km_num($split['kh'] ?: $tKh);
                }
                if (!empty($tRaw)) {
                    $split = split_dual_language($tRaw);
                    if (!empty($split['kh'])) return km_num($split['kh']);
                }
                if (!empty($tKh)) return km_num($tKh);
                if (!empty($tEn)) return $tEn;
                return !empty($tRaw) ? km_num($tRaw) : '';
            } else {
                // English requested
                if (!empty($tEn) && $tEnHasLatin) {
                    $split = split_dual_language($tEn);
                    return $split['en'] ?: $tEn;
                }
                if (!empty($tRaw)) {
                    $split = split_dual_language($tRaw);
                    if (!empty($split['en'])) return $split['en'];
                }
                if (!empty($tKh)) {
                    $split = split_dual_language($tKh);
                    if (!empty($split['en'])) return $split['en'];
                }

                // Check static dictionary mapping fallback
                $maps = get_translation_maps();
                $checkStr = $tKh ?: $tRaw;
                if (!empty($checkStr) && isset($maps['titles'][$checkStr])) {
                    return $maps['titles'][$checkStr];
                }

                // Safe fallback for old data
                return $tEn ?: ($tKh ?: $tRaw);
            }
        }

        // String input
        $str = trim((string)$title);
        if (empty($str)) {
            return '';
        }

        $split = split_dual_language($str);
        if ($isKhmer) {
            if (!empty($split['kh'])) {
                return km_num($split['kh']);
            }
            $maps = get_translation_maps();
            $enToKmTitle = array_flip($maps['titles']);
            return isset($enToKmTitle[$str]) ? km_num($enToKmTitle[$str]) : km_num($str);
        } else {
            if (!empty($split['en'])) {
                return $split['en'];
            }
            $maps = get_translation_maps();
            if (isset($maps['titles'][$str])) {
                return $maps['titles'][$str];
            }
            return $str;
        }
    }
}

if (!function_exists('parse_dual_lang')) {
    /**
     * General Multi-Format Dual-Language Content Extractor
     */
    function parse_dual_lang(?string $text, ?string $targetLang = null): string
    {
        if (empty($text)) {
            return '';
        }

        $currentLang = $targetLang ?? ($_SESSION['lang'] ?? 'en');
        $isKhmerMode = ($currentLang === 'kh' || $currentLang === 'km');
        $str = trim($text);

        $split = split_dual_language($str);
        if ($isKhmerMode) {
            return !empty($split['kh']) ? km_num($split['kh']) : km_num($str);
        } else {
            return !empty($split['en']) ? $split['en'] : $str;
        }
    }
}

if (!function_exists('article_summary')) {
    /**
     * Dynamic Article Summary Language Translation Resolver
     */
    function article_summary($summary, ?string $targetLang = null): string
    {
        $currentLang = $targetLang ?? ($_SESSION['lang'] ?? 'en');
        $isKhmer = ($currentLang === 'kh' || $currentLang === 'km');

        if (is_array($summary)) {
            $sKh = trim((string)($summary['summary_kh'] ?? ''));
            $sEn = trim((string)($summary['summary_en'] ?? ''));
            $sRaw = trim((string)($summary['summary'] ?? ''));

            $sEnHasLatin = (bool) preg_match('/[a-zA-Z]/', $sEn);
            $sKhHasKhmer = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', $sKh);

            if ($isKhmer) {
                if (!empty($sKh) && $sKhHasKhmer) {
                    $split = split_dual_language($sKh);
                    return km_num($split['kh'] ?: $sKh);
                }
                if (!empty($sRaw)) {
                    $split = split_dual_language($sRaw);
                    if (!empty($split['kh'])) return km_num($split['kh']);
                }
                if (!empty($sKh)) return km_num($sKh);
                return !empty($sRaw) ? km_num($sRaw) : (!empty($sEn) ? $sEn : '');
            } else {
                if (!empty($sEn) && $sEnHasLatin) {
                    $split = split_dual_language($sEn);
                    return $split['en'] ?: $sEn;
                }
                if (!empty($sRaw)) {
                    $split = split_dual_language($sRaw);
                    if (!empty($split['en'])) return $split['en'];
                }
                if (!empty($sKh)) {
                    $split = split_dual_language($sKh);
                    if (!empty($split['en'])) return $split['en'];
                }
                return $sEn ?: ($sKh ?: $sRaw);
            }
        }

        $str = (string)$summary;
        return parse_dual_lang($str, $targetLang);
    }
}

if (!function_exists('article_content')) {
    /**
     * Dynamic Article Content Language Translation Resolver
     */
    function article_content($content, ?string $targetLang = null): string
    {
        $currentLang = $targetLang ?? ($_SESSION['lang'] ?? 'en');
        $isKhmer = ($currentLang === 'kh' || $currentLang === 'km');

        if (is_array($content)) {
            $cKh = trim((string)($content['content_kh'] ?? ''));
            $cEn = trim((string)($content['content_en'] ?? ''));
            $cRaw = trim((string)($content['content'] ?? ''));

            $cEnHasLatin = (bool) preg_match('/[a-zA-Z]/', strip_tags($cEn));
            $cKhHasKhmer = (bool) preg_match('/[\x{1780}-\x{17FF}]/u', strip_tags($cKh));

            if ($isKhmer) {
                if (!empty($cKh) && $cKhHasKhmer) {
                    $split = split_dual_language($cKh);
                    return km_num($split['kh'] ?: $cKh);
                }
                if (!empty($cRaw)) {
                    $split = split_dual_language($cRaw);
                    if (!empty($split['kh'])) return km_num($split['kh']);
                }
                if (!empty($cKh)) return km_num($cKh);
                return !empty($cRaw) ? km_num($cRaw) : (!empty($cEn) ? $cEn : '');
            } else {
                if (!empty($cEn) && $cEnHasLatin) {
                    $split = split_dual_language($cEn);
                    return $split['en'] ?: $cEn;
                }
                if (!empty($cRaw)) {
                    $split = split_dual_language($cRaw);
                    if (!empty($split['en'])) return $split['en'];
                }
                if (!empty($cKh)) {
                    $split = split_dual_language($cKh);
                    if (!empty($split['en'])) return $split['en'];
                }
                return $cEn ?: ($cKh ?: $cRaw);
            }
        }

        $str = (string)$content;
        return parse_dual_lang($str, $targetLang);
    }
}

if (!function_exists('cat_desc')) {
    /**
     * Dynamic Category Description Language Translation Resolver
     */
    function cat_desc(?string $description, ?string $targetLang = null): string
    {
        return parse_dual_lang($description, $targetLang);
    }
}