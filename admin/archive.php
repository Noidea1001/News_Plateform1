<?php
/**
 * Admin News Archive Management Entry Point
 * news-platform / admin / archive.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$controller->archive();
