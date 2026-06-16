<?php
//Lớp cha giúp các controller con gọi model và view
class Controller {
    // Hàm gọi Model
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    // Hàm gọi View (Tự động bọc giao diện vào layout.php)
    public function view($view, $data = [], $useLayout = true) {
        // Biến $view và $data sẽ được layout sử dụng
        if ($useLayout) {
            require_once __DIR__ . '/../views/layout.php';
        } else {
            require_once __DIR__ . '/../views/' . $view . '.php';
        }
    }
}