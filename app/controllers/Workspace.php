<?php
class Workspace extends Controller {
    private $taskModel;
    private $projectModel;

    public function __construct()
    {
        // Yêu cầu đăng nhập mới được vào
        if (!isset($_SESSION['user'])) {
            redirect('auth');
            exit();
        }

        $this->projectModel = $this->model('ProjectModel');
        $this->taskModel = $this->model('TaskModel');
    }

    // Dashboard tổng hợp cá nhân
    public function index() {
        $userId = $_SESSION['user']['id'];

        $data['page_title'] = "Dashboard tổng hợp";
        $data['assigned_issues'] = $this->taskModel->getAssignedIssuesByUserId($userId);
        $data['my_projects'] = $this->projectModel->getProjectsWithCountsByUserId($userId);

        $this->view('pages/dashboard/member', $data);
    }

    // Danh sách công việc cá nhân có tìm kiếm động
    public function myTasks()
    {
        $userId = $_SESSION['user']['id'];

        //  Đọc từ khóa 'q' trước khi gọi Model để thực hiện chức năng tìm kiếm
        $search = trim($_GET['q'] ?? ''); 

        $tasks = $this->taskModel->getAllIssuesByUserId($userId, $search);

        // Thống kê nhanh tự động tính theo danh sách sau khi đã lọc từ khóa
        $stats = [
            'total'       => count($tasks),
            'todo'        => count(array_filter($tasks, fn($t) => $t['status'] === 'todo')),
            'in_progress' => count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress')),
            'in_review'   => count(array_filter($tasks, fn($t) => $t['status'] === 'in_review')),
            'done'        => count(array_filter($tasks, fn($t) => $t['status'] === 'done')),
        ];

        $data = [
            'page_title' => "Công việc của tôi",
            'tasks'      => $tasks,
            'stats'      => $stats,
            'search'     => $search // Truyền từ khóa sang View
        ];

        $this->view('pages/workspace/my_tasks', $data);
    }

    // Dự án của người dùng hiện tại
    public function myProjects()
    {
        $userId = $_SESSION['user']['id']; 

        $data['page_title'] = "Dự án của tôi";
        $data['projects'] = $this->projectModel->getProjectsByUserId($userId);

        $this->view('pages/workspace/my_projects', $data);
    }
}