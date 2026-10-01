<?php
/**
 * Public Reader Settings Page
 * news-platform / public / settings.php
 */

require_once __DIR__ . '/../src/Core/bootstrap.php';

use App\Controllers\PublicController;

$controller = new PublicController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->updateReaderProfile($_POST);
} else {
    $controller->showReaderSettings();
}
