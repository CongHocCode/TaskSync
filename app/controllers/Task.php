<?php
class Task extends Controller {
    public function index() {
        // Thiết lập tiêu đề trang cho Layout
        $data['page_title'] = "Bảng công việc Kanban";
        
        $this->view('pages/tasks/kanban', $data);
    }
}