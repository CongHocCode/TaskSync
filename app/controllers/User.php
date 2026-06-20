<?php
class User extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            redirect('auth');
            exit();
        }
    }

    // Trang xem hồ sơ cá nhân (user/profile)
    public function profile() {
        $data['page_title'] = "Hồ sơ cá nhân";
        $this->view('pages/user/profile', $data);
    }

    // Trang cài đặt tài khoản (user/settings)
    public function settings() {
        $data['page_title'] = "Cài đặt tài khoản";
        $this->view('pages/user/settings', $data);
    }
}