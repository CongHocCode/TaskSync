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
        $taskModel = $this->model('TaskModel');
        $data['page_title'] = "Thống kê hệ thống";
        $data['task_frequency'] = $taskModel->getTaskFrequency();
        $data['new_users_stats'] = $taskModel->getNewUsersStats();
        
        $this->view('pages/dashboard/admin', $data);
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
        $projectModel = $this->model('ProjectModel');
        $userModel = $this->model('UserModel');

        $data['page_title'] = "Quản lý toàn bộ dự án";
        $data['projects'] = $projectModel->getAllProjectsWithCounts();
        $data['project_members'] = $projectModel->getAllActiveProjectMembersGrouped();
        $data['users'] = $userModel->getAll();

        $this->view('pages/admin/projects', $data);
    }

    // Đổi Owner của dự án (admin/changeProjectOwner)
    public function changeProjectOwner()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'] ?? null;
            $newOwnerId = $_POST['new_owner_id'] ?? null;

            if ($projectId && $newOwnerId) {
                $projectModel = $this->model('ProjectModel');
                $success = $projectModel->changeProjectOwner($projectId, $newOwnerId);
                if ($success) {
                    $_SESSION['flash_success'] = "Thay đổi trưởng dự án thành công!";
                } else {
                    $_SESSION['flash_error'] = "Thay đổi trưởng dự án thất bại.";
                }
            } else {
                $_SESSION['flash_error'] = "Thông tin không đầy đủ.";
            }
        }
        redirect('admin/projects');
        exit;
    }

    // Xóa dự án (admin/deleteProject/ID)
    public function deleteProject($id = null)
    {
        if (!$id) {
            redirect('admin/projects');
            exit;
        }

        $projectModel = $this->model('ProjectModel');
        $success = $projectModel->deleteProject($id);

        if ($success) {
            $_SESSION['flash_success'] = "Xóa dự án thành công!";
        } else {
            $_SESSION['flash_error'] = "Xóa dự án thất bại.";
        }

        redirect('admin/projects');
        exit;
    }


    // Xóa nhân sự (admin/deleteUser/ID)
    public function deleteUser($id = null)
    {
        if (!$id) {
            redirect('admin/users');
            exit;
        }

        // Không cho phép tự xóa bản thân
        if ($id == $_SESSION['user']['id']) {
            redirect('admin/users');
            exit;
        }

        $userModel = $this->model('UserModel');
        $user = $userModel->getById($id);

        if ($user) {
            $adminId = $_SESSION['user']['id'];
            $userModel->delete($id, $adminId);
        }

        redirect('admin/users');
        exit;
    }
}
