<?php
class Admin extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('auth');
            exit();
        }
    }

    // Trang Dashboard thống kê hệ thống (admin/dashboard)
    public function dashboard()
    {
        $data['page_title'] = "Thống kê hệ thống";
        $this->view('pages/admin/dashboard', $data);
    }

    // Trang danh sách nhân viên (admin/users)
    public function users()
    {
        $userModel = $this->model('UserModel');
        $users = $userModel->getAll();

        $data['page_title'] = "Quản lý nhân sự";
        $data['users'] = $users;

        $this->view('pages/admin/users', $data);
    }

    //  Trang tạo nhân viên mới (admin/createUser)
    public function createUser()
    {
        $data['page_title'] = "Thêm nhân viên mới";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName  = trim($_POST['last_name'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $username  = trim($_POST['username'] ?? '');
            $password  = trim($_POST['password'] ?? '');
            $role      = trim($_POST['role'] ?? 'user');

            $userModel = $this->model('UserModel');

            if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password)) {
                $data['error'] = "Vui lòng điền đầy đủ thông tin.";
            } elseif ($userModel->checkExists($username, $email)) {
                $data['error'] = "Username hoặc Email này đã tồn tại.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $success = $userModel->create([
                    'username'      => $username,
                    'email'         => $email,
                    'password_hash' => $hashedPassword,
                    'first_name'    => $firstName,
                    'last_name'     => $lastName,
                    'role'          => $role
                ]);

                if ($success) {
                    redirect('admin/users');
                    exit;
                } else {
                    $data['error'] = "Có lỗi xảy ra, vui lòng thử lại.";
                }
            }
        }

        $this->view('pages/admin/create_user', $data); // Form tạo nằm trong admin
    }

    // Khóa/Mở khóa tài khoản (admin/toggleUserStatus)
    public function toggleUserStatus($id)
    {
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($id);

        if ($user) {
            $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
            $userModel->updateStatus($id, $newStatus);
        }

        redirect('admin/users');
    }

    // Sửa thông tin nhân viên (admin/editUser/ID)
    public function editUser($id = null)
    {
        if (!$id) {
            redirect('admin/users');
        }

        $userModel = $this->model('UserModel');
        $user = $userModel->getById($id);

        if (!$user) {
            die("Nhân sự không tồn tại");
        }

        $data['user'] = $user;
        $data['page-title'] = "Cập nhật thông tin nhân sự";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName  = trim($_POST['last_name'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $username  = trim($_POST['username'] ?? '');
            $role      = trim($_POST['role'] ?? 'user');

            if (empty($firstName) || empty($lastName) || empty($email) || empty($username)) {
                $data['error'] = "Vui lòng điền đầy đủ thông tin.";
            } else {
                $success = $userModel->update($id, [
                    'username'   => $username,
                    'email'      => $email,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'role'       => $role
                ]);

                if ($success) {
                    redirect('admin/users');
                    exit;
                } else {
                    $data['error'] = "Có lỗi xảy ra trong quá trình cập nhật.";
                }
            }
        }
        $this->view('pages/admin/edit_user', $data);
    }

    // Trang xem toàn bộ dự án hệ thống (admin/projects)
    public function projects()
    {
        $data['page_title'] = "Quản lý toàn bộ dự án";
        $this->view('pages/admin/projects', $data);
    }
}
