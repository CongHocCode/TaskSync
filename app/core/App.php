<?php
// Dev toggle: set to false to disable automatic dev login
if (!defined('DEV_BYPASS_AUTH')) {
    define('DEV_BYPASS_AUTH', false); //TODO đang tắt để test login
}

class App {
    protected $controller = "Auth"; //Mặc định mở trang Workspace (bypass login for dev) //TODO
    protected $method = "index";    // Mặc định gọi hàm index
    protected $params = [];

    public function __construct() {
        // DEV: Nếu bật DEV_BYPASS_AUTH thì tạo 1 user mẫu để bỏ qua bước login
        if (defined('DEV_BYPASS_AUTH') && DEV_BYPASS_AUTH) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            if (!isset($_SESSION['user'])) {
                $_SESSION['user'] = [
                    'id' => 1,
                    'name' => 'Quyen Gia',
                    'role' => 'admin',
                    'email' => 'dev@local'
                ];
            }
        }

        $url = $this->parseUrl();

        // 1. Tìm Controller
        if (isset($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . '.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }
        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Tìm Method (Hàm)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Lấy tham số (Params)
        $this->params = $url ? array_values($url) : [];

        // 4. Chạy
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}