<?php
class App {
    protected $controller = "Home"; // Mặc định mở Home
    protected $method = "index";    // Mặc định gọi hàm index
    protected $params = [];

    public function __construct() {
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