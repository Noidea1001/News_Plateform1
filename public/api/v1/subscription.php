<?php
/**
 * Real-Time Reader Topic Subscriptions REST API v1
 * news-platform / public / api / v1 / subscription.php
 */

require_once __DIR__ . '/../../../src/Core/bootstrap.php';

use App\Controllers\PublicController;

$controller = new PublicController();
$controller->subscriptionApi();
