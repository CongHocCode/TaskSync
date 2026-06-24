<?php
// <<<<<<< HEAD
// // NOTE CHO THÀNH: Bỏ comment dòng này và comment dòng dưới để chạy
define('BASE_URL', 'http://localhost:8081/TaskSync/public');
// // define('BASE_URL', 'http://localhost/TaskSync/public');
// =======

// Thử nạp file config.php trước để lấy cấu hình BASE_URL động
if (file_exists(__DIR__ . '/../app/core/config.php')) {
    require_once __DIR__ . '/../app/core/config.php';
}

// Giá trị mặc định for BASE_URL nếu máy không sử dụng file config.php cục bộ
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8081/TaskSync/public');
}

// >>>>>>> main
session_start();

function redirect($path)
{
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