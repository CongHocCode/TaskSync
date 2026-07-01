<?php
class Project extends Controller
{
    private $projectModel;
    public function __construct()
    {
        // Tự động nạp ProjectModel cho tất cả các hàm bên dưới sử dụng
        $this->projectModel = $this->model('ProjectModel');

        // BẢO MẬT: Kiểm tra nếu chưa đăng nhập thì redirect về trang auth ngay lập tức
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['flash_error'] = "Vui lòng đăng nhập để tiếp tục.";
            redirect('auth');
            exit();
        }
    }
    // Danh sách tất cả dự án (Hệ thống)- này tui làm theo mẫu nha
    public function index()
    {
        $data['page_title'] = "Tất cả dự án";

        // Gọi model lấy toàn bộ dự án
        $data['projects'] = $this->projectModel->getAllProjects();

        $this->view('pages/projects/index', $data);
    }

    // Hàm tạo dự án mới (Cả hiển thị form và xử lý lưu data)
    public function create()
    {
        // Nếu người dùng gửi Form lên (Request POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $key = strtoupper(trim($_POST['key'] ?? '')); // Chuyển mã key sang in hoa
            $description = trim($_POST['description'] ?? '');
            $githubRepoUrl = trim($_POST['github_repo_url'] ?? '');
            $ownerId = $_SESSION['user']['id'];

            // Kiểm tra lỗi bỏ trống trường bắt buộc
            if (empty($name) || empty($key)) {
                $_SESSION['flash_error'] = "Vui lòng nhập đầy đủ tên và mã viết tắt dự án.";
                redirect('project/myProjects');
                exit();
            }

            // Gọi model xử lý lưu dữ liệu
            $success = $this->projectModel->createProject($name, $key, $description, $githubRepoUrl, $ownerId);

            if ($success) {
                $_SESSION['flash_success'] = "Tạo dự án mới thành công!";
                redirect('project/myProjects');
            } else {
                $_SESSION['flash_error'] = "Tạo dự án thất bại. Có thể mã viết tắt (Key) đã tồn tại.";
                redirect('project/myProjects');
            }
            exit();
        }

        // Nếu là truy cập bình thường (GET), hiển thị form tạo dự án
        $data['page_title'] = "Tạo dự án mới";
        $this->view('pages/projects/create', $data);
    }
    // Bảng Kanban của dự án
    public function kanban($projectId = null)
    {
        $isMember = $this->projectModel->isProjectMember($projectId, $_SESSION['user']['id']);
        $isAdmin = ($_SESSION['user']['role'] === 'admin');

        if (!$isMember && !$isAdmin) {
            redirect('workspace/dashboard'); // Đẩy ngược về trang chủ
            exit();
        }

        if (!$projectId) {
            redirect('project/myProjects');
        }

        // Lấy thông tin dự án hiện tại để hiển thị tên dự án lên tiêu đề
        $project = $this->projectModel->getProjectById($projectId);

        // Lấy danh sách tasks từ database
        $taskModel = $this->model('TaskModel');
        $tasks = $taskModel->getIssuesByProjectId($projectId);

        // Lấy danh sách thành viên tham gia dự án
        $members = $this->projectModel->getProjectMembers($projectId);
        // Lọc các member đang active để đưa cho kanban
        $activeMembers = array_filter($members ?? [], function ($m) {
            return ($m['status'] ?? 'active') === 'active';
        });
        $data['page_title'] = "Bảng Kanban - " . ($project['name'] ?? "WEB");
        $data['project'] = $project;
        $data['tasks'] = $tasks;
        $data['members'] = $activeMembers;

        $this->view('pages/projects/kanban', $data);
    }

    // Danh sách task của dự án
    public function list($projectId = null)
    {
        if (!$projectId) {
            redirect('project/myProjects');
        }

        $project = $this->projectModel->getProjectById($projectId);

        // Lấy danh sách tasks từ database
        $taskModel = $this->model('TaskModel');
        $tasks = $taskModel->getIssuesByProjectId($projectId);

        // Lấy danh sách thành viên tham gia dự án
        $members = $this->projectModel->getProjectMembers($projectId);

        $data['page_title'] = "Danh sách - " . ($project['name'] ?? "WEB");
        $data['project'] = $project;
        $data['tasks'] = $tasks;
        $data['members'] = $members;
        $this->view('pages/projects/list', $data);
    }

    // Trang quản lý thành viên & Thống kê dự án
    public function members($projectId = null)
    {
        if (!$projectId) {
            redirect('project/myProjects');
            exit();
        }

        $project = $this->projectModel->getProjectById($projectId);

        if (!$project) {
            redirect('project/myProjects');
            exit();
        }

        // Người dùng phải là thành viên dự án (hoặc Admin) mới được quyền xem danh sách này [4]
        $isMember = $this->projectModel->isProjectMember($projectId, $_SESSION['user']['id']);
        $isAdmin = ($_SESSION['user']['role'] === 'admin');

        if (!$isMember && !$isAdmin) {
            redirect('project/myProjects');
            exit();
        }

        // Lấy thành viên hiện tại
        $members = $this->projectModel->getProjectMembers($projectId);

        // Lấy nhân sự chưa có trong dự án (để đưa vào ô Select thêm thành viên)
        $nonMembers = $this->projectModel->getNonMembersOfProject($projectId);

        // Lấy thống kê của riêng dự án này
        $projectStats = $this->projectModel->getProjectStats($projectId);

        // Kiểm tra quyền xóa thành viên
        $isManager = $this->projectModel->isProjectManager($projectId, $_SESSION['user']['id']);
        $isAuthorizedToDelete = ($isManager || $isAdmin);

        $this->view('pages/projects/members', [
            'page_title'              => $project['name'] . ' - Thành viên',
            'project'                 => $project,
            'members'                 => $members,
            'non_members'             => $nonMembers,
            'stats'                   => $projectStats,
            'is_authorized_to_delete' => $isAuthorizedToDelete // Truyền quyền quyết định xóa sang View
        ]);
    }

    public function searchNonMembers($projectId)
    {
        // Lấy từ khóa tìm kiếm qua tham số GET, ví dụ: ?q=nguyen
        $searchTerm = trim($_GET['q'] ?? '');

        $projectModel = $this->model('ProjectModel');
        $results = $projectModel->searchNonMembersOfProject($projectId, $searchTerm);

        header('Content-Type: application/json');
        echo json_encode($results);
        exit();
    }

    // Thêm thành viên dự án
    public function addMember()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'] ?? null;
            $userId    = $_POST['user_id'] ?? null;
            $role      = $_POST['role'] ?? 'member';

            // Lấy ID của người đang thực hiện hành động mời
            $senderId  = $_SESSION['user']['id'];

            if ($projectId && $userId) {
                $projectModel = $this->model('ProjectModel');

                // Chỉ Manager của dự án hoặc Admin hệ thống mới được mời
                $isAuthorized = ($_SESSION['user']['role'] === 'admin' || $projectModel->isProjectManager($projectId, $senderId));

                if ($isAuthorized) {
                    // Truyền thêm $senderId vào làm tham số thứ 4 (invited_by)
                    $projectModel->addMemberToProject($projectId, $userId, $role, $senderId);
                    $_SESSION['flash_success'] = "Đã gửi lời mời tham gia dự án thành công!";
                } else {
                    $_SESSION['flash_error'] = "Bạn không có quyền mời thành viên vào dự án này.";
                }
            }
            header('Location: ' . BASE_URL . '/project/members/' . $projectId);
            exit();
        }
    }

    public function removeMember($projectId, $userId)
    {
        // Kiểm tra bảo mật: Chỉ Manager của dự án hoặc Admin hệ thống mới được quyền xóa thành viên
        $isAuthorized = ($_SESSION['user']['role'] === 'admin' || $this->projectModel->isProjectManager($projectId, $_SESSION['user']['id']));

        if ($isAuthorized) {
            $this->projectModel->removeMemberFromProject($projectId, $userId);
        }

        redirect('/project/members/' . $projectId);
        exit();
    }

    // Thay đổi vai trò thành viên (Bổ nhiệm / Bãi nhiệm Manager)
    public function changeRole()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'] ?? null;
            $userId    = $_POST['user_id'] ?? null;
            $role      = $_POST['role'] ?? 'member'; // Vai trò mới muốn thay đổi
            $senderId  = $_SESSION['user']['id'];

            if ($projectId && $userId) {
                $projectModel = $this->model('ProjectModel');

                // Bảo mật: Chỉ Manager gốc của dự án hoặc Admin hệ thống mới được đổi vai trò người khác
                $isAuthorized = ($_SESSION['user']['role'] === 'admin' || $projectModel->isProjectManager($projectId, $senderId));

                if ($isAuthorized) {
                    // Gọi lại hàm addMemberToProject để kích hoạt ON DUPLICATE KEY UPDATE vai trò mới
                    $projectModel->addMemberToProject($projectId, $userId, $role, $senderId);
                    $_SESSION['flash_success'] = "Đã thay đổi vai trò thành viên thành công!";
                } else {
                    $_SESSION['flash_error'] = "Bạn không có quyền thực hiện hành động này.";
                }
            }
            redirect('project/members/' . $projectId);
            exit();
        }
    }

    // Chấp nhận lời mời tham gia dự án
    public function acceptInvite($projectId)
    {
        $userId = $_SESSION['user']['id'];
        $projectModel = $this->model('ProjectModel');

        $success = $projectModel->acceptInvitation($projectId, $userId);
        if ($success) {
            $_SESSION['flash_success'] = "Bạn đã chính thức tham gia dự án!";
            redirect('project/kanban/' . $projectId); // Nhảy thẳng vào bảng Kanban
            exit();
        }
        redirect('/dashboard/member');
    }

    // Từ chối lời mời tham gia dự án
    public function declineInvite($projectId)
    {
        $userId = $_SESSION['user']['id'];
        $projectModel = $this->model('ProjectModel');

        $success = $projectModel->declineInvitation($projectId, $userId);
        if ($success) {
            $_SESSION['flash_success'] = "Đã từ chối lời mời tham gia dự án.";
        }
        redirect('/dashboard/member');
    }
    public function memberStats($userId)
    {
        // Lấy ID dự án từ URL gửi lên bằng tham số GET
        $projectId = $_GET['project_id'] ?? null;

        $projects = $this->projectModel->getProjectsByMemberId($userId);
        $tasks    = $this->projectModel->getMemberTaskStats($userId, $projectId);

        header('Content-Type: application/json');
        echo json_encode([
            'projects' => $projects,
            'tasks'    => $tasks
        ]);
        exit();
    }

    // Trang cấu hình dự án (Chỉ dành riêng cho Quản lý dự án - Manager)
    public function settings($projectId = null)
    {
        if (!$projectId) {
            header('Location: ' . BASE_URL . 'project/myProjects');
            exit();
        }

        $projectModel = $this->model('ProjectModel');

        $isManager = $this->projectModel->isProjectManager($projectId, $_SESSION['user']['id']);
        $isAdmin = ($_SESSION['user']['role'] === 'admin');

        if (!$isManager && !$isAdmin) {
            redirect('project/kanban/' . $projectId); // Đẩy về trang Kanban của chính dự án đó
            exit();
        }

        $project = $projectModel->getProjectById($projectId);

        if (!$project) {
            header('Location: ' . BASE_URL . 'project/myProjects');
            exit();
        }

        // Lấy thống kê sức khỏe dự án
        $projectStats = $projectModel->getProjectStats($projectId);

        // Lưu đường dẫn quay lại dựa trên nguồn truy cập (từ quản lý dự án admin hay dự án của tôi)
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin/projects') !== false) {
            $_SESSION['settings_back_url'] = 'admin/projects';
        } else {
            $_SESSION['settings_back_url'] = 'project/myProjects';
        }

        // Nạp view Cấu hình dự án
        $this->view('pages/projects/settings', [
            'page_title' => $project['name'] . ' - Cấu hình',
            'project'    => $project,
            'stats'      => $projectStats
        ]);
    }

    // Cập nhật thông tin dự án
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = trim($_POST['id'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $key = strtoupper(trim($_POST['key'] ?? ''));
            $description = trim($_POST['description'] ?? '');
            $githubRepoUrl = trim($_POST['github_repo_url'] ?? '');
            $userId = $_SESSION['user']['id'];

            if (empty($projectId) || empty($name) || empty($key)) {
                $_SESSION['flash_error'] = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
                redirect('project/myProjects');
                exit();
            }

            // Kiểm tra quyền hạn: phải là Admin hệ thống hoặc Manager của dự án
            $isAuthorized = ($_SESSION['user']['role'] === 'admin' || $this->projectModel->isProjectManager($projectId, $userId));
            if (!$isAuthorized) {
                $_SESSION['flash_error'] = "Bạn không có quyền chỉnh sửa dự án này.";
                redirect('project/myProjects');
                exit();
            }

            $success = $this->projectModel->updateProject($projectId, $name, $key, $description, $githubRepoUrl);

            if ($success) {
                $_SESSION['flash_success'] = "Cập nhật dự án thành công!";
            } else {
                $_SESSION['flash_error'] = "Cập nhật dự án thất bại. Có thể mã viết tắt (Key) đã tồn tại.";
            }
            $redirectUrl = $_SESSION['settings_back_url'] ?? 'project/myProjects';
            unset($_SESSION['settings_back_url']);
            redirect($redirectUrl);
            exit();
        }
        $redirectUrl = $_SESSION['settings_back_url'] ?? 'project/myProjects';
        unset($_SESSION['settings_back_url']);
        redirect($redirectUrl);
    }

    // Xóa dự án
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = trim($_POST['id'] ?? '');
            $userId = $_SESSION['user']['id'];

            if (empty($projectId)) {
                $_SESSION['flash_error'] = "ID dự án không hợp lệ.";
                redirect('project/myProjects');
                exit();
            }

            // Kiểm tra quyền hạn: phải là Admin hệ thống hoặc Manager của dự án
            $isAuthorized = ($_SESSION['user']['role'] === 'admin' || $this->projectModel->isProjectManager($projectId, $userId));
            if (!$isAuthorized) {
                $_SESSION['flash_error'] = "Bạn không có quyền xóa dự án này.";
                redirect('project/myProjects');
                exit();
            }

            $success = $this->projectModel->deleteProject($projectId);

            if ($success) {
                $_SESSION['flash_success'] = "Xóa dự án thành công!";
            } else {
                $_SESSION['flash_error'] = "Xóa dự án thất bại. Vui lòng thử lại.";
            }
            $redirectUrl = $_SESSION['settings_back_url'] ?? 'project/myProjects';
            unset($_SESSION['settings_back_url']);
            redirect($redirectUrl);
            exit();
        }
        $redirectUrl = $_SESSION['settings_back_url'] ?? 'project/myProjects';
        unset($_SESSION['settings_back_url']);
        redirect($redirectUrl);
    }
}
