<?php
session_start();

function redirect($path) {
    $path = '/' . ltrim($path, '/');
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $target = ($base === '/' ? '' : $base) . $path;

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';

    if ($currentPath === $target) {
        return;
    }

    header('Location: ' . $target);
    exit;
}

// Nạp các file lõi của hệ thống
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';

// Bật công tắc chạy hệ thống
$app = new App();