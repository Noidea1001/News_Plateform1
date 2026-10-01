<?php
/**
 * Public Reader News Archive ("បណ្ណសារព័ត៌មាន")
 * news-platform / public / archive.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';

use App\Controllers\PublicController;

$controller = new PublicController();
$controller->archive();
