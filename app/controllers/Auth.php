<?php
class Auth extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = $this->model('UserModel');
            $user = $userModel->getByUsername($username);

            // Kiểm tra mật khẩu (hàm password_verify so khớp chuỗi với mã hash)
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                // Đăng nhập xong nhảy về trang chủ
                header('Location: /TaskSync/public/home');
                exit;
            } else {
                $data['error'] = "Sai tài khoản hoặc mật khẩu!";
            }
        }
        
        // Hiện trang login (không nạp vào layout chung vì trang login thường rỗng)
        require_once '../app/views/pages/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /TaskSync/public/auth/login');
    }
}