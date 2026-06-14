<?php
class Auth extends Controller {
    public function index() {
        // Thiết lập tiêu đề trang cho Layout
        $data['page_title'] = "Đăng nhập - TaskSync";

        if (isset($_SESSION['user'])) {
            redirect('workspace');
        }
        
        // Nếu method là POST, xử lý login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Tài khoản demo mặc định
            $demoUser = [
                'username' => 'admin',
                'password' => 'admin123',
                'role' => 'admin',
                'display_name' => 'Quyen Gia'
            ];

            if (strcasecmp($username, $demoUser['username']) === 0 && in_array($password, [$demoUser['password'], 'admin'], true)) {
                $_SESSION['user'] = [
                    'username' => $demoUser['username'],
                    'role' => $demoUser['role'],
                    'display_name' => $demoUser['display_name'],
                ];
                redirect('workspace');
            }

            $data['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
        
        // Khi đang ở trang đăng nhập, không dùng layout chung có sidebar/header
        $this->view('pages/auth/login', $data, false);
    }

    public function logout() {
        session_unset();
        session_destroy();
        redirect('auth');
    }
}
