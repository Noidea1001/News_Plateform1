<?php
/**
 * Application Core Bootstrap
 * news-platform / src / Core / bootstrap.php
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/TemplateEngine.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/../Controllers/PublicController.php';
require_once __DIR__ . '/../Controllers/AdminController.php';
