<?php
class Auth extends Controller
{

    // Hiển thị giao diện và redirect nếu cần
    public function index()
    {
        $data['page_title'] = "Đăng nhập & Đăng ký | TaskSync";

        if (isset($_SESSION['flash_error'])) { //Hiển thị lỗi nếu có
            $data['error'] = $_SESSION['flash_error'];
            unset($_SESSION['flash_error']);
        }

        if (isset($_SESSION['user'])) {
            redirect('workspace');
        }

        // Nhận thông báo thành công sau khi đăng ký nếu có
        if (isset($_SESSION['flash_success'])) {
            $data['success'] = $_SESSION['flash_success'];
            unset($_SESSION['flash_success']);
        }

        $this->view('pages/auth/index', $data, false);
    }

    // ĐĂNG NHẬP (Chỉ nhận POST)
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth');
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $userModel = $this->model('UserModel');
        $user = $userModel->getByUsernameOrEmail($username);

        if ($user && password_verify($password, $user['password_hash'])) {

            if (isset($user['status']) && $user['status'] === 'inactive') {
                $_SESSION['flash_error'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin để mở khóa.';
                redirect('auth');
                exit();
            }

            $_SESSION['user'] = [
                'id'           => $user['id'],
                'username'     => $user['username'],
                'role'         => $user['role'],
                'display_name' => $user['first_name'] . ' ' . $user['last_name'],
                'avatar'       => $user['avatar_url'] ?? 'default-avatar.png'
            ];
            redirect('workspace');
        } else {
            // Nếu sai, lưu lỗi vào session tạm và đá ngược về trang index
            $_SESSION['flash_error'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
            redirect('auth');
        }
    }

    // ĐĂNG KÝ (Chỉ nhận POST)
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth');
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $password  = trim($_POST['password'] ?? '');

        $userModel = $this->model('UserModel');

        if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password)) {
            $_SESSION['flash_error'] = "Vui lòng điền đầy đủ các thông tin bắt buộc.";
            redirect('auth');
        } elseif ($userModel->checkExists($username, $email)) {
            $_SESSION['flash_error'] = "Tên đăng nhập hoặc Email này đã được sử dụng.";
            redirect('auth');
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $success = $userModel->create([
                'username'      => $username,
                'email'         => $email,
                'password_hash' => $hashedPassword,
                'first_name'    => $firstName,
                'last_name'     => $lastName
            ]);

            if ($success) {
                $_SESSION['flash_success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                redirect('auth');
            } else {
                $_SESSION['flash_error'] = "Đã xảy ra lỗi hệ thống, vui lòng thử lại sau.";
                redirect('auth');
            }
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        redirect('auth');
    }
}
