<?php
class Admin extends Controller
{
    // Khai báo các thuộc tính lớp để quản lý các Model dùng chung
    private $userModel;
    private $projectModel;
    private $taskModel;

    public function __construct()
    {
        // Chốt chặn bảo mật hệ thống
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('auth');
            exit();
        }

        // Khởi tạo các Model duy nhất một lần tại đây
        $this->userModel = $this->model('UserModel');
        $this->projectModel = $this->model('ProjectModel');
        $this->taskModel = $this->model('TaskModel');
    }

    public function dashboard()
    {
        // Lấy các con số đếm tổng quan hệ thống từ DB
        $totalUsers    = $this->userModel->getTotalUsersCount();
        $blockedUsers  = $this->userModel->getBlockedUsersCount();
        $totalProjects = $this->projectModel->getTotalProjectsCount();
        $totalTasks    = $this->taskModel->getTotalTasksCount();

        // Lấy dữ liệu thống kê cho 2 biểu đồ Chart.js
        $taskFrequency = $this->taskModel->getSystemTaskFrequency();
        $newUsersStats = $this->userModel->getNewUsersStats();

        // Đóng gói dữ liệu vào mảng $data truyền sang View
        $data = [
            'total_users'    => $totalUsers,
            'blocked_users'  => $blockedUsers,
            'total_projects' => $totalProjects,
            'total_tasks'    => $totalTasks,
            'task_frequency' => $taskFrequency,
            'new_users'      => $newUsersStats
        ];

        // Bổ sung dữ liệu động cho 2 widget danh sách phía dưới của Admin
        $userId = $_SESSION['user']['id'];
        $data['assigned_issues'] = $this->taskModel->getAllIssuesByUserId($userId); // Lấy công việc của riêng Admin
        $data['projects']        = $this->projectModel->getAllProjects(); // Lấy các dự án hoạt động trên máy chủ

        $this->view('pages/dashboard/admin', $data);
    }

    // Trang danh sách nhân viên (admin/users)
    public function users()
    {
        $users = $this->userModel->getAll();

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

            if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password)) {
                $data['error'] = "Vui lòng điền đầy đủ thông tin.";
            } elseif ($this->userModel->checkExists($username, $email)) {
                $data['error'] = "Username hoặc Email này đã tồn tại.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $success = $this->userModel->create([
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

        $this->view('pages/admin/create_user', $data);
    }

    // Khóa/Mở khóa tài khoản (admin/toggleUserStatus)
    public function toggleUserStatus($id)
    {
        // Không cho phép Admin tự khóa tài khoản của chính mình
        if ($id == $_SESSION['user']['id']) {
            $_SESSION['flash_error'] = "Bảo mật hệ thống: Bạn không thể tự khóa tài khoản của chính mình!";
            redirect('admin/users');
            exit();
        }

        $user = $this->userModel->getById($id);

        if ($user) {
            //Ngăn Admin thường khóa tài khoản của Admin khác
            /*
            if ($user['role'] === 'admin') {
                $_SESSION['flash_error'] = "Bảo mật hệ thống: Không thể khóa tài khoản của quản trị viên khác!";
                redirect('admin/users');
                exit();
            }
            */

            $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
            $this->userModel->updateStatus($id, $newStatus);
            $_SESSION['flash_success'] = "Đã cập nhật trạng thái hoạt động tài khoản thành công.";
        }

        redirect('admin/users');
        exit();
    }

    // Sửa thông tin nhân viên (admin/editUser/ID)
    public function editUser($id = null)
    {
        if (!$id) {
            redirect('admin/users');
        }

        $user = $this->userModel->getById($id);

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
            } elseif ($id == $_SESSION['user']['id'] && $role !== 'admin') {
                $data['error'] = "Bảo mật hệ thống: Bạn không thể tự hạ vai trò Admin của chính mình!";
            } else {
                $success = $this->userModel->update($id, [
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
        $data['projects'] = $this->projectModel->getAllProjectsWithCounts();
        $data['project_members'] = $this->projectModel->getAllActiveProjectMembersGrouped();
        $data['users'] = $this->userModel->getAll();

        $this->view('pages/admin/projects', $data);
    }

    // Đổi Owner của dự án (admin/changeProjectOwner)
    public function changeProjectOwner()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'] ?? null;
            $newOwnerId = $_POST['new_owner_id'] ?? null;

            if ($projectId && $newOwnerId) {
                $success = $this->projectModel->changeProjectOwner($projectId, $newOwnerId);
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

        $success = $this->projectModel->deleteProject($id);

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

        $user = $this->userModel->getById($id);

        if ($user) {
            $adminId = $_SESSION['user']['id'];
            $this->userModel->delete($id, $adminId);
        }

        redirect('admin/users');
        exit;
    }
}
