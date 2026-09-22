<?php
/**
 * CMS Admin Subscribers CSV Export Entrypoint
 * news-platform / admin / export-subscribers.php
 */


require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$controller->exportSubscribersCsv();
?>
