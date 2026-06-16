<?php
class Admin extends Controller {
    public function index() {
        // Dashboard admin
        $data['page_title'] = "Admin Dashboard";
        $this->view('pages/admin/dashboard', $data);
    }

    public function users() {
        // Quản lý người dùng
        $data['page_title'] = "Quản lý người dùng";
        $this->view('pages/admin/users', $data);
    }

    public function projects() {
        // Quản lý dự án
        $data['page_title'] = "Quản lý dự án";
        $this->view('pages/admin/projects', $data);
    }

    public function settings() {
        // Cài đặt hệ thống
        $data['page_title'] = "Cài đặt hệ thống";
        $this->view('pages/admin/settings', $data);
    }
}
