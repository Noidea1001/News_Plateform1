<?php
/**
 * CMS Admin Public Reader Users Entrypoint
 * news-platform / admin / readers.php
 */

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$controller->readers();
