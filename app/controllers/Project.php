<?php
class Project extends Controller {
    private $projectModel;
    public function __construct() {
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
    // 1. Danh sách tất cả dự án (Hệ thống)- này tui làm theo mẫu nha
    public function index() {
        $data['page_title'] = "Tất cả dự án";
        
        // Gọi model lấy toàn bộ dự án
        $data['projects'] = $this->projectModel->getAllProjects();
        
        $this->view('pages/projects/index', $data);
    }

     // 2. Dự án của người dùng hiện tại
    public function myProjects() {
        $userId = $_SESSION['user']['id']; // Lấy ID của user đang đăng nhập từ Session
        
        $data['page_title'] = "Dự án của tôi";
        // Gọi model lấy danh sách dự án mà user này tham gia
        $data['projects'] = $this->projectModel->getProjectsByUserId($userId);
        
        $this->view('pages/projects/my-projects', $data);
    }

    // 3. Hàm tạo dự án mới (Cả hiển thị form và xử lý lưu data)
    public function create() {
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
    // 4. Bảng Kanban của dự án
    public function kanban($projectId = null) {
        if (!$projectId) {
            redirect('project/myProjects');
        }
        
        // Lấy thông tin dự án hiện tại để hiển thị tên dự án lên tiêu đề
        $project = $this->projectModel->getProjectById($projectId);
        
        $data['page_title'] = "Bảng Kanban - " . ($project['name'] ?? "WEB");
        $data['project'] = $project;
        $this->view('pages/projects/kanban', $data);
    }

    // 5. Danh sách task của dự án
    public function list($projectId = null) {
        if (!$projectId) {
            redirect('project/myProjects');
        }
        
        $project = $this->projectModel->getProjectById($projectId);
        
        $data['page_title'] = "Danh sách - " . ($project['name'] ?? "WEB");
        $data['project'] = $project;
        $this->view('pages/projects/list', $data);
    }

    // 6. Thành viên dự án
    public function members($projectId = null) {
        if (!$projectId) {
            redirect('project/myProjects');
        }
        
        $project = $this->projectModel->getProjectById($projectId);
        
        $data['page_title'] = "Thành viên Dự án - " . ($project['name'] ?? "WEB");
        $data['project'] = $project;
        $this->view('pages/projects/members', $data);
    }

    // 7. Cấu hình dự án
    public function settings($projectId = null) {
        if (!$projectId) {
            redirect('project/myProjects');
        }
        
        $project = $this->projectModel->getProjectById($projectId);
        
        $data['page_title'] = "Cấu hình dự án - " . ($project['name'] ?? "WEB");
        $data['project'] = $project;
        $this->view('pages/projects/settings', $data);
    }
}
