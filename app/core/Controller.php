<?php
//Lớp cha giúp các controller con gọi model và view
class Controller
{
    // Hàm gọi Model
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    // Hàm gọi View (Tự động bọc giao diện vào layout.php)
    public function view($view, $data = [], $useLayout = true)
    {
        // Tự động nạp kết nối Database để lấy cấu hình toàn cục
        $dbInstance = new Database();
        $pdo = $dbInstance->pdo;

        // Tự động lấy Cấu hình hệ thống lưu vào biến $systemSettings
        $systemSettings = [];
        try {
            $stmt = $pdo->query("SELECT * FROM system_settings");
            if ($stmt) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $systemSettings[$row['key']] = $row['value'];
                }
            }
        } catch (Exception $e) {
            // Dự phòng mặc định nếu chưa khởi tạo bảng trong phpMyAdmin
            $systemSettings = [
                'system_name' => 'TaskSync',
                'allow_registration' => 'on',
                'maintenance_mode' => 'off'
            ];
        }

        // Tự động lấy số lượng Lời mời chờ duyệt của User đang đăng nhập lưu vào biến $pendingInvites
        $pendingInvites = [];
        if (isset($_SESSION['user']['id'])) {
            require_once __DIR__ . '/../models/ProjectModel.php';
            $projModel = new ProjectModel();
            $pendingInvites = $projModel->getPendingInvitations($_SESSION['user']['id']);
        }

        // Biến $view và $data sẽ được layout sử dụng
        if ($useLayout) {
            require_once __DIR__ . '/../views/layout.php';
        } else {
            require_once __DIR__ . '/../views/' . $view . '.php';
        }
    }
}
