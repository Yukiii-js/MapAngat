<?php
$publicDir = __DIR__ . DIRECTORY_SEPARATOR . 'public';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($uri === '' || $uri === '/') {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $publicDir . DIRECTORY_SEPARATOR . 'index.php';
    require $publicDir . DIRECTORY_SEPARATOR . 'index.php';
    return;
}

$target = $publicDir . str_replace('/', DIRECTORY_SEPARATOR, $uri);
if (is_file($target)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicDir . DIRECTORY_SEPARATOR . 'index.php';
$_SERVER['REQUEST_URI'] = $uri;
require $publicDir . DIRECTORY_SEPARATOR . 'index.php';
