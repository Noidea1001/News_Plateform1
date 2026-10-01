<?php
/**
 * Public Reader Logout
 * news-platform / public / logout.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';

use App\Controllers\PublicController;

$controller = new PublicController();
$controller->logoutReader();
