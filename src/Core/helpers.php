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
     * Convert digits to Khmer digits if active language is Khmer
     */
    function km_num($num): string
    {
        $currentLang = $_SESSION['lang'] ?? 'en';
        $str = (string) $num;
        if ($currentLang === 'kh' || $currentLang === 'km') {
            $digitsKm = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];
            return strtr($str, $digitsKm);
        }
        return $str;
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

if (!function_exists('cat_name')) {
    /**
     * Category Name Language Translation & Formatting Resolver
     * Ensures pure Khmer when Khmer is selected, and pure English when English is selected.
     * Strips leading index numbers (e.g., '5 ') and removes parenthetical translation labels.
     */
    function cat_name(?string $categoryName): string
    {
        if (empty($categoryName)) {
            return '';
        }

        $currentLang = $_SESSION['lang'] ?? 'en';

        // 1. Strip leading digits / bullet numbers (e.g. "5 ", "5. ", "05- ")
        $str = preg_replace('/^[\d\.\-\s]+/u', '', trim($categoryName));

        // 2. Mapping dictionaries for exact lookup fallback
        $enToKm = [
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
            'Culture' => 'វប្បធម៌',
        ];

        $kmToEn = [
            'បច្ចេកវិទ្យា & AI' => 'Technology & AI',
            'នយោបាយសកល' => 'Global Politics',
            'បរិស្ថាន & វិទ្យាសាស្ត្រ' => 'Climate & Science',
            'សេដ្ឋកិច្ច & ទីផ្សារ' => 'Economy & Markets',
            'ហេដ្ឋារចនាសម្ព័ន្ធ & ដឹកជញ្ជូន' => 'Infrastructure',
            'អប់រំ & សុខាភិបាល' => 'Education & Health',
            'ព័ត៌មានជាតិ' => 'National News',
            'ព័ត៌មានអន្តរជាតិ' => 'International',
            'អាជីវកម្ម' => 'Business',
            'កីឡា' => 'Sports',
            'កម្សាន្ត' => 'Entertainment',
            'វប្បធម៌' => 'Culture',
        ];

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
     * Dynamic URL generator that automatically detects base subdirectories (e.g. /News-platefrom/)
     */
    function url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = dirname($scriptName);

        $base = '';
        if (str_contains($dir, '/public')) {
            $base = substr($dir, 0, strpos($dir, '/public'));
        } elseif (str_contains($dir, '/admin')) {
            $base = substr($dir, 0, strpos($dir, '/admin'));
        } else {
            $base = ($dir === '/' || $dir === '\\') ? '' : $dir;
        }

        $base = rtrim(str_replace('\\', '/', $base), '/');

        return ($base !== '' ? $base : '') . '/' . $path;
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

if (!function_exists('article_title')) {
    /**
     * Dynamic Article Title Language Translation Resolver
     * Translates article titles between Khmer and English based on current session language.
     */
    function article_title(?string $title): string
    {
        if (empty($title)) return '';

        $currentLang = $_SESSION['lang'] ?? 'en';
        $title = trim($title);

        static $kmToEnTitle = [
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

            // schema.sql titles
            'ប្រព័ន្ធ AI ស្វ័យតជំនាន់ថ្មីផ្លាស់ប្តូរស្ថាបត្យកម្មសូហ្វវែរសហគ្រាស' => 'Next-Generation Autonomous AI Systems Reshape Enterprise Architecture',
            'ការស៊ើបអង្កេតជម្រៅលើបណ្តាញខ្សែកាបបាតសមុទ្រពិភពលោក' => 'Silent Subsea Cable Revolution: Deep-Dive into Global Fiber Optics',
            'ហេតុអ្វីបានជាវិចារណញាណរបស់មនុស្សនៅតែមានសារៈសំខាន់ក្នុងយុគសម័យស្វ័យប្រវត្តិកម្ម' => 'Why Human Intuition Remains Imperative in an Automated Era',
        ];

        static $enToKmTitle = null;
        if ($enToKmTitle === null) {
            $enToKmTitle = array_flip($kmToEnTitle);
        }

        $hasKhmer = (bool)preg_match('/[\x{1780}-\x{17FF}]/u', $title);

        if ($currentLang === 'kh' || $currentLang === 'km') {
            if ($hasKhmer) {
                return km_num($title);
            }
            return $enToKmTitle[$title] ?? $title;
        } else {
            // English mode
            if (!$hasKhmer) {
                return $title;
            }
            return $kmToEnTitle[$title] ?? $title;
        }
    }
}
?>



