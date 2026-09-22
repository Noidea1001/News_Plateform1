<?php
/**
 * Template Engine View Renderer (Safe Extraction & XSS Escaping)
 * news-platform / src / Core / TemplateEngine.php
 */

namespace App\Core;

use Exception;

class TemplateEngine
{
    private string $templatePath;

    public function __construct(string $templatePath = __DIR__ . '/../../templates')
    {
        $this->templatePath = rtrim($templatePath, '/\\');
    }

    /**
     * Escape output string safely for HTML context (XSS Prevention)
     */
    public static function e(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Human-friendly time ago formatter (supports Khmer & English)
     */
    public static function timeAgo(?string $datetime): string
    {
        if (!$datetime) {
            return '';
        }
        $timestamp = strtotime($datetime);
        if (!$timestamp) {
            return '';
        }

        $currentLang = $_SESSION['lang'] ?? 'en';
        $isKhmer = ($currentLang === 'kh' || $currentLang === 'km');
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return $isKhmer ? 'ទើបតែឥឡូវ' : 'Just now';
        }

        $digitsKm = ['0'=>'០','1'=>'១','2'=>'២','3'=>'៣','4'=>'៤','5'=>'៥','6'=>'៦','7'=>'៧','8'=>'៨','9'=>'៩'];

        if ($diff < 3600) {
            $mins = floor($diff / 60);
            if ($isKhmer) {
                return strtr((string)$mins, $digitsKm) . ' នាទីមុន';
            }
            return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
        }
        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            if ($isKhmer) {
                return strtr((string)$hours, $digitsKm) . ' ម៉ោងមុន';
            }
            return $hours . ' hr' . ($hours > 1 ? 's' : '') . ' ago';
        }
        if ($diff < 2592000) {
            $days = floor($diff / 86400);
            if ($isKhmer) {
                return strtr((string)$days, $digitsKm) . ' ថ្ងៃមុន';
            }
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }

        return self::formatDate($datetime, 'M j, Y');
    }

    /**
     * Format date string with automatic Khmer translation when Khmer language is selected
     */
    public static function formatDate(?string $datetime, string $format = 'F j, Y'): string
    {
        if (!$datetime) {
            return '';
        }
        $timestamp = strtotime($datetime);
        if (!$timestamp) {
            return '';
        }

        $formatted = date($format, $timestamp);
        $currentLang = $_SESSION['lang'] ?? 'en';

        if ($currentLang === 'kh' || $currentLang === 'km') {
            $daysMap = [
                'Monday' => 'ថ្ងៃច័ន្ទ', 'Tuesday' => 'ថ្ងៃអង្គារ', 'Wednesday' => 'ថ្ងៃពុធ',
                'Thursday' => 'ថ្ងៃព្រហស្បតិ៍', 'Friday' => 'ថ្ងៃសុក្រ', 'Saturday' => 'ថ្ងៃសៅរ៍', 'Sunday' => 'ថ្ងៃអាទិត្យ',
                'Mon' => 'ច័ន្ទ', 'Tue' => 'អង្គារ', 'Wed' => 'ពុធ', 'Thu' => 'ព្រហស្បតិ៍', 'Fri' => 'សុក្រ', 'Sat' => 'សៅរ៍', 'Sun' => 'អាទិត្យ'
            ];
            $monthsMap = [
                'January' => 'មករា', 'February' => 'កុម្ភៈ', 'March' => 'មីនា', 'April' => 'មេសា',
                'May' => 'ឧសភា', 'June' => 'មិថុនា', 'July' => 'កក្កដា', 'August' => 'សីហា',
                'September' => 'កញ្ញា', 'October' => 'តុលា', 'November' => 'វិច្ឆិកា', 'December' => 'ធ្នូ',
                'Jan' => 'មករា', 'Feb' => 'កុម្ភៈ', 'Mar' => 'មីនា', 'Apr' => 'មេសា',
                'Jun' => 'មិថុនា', 'Jul' => 'កក្កដា', 'Aug' => 'សីហា', 'Sep' => 'កញ្ញា',
                'Oct' => 'តុលា', 'Nov' => 'វិច្ឆិកា', 'Dec' => 'ធ្នូ'
            ];

            $digitsKm = ['0'=>'០','1'=>'១','2'=>'២','3'=>'៣','4'=>'៤','5'=>'៥','6'=>'៦','7'=>'៧','8'=>'៨','9'=>'៩'];

            // Replace Day Names
            foreach ($daysMap as $enD => $kmD) {
                if (str_contains($formatted, $enD)) {
                    $formatted = str_replace($enD, $kmD, $formatted);
                    break;
                }
            }

            // Replace Month Names
            foreach ($monthsMap as $enM => $kmM) {
                if (str_contains($formatted, $enM)) {
                    $formatted = str_replace($enM, $kmM, $formatted);
                    break;
                }
            }

            // Convert digits to Khmer digits
            $formatted = strtr($formatted, $digitsKm);
        }

        return $formatted;
    }

    /**
     * Render a partial view template and return buffered HTML
     */ 
    public function renderPartial(string $viewName, array $data = []): string
    {
        $file = $this->templatePath . '/' . ltrim($viewName, '/\\');
        if (!file_exists($file)) {
            // Append .php if missing
            if (file_exists($file . '.php')) {
                $file .= '.php';
            } else {
                throw new Exception("Template view file not found: {$file}");
            }
        }

        // Extract variables into scope safely
        extract($data, EXTR_SKIP);

        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Render full page layout wrapping view
     */
    public function renderPage(string $viewName, array $data = [], string $layoutType = 'public'): void
    {
        $headerFile = $layoutType === 'admin' 
            ? 'layouts/header-admin.php' 
            : 'layouts/header-public.php';
        
        $footerFile = 'layouts/footer.php';

        $data['engine'] = $this;

        // Render header
        echo $this->renderPartial($headerFile, $data);

        // Render target body view
        echo $this->renderPartial($viewName, $data);

        // Render footer
        echo $this->renderPartial($footerFile, $data);
    }

    /**
     * Render specific article layout dynamic blueprint
     */
    public function renderArticleView(string $templateType, array $data): void
    {
        $validTemplates = ['standard', 'investigative', 'opinion'];
        if (!in_array($templateType, $validTemplates, true)) {
            $templateType = 'standard';
        }

        $viewName = "views/article-{$templateType}.php";
        $this->renderPage($viewName, $data, 'public');
    }
}

// Global XSS helper function alias for clean view syntax
if (!function_exists('e')) {
    function e(?string $value): string {
        return TemplateEngine::e($value);
    }
}
?>
