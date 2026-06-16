<?php
class User extends Controller {
    public function index() {
        // Danh sách người dùng
        $data['page_title'] = "Quản lý người dùng";
        $this->view('pages/users/index', $data);
    }

    public function profile($userId = null) {
        // Hồ sơ người dùng
        $data['page_title'] = "Hồ sơ người dùng";
        $data['user_id'] = $userId;
        $this->view('pages/users/profile', $data);
    }

    public function settings() {
        // Cài đặt tài khoản
        $data['page_title'] = "Cài đặt tài khoản";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Xử lý cập nhật cài đặt
        }
        $this->view('pages/users/settings', $data);
    }
}
