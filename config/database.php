<?php
/**
 * Database Configuration Singleton Parameters
 * news-platform / config / database.php
 */

$getEnvVar = function (string $key, string $default = ''): string {
    $val = getenv($key);
    if ($val !== false && $val !== '') {
        return $val;
    }
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    return $default;
};

$host = $getEnvVar('DB_HOST', '127.0.0.1');
$port = (int) $getEnvVar('DB_PORT', '3306');
$dbname = $getEnvVar('DB_NAME', 'news_platform');
$username = $getEnvVar('DB_USER', 'root');
$password = $getEnvVar('DB_PASS', '');

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
];

if (str_contains($host, '.aivencloud.com') || str_contains($host, 'render.com') || strtolower($getEnvVar('DB_SSL_VERIFY', '')) === 'false') {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}

return [
    'host' => $host,
    'port' => $port,
    'dbname' => $dbname,
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8mb4',
    'options' => $options,
];
