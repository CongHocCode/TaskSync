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

    public function uploadAvatar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $userId = $_SESSION['user']['id'];
            $file = $_FILES['avatar'];

            // Kiểm tra xem có lỗi khi upload không
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['flash_error'] = "Đã xảy ra lỗi trong quá trình tải tệp lên.";
                redirect('user/profile');
                exit();
            }

            // Khống chế dung lượng ảnh tối đa (Lấy từ cài đặt của admin)
            $settings = $this->userModel->getSystemSettings();
            $maxMB = (int)($settings['max_upload_size'] ?? 2); // Mặc định là 2MB nếu rỗng

            $maxSize = $maxMB * 1024 * 1024; // Quy đổi ra Bytes
            if ($file['size'] > $maxSize) {
                $_SESSION['flash_error'] = "Dung lượng ảnh tải lên không được vượt quá " . $maxMB . "MB.";
                redirect('user/profile');
                exit();
            }

            // Khống chế định dạng tệp tin (chỉ cho phép các định dạng ảnh phổ biến)
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['flash_error'] = "Chỉ chấp nhận các định dạng tệp tin ảnh: " . implode(', ', $allowedExtensions);
                redirect('user/profile');
                exit();
            }

            // Tạo tên tệp độc nhất dựa trên timestamp và ID để tránh trùng lặp
            $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;

            // Xác định thư mục lưu trữ thực tế trên máy chủ
            // Đường dẫn đích: /public/uploads/avatars/
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';

            // Tự động tạo thư mục nếu chưa có sẵn
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destination = $uploadDir . $newFileName;

            // Di chuyển tệp tin từ thư mục tạm của PHP sang thư mục lưu trữ thực tế
            if (move_uploaded_file($file['tmp_name'], $destination)) {

                // Xóa file ảnh cũ
                $currentUser = $this->userModel->getById($userId);
                if ($currentUser && !empty($currentUser['avatar_url']) && $currentUser['avatar_url'] !== 'default-avatar.png') {
                    $oldFile = $uploadDir . $currentUser['avatar_url'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile); // Tiến hành xóa
                    }
                }

                // Cập nhật tên tệp mới vào CSDL và cập nhật lại thông tin Session
                $this->userModel->updateAvatar($userId, $newFileName);
                $_SESSION['user']['avatar_url'] = $newFileName;

                $_SESSION['flash_success'] = "Cập nhật ảnh đại diện thành công!";
            } else {
                $_SESSION['flash_error'] = "Không thể lưu tệp ảnh lên máy chủ.";
            }
        }
        redirect('user/profile');
        exit();
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
