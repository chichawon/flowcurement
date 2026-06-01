<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = '/flowcurement';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

if ($requestUri === $basePath) {
    $_SERVER['REQUEST_URI'] = '/';
} elseif (str_starts_with($requestUri, $basePath.'/')) {
    $_SERVER['REQUEST_URI'] = substr($requestUri, strlen($basePath));
}

if (isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = $basePath.'/index.php';
}

if (isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = $basePath.'/index.php';
}

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());