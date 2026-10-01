<?php
/**
 * Delete Public Reader User Action Handler
 * news-platform / admin / actions / delete-reader.php
 */

require_once __DIR__ . '/../../src/Core/bootstrap.php';

use App\Controllers\AdminController;

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$token = $_GET['csrf_token'] ?? ($_POST['csrf_token'] ?? '');

try {
    $controller = new AdminController();
    $controller->deleteReader($id, $token);
} catch (Throwable $e) {
    header('Location: ' . url('admin/readers.php?error=' . urlencode('Delete Error: ' . $e->getMessage())));
    exit;
}
