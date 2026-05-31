<?php
class Home extends Controller {
    public function index() {
        // Gọi View và truyền dữ liệu sang
        $this->view('pages/home/index', [
            'page_title' => 'Trang chủ TaskSync',
            'message' => 'Hệ thống MVC Core đã hoạt động!'
        ]);
    }
}