<?php
/**
 * CMS Admin AJAX Image Upload Endpoint
 * news-platform / admin / actions / upload-image.php
 */

require_once __DIR__ . '/../../src/Core/helpers.php';
require_once __DIR__ . '/../../src/Core/Database.php';
require_once __DIR__ . '/../../src/Core/Auth.php';
require_once __DIR__ . '/../../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$controller->uploadImageAjax();
