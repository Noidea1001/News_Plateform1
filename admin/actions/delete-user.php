<?php
/**
 * Delete Staff User Action Handler
 * news-platform / admin / actions / delete-user.php
 */

require_once __DIR__ . '/../../src/Core/bootstrap.php';

use App\Controllers\AdminController;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['csrf_token'] ?? '';

try {
    $controller = new AdminController();
    $controller->deleteUser($id, $token);
} catch (Throwable $e) {
    header('Location: ' . url('admin/users.php?error=' . urlencode('Delete Error: ' . $e->getMessage())));
    exit;
}
