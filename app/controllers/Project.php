<?php
class Project extends Controller {
    public function index() {
        // Danh sách tất cả dự án
        $data['page_title'] = "Dự án";
        $this->view('pages/projects/index', $data);
    }

    public function myProjects() {
        // Dự án của người dùng hiện tại
        $data['page_title'] = "Dự án của tôi (3)";
        $this->view('pages/projects/my-projects', $data);
    }

    public function kanban($projectId = null) {
        // Bảng Kanban của dự án
        $data['page_title'] = "Bảng Kanban - " . ($projectId ?? "WEB");
        $data['project_id'] = $projectId;
        $this->view('pages/projects/kanban', $data);
    }

    public function list($projectId = null) {
        // Danh sách task của dự án
        $data['page_title'] = "Danh sách - " . ($projectId ?? "WEB");
        $data['project_id'] = $projectId;
        $this->view('pages/projects/list', $data);
    }

    public function members($projectId = null) {
        // Thành viên dự án
        $data['page_title'] = "Thành viên Dự án - " . ($projectId ?? "WEB");
        $data['project_id'] = $projectId;
        $this->view('pages/projects/members', $data);
    }

    public function settings($projectId = null) {
        // Cấu hình dự án
        $data['page_title'] = "Cấu hình dự án - " . ($projectId ?? "WEB");
        $data['project_id'] = $projectId;
        $this->view('pages/projects/settings', $data);
    }
}
