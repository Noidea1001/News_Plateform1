<?php
/**
 * Dynamic Progressive Web App (PWA) Manifest Resolver
 * news-platform / public / manifest.php
 */
header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

require_once __DIR__ . '/../src/Core/helpers.php';

$appUrl = url('index.php');
$dir = str_replace('\\', '/', dirname($appUrl));
$scopeUrl = ($dir === '/' || $dir === '.' || $dir === '\\') ? '/' : rtrim($dir, '/') . '/';

$manifest = [
    'name' => 'NewsPlatform - Independent Journalism',
    'short_name' => 'NewsPlatform',
    'description' => 'Production-grade digital newsroom delivering real-time breaking news, investigative reporting, and opinions.',
    'id' => $appUrl,
    'start_url' => $appUrl,
    'scope' => $scopeUrl,
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#c8102e',
    'orientation' => 'portrait-primary',
    'categories' => ['news', 'magazines'],
    'icons' => [
        [
            'src' => url('assets/icons/icon-192.png'),
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => url('assets/icons/icon-maskable-192.png'),
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'maskable'
        ],
        [
            'src' => url('assets/icons/icon-512.png'),
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => url('assets/icons/icon-maskable-512.png'),
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'maskable'
        ],
        [
            'src' => url('assets/icons/icon-192.svg'),
            'sizes' => '192x192',
            'type' => 'image/svg+xml',
            'purpose' => 'any'
        ],
        [
            'src' => url('assets/icons/icon-512.svg'),
            'sizes' => '512x512',
            'type' => 'image/svg+xml',
            'purpose' => 'any'
        ]
    ]
];

echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;
