<?php
//Lớp cha giúp các controller con gọi model và view
class Controller {
    // Hàm gọi Model
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    // Hàm gọi View (Tự động bọc giao diện vào layout.php)
    public function view($view, $data = []) {
        // Biến $view và $data sẽ được layout sử dụng
        require_once __DIR__ . '/../views/layout.php';
    }
}