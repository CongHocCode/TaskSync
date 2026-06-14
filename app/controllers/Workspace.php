<?php
class Workspace extends Controller {
    public function index() {
        if (!isset($_SESSION['user'])) {
            redirect('auth');
        }

        $data['page_title'] = "Dashboard tổng hợp";
        $this->view('pages/dashboard', $data);
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
