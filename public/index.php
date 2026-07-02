<?php

// Thử nạp file config.php trước để lấy cấu hình BASE_URL động
if (file_exists(__DIR__ . '/../app/core/config.php')) {
    require_once __DIR__ . '/../app/core/config.php';
}

// Giá trị mặc định cho BASE_URL nếu máy không sử dụng file config.php cục bộ
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8081/TaskSync/public');
}

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

try {
    $dbObj = new Database();
    $stmt = $dbObj->pdo->query("SELECT `value` FROM system_settings WHERE `key` = 'maintenance_mode' LIMIT 1");
    $maintenanceMode = $stmt ? $stmt->fetchColumn() : 'off';

    // Nếu đang bảo trì và tài khoản đăng nhập KHÔNG PHẢI là Admin -> Chặn
    if ($maintenanceMode === 'on' && isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'admin') {
        unset($_SESSION['user']); // Hủy phiên hoạt động
        $_SESSION['flash_error'] = "Hệ thống đang bảo trì nâng cấp định kỳ. Vui lòng quay lại sau!";
        
        // Đẩy về trang đăng nhập
        header('Location: ' . BASE_URL . '/auth');
        exit();
    }
} catch (Exception $e) {
    // Bỏ qua nếu CSDL chưa nạp cấu hình
}

// Bật công tắc chạy hệ thống
$app = new App();