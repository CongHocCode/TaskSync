<?php
class Task extends Controller {
    public function index() {
        // Thiết lập tiêu đề trang cho Layout
        $data['page_title'] = "Bảng công việc Kanban";
        
        $this->view('pages/tasks/kanban', $data);
    }

    public function myTasks() {
        // Công việc của người dùng hiện tại
        $data['page_title'] = "Task của tôi (2)";
        $this->view('pages/tasks/my-tasks', $data);
    }

    public function create() {
        // Tạo task mới
        $data['page_title'] = "Tạo Issue mới";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Xử lý tạo task
        }
        $this->view('pages/tasks/create', $data);
    }

    public function edit($taskId = null) {
        // Chỉnh sửa task
        $data['page_title'] = "Chỉnh sửa Task";
        $data['task_id'] = $taskId;
        $this->view('pages/tasks/edit', $data);
    }
}
