<?php
/**
 * Database Configuration Singleton Parameters
 * news-platform / config / database.php
 */

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
];

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';

if (str_contains($host, '.aivencloud.com') || (isset($_ENV['DB_SSL_VERIFY']) && $_ENV['DB_SSL_VERIFY'] === 'false')) {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}

return [
    'host' => $host,
    'port' => (int)($_ENV['DB_PORT'] ?? 3306),
    'dbname' => $_ENV['DB_NAME'] ?? 'news_platform',
    'username' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASS'] ?? '',
    'charset' => 'utf8mb4',
    'options' => $options,
];
