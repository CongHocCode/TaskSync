<?php
class Workspace extends Controller {
    public function index() {
        if (!isset($_SESSION['user'])) {
            redirect('auth');
        }

        // Nếu là admin thì chuyển hướng sang dashboard của admin
        if ($_SESSION['user']['role'] === 'admin') {
            redirect('admin/dashboard');
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $taskModel = $this->model('TaskModel');
        $projectModel = $this->model('ProjectModel');

        $data['page_title'] = "Dashboard tổng hợp";
        $data['assigned_issues'] = $taskModel->getAssignedIssuesByUserId($userId);
        $data['my_projects'] = $projectModel->getProjectsWithCountsByUserId($userId);

        $this->view('pages/dashboard/member', $data);
    }

    public function overview() {
        if (!isset($_SESSION['user'])) {
            redirect('auth');
        }

        $data['page_title'] = "Tổng quan Workspace";
        $this->view('pages/workspace/overview', $data);
    }

    public function settings() {
        if (!isset($_SESSION['user'])) {
            redirect('auth');
        }

        $data['page_title'] = "Cài đặt Workspace";
        $this->view('pages/workspace/settings', $data);
    }
}
