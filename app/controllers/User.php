<?php

class User extends Controller
{
    private $userModel;

    public function __construct()
    {
        // Yêu cầu đăng nhập mới được truy cập các tính năng này
        if (!isset($_SESSION['user'])) {
            redirect('auth/login');
            exit();
        }

        // Khởi tạo UserModel
        $this->userModel = $this->model('UserModel');
    }

   
    // Hiển thị trang Hồ sơ cá nhân (Profile)
    public function profile()
    {
        $userId = $_SESSION['user']['id'];

        // Lấy thông tin mới nhất và đầy đủ của User từ Database
        $user = $this->userModel->getById($userId);

        if (!$user) {
            redirect('auth/login');
            exit();
        }

        $this->view('pages/workspace/profile', [
            'page_title' => 'Cài đặt tài khoản - TaskSync',
            'user'       => $user
        ]);
    }

    // Xử lý cập nhật thông tin cá nhân (Họ tên, Email)
    public function updateInfo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];

            // Lấy thông tin gốc từ DB để bảo toàn Username và Role
            $currentUser = $this->userModel->getById($userId);

            if ($currentUser) {
                $data = [
                    'username'   => $currentUser['username'],  // Giữ nguyên từ DB
                    'role'       => $currentUser['role'],      // BẢO MẬT: Giữ nguyên quyền từ DB
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'last_name'  => trim($_POST['last_name'] ?? ''),
                    'email'      => trim($_POST['email'] ?? '')
                ];

                // Thực hiện cập nhật vào CSDL
                $success = $this->userModel->update($userId, $data);

                if ($success) {
                    // Cập nhật lẻ các khóa trong Session
                    $_SESSION['user']['first_name'] = $data['first_name'];
                    $_SESSION['user']['last_name']  = $data['last_name'];
                    $_SESSION['user']['email']      = $data['email'];

                    // Ghép họ tên mới để cập nhật khóa display_name cho Sidebar nhận diện
                    $fullName = trim($data['first_name'] . ' ' . $data['last_name']);
                    $_SESSION['user']['display_name'] = !empty($fullName) ? $fullName : $data['username'];

                    $_SESSION['flash_success'] = "Cập nhật thông tin cá nhân thành công!";
                } else {
                    $_SESSION['flash_error'] = "Không thể cập nhật thông tin. Vui lòng thử lại.";
                }
            }
        }
        redirect('user/profile');
    }

    // Xử lý yêu cầu đổi mật khẩu mới (Có kiểm tra mật khẩu cũ)
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword     = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Kiểm tra các trường nhập vào
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['flash_error'] = "Vui lòng điền đầy đủ các trường mật khẩu.";
                redirect('user/profile');
                exit();
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['flash_error'] = "Mật khẩu mới và mật khẩu xác nhận không khớp.";
                redirect('user/profile');
                exit();
            }

            // Gọi hàm xử lý nghiệp vụ của UserModel
            $success = $this->userModel->updatePassword($userId, $currentPassword, $newPassword);

            if ($success) {
                $_SESSION['flash_success'] = "Đổi mật khẩu thành công!";
            } else {
                $_SESSION['flash_error'] = "Đổi mật khẩu thất bại. Mật khẩu hiện tại không chính xác.";
            }
        }
        redirect('user/profile');
    }
}