<?php
/**
 * ============================================================================
 * Multi-Language Configuration & Switcher (Common Loader)
 * File: languages/common.php
 * Description: Manages user language selection (English & Khmer), handles session
 *              and cookie storage, and loads the corresponding language array file.
 * ============================================================================
 */

// Step 1: Start PHP session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Step 2: Check if user switched language via URL parameter (e.g. ?lang=kh or ?lang=en)
if (isset($_GET['lang'])) {
    $user_lang = strtolower(trim($_GET['lang']));
    // Support both 'kh' and 'km' for Khmer, and 'en' for English
    if (in_array($user_lang, ['en', 'kh', 'km'], true)) {
        $_SESSION['lang'] = ($user_lang === 'km' || $user_lang === 'kh') ? 'kh' : 'en';
        setcookie('app_lang', $_SESSION['lang'], time() + (86400 * 30), '/');
    }
}

// Step 3: Default to English if no language session or cookie exists
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $_COOKIE['app_lang'] ?? 'en';
}

$currentLanguage = $_SESSION['lang'];

// Step 4: Select appropriate language dictionary file
switch ($currentLanguage) {
    case 'kh':
    case 'km':
        $lang_file = 'lang_kh.php';
        break;

    case 'en':
    default:
        $lang_file = 'lang_en.php';
        break;
}

// Step 5: Load translation dictionary array with English fallback
$enBase = file_exists(__DIR__ . '/lang_en.php') ? include __DIR__ . '/lang_en.php' : [];

if ($currentLanguage === 'kh' || $currentLanguage === 'km') {
    $khFilePath = __DIR__ . '/lang_kh.php';
    $khLang = file_exists($khFilePath) ? include $khFilePath : [];
    $lang = array_merge($enBase, $khLang);
} else {
    $lang = $enBase;
}

return $lang;
