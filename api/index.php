<?php

$projectRoot = dirname(__DIR__);
$requestPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$requestPath = str_replace('\\', '/', $requestPath);
$relativePath = ltrim($requestPath, '/');

if ($relativePath === '') {
    $relativePath = 'index.php';
} elseif (str_ends_with($relativePath, '/')) {
    $relativePath .= 'index.php';
}

if (str_starts_with($relativePath, 'api/') || str_contains($relativePath, '..')) {
    http_response_code(404);
    exit('Not found');
}

$candidate = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
if (is_dir($candidate)) {
    $candidate .= DIRECTORY_SEPARATOR . 'index.php';
}

$target = realpath($candidate);
$root = realpath($projectRoot);
if ($target === false || $root === false || !str_starts_with($target, $root . DIRECTORY_SEPARATOR) || pathinfo($target, PATHINFO_EXTENSION) !== 'php') {
    http_response_code(404);
    exit('Not found');
}

chdir(dirname($target));
require $target;

