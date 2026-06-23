<?php

// Thử nạp file cấu hình nếu có (không báo lỗi nếu chưa tạo file config.php)
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

class Database
{
    // Giá trị cấu hình mặc định (dành cho Quyết và Quyền)
    private $host = "localhost";
    private $port = "3306"; 
    private $dbname = "task_sync";
    private $username = "root";
    private $password = "";
    public $pdo;

    public function __construct()
    {
        // Nếu có cấu hình riêng từ config.php thì ghi đè lên giá trị mặc định
        if (defined('DB_HOST')) $this->host = DB_HOST;
        if (defined('DB_PORT')) $this->port = DB_PORT;
        if (defined('DB_NAME')) $this->dbname = DB_NAME;
        if (defined('DB_USER')) $this->username = DB_USER;
        if (defined('DB_PASS')) $this->password = DB_PASS;

        try {
            // Chuỗi kết nối luôn có port động, giải quyết triệt để lỗi cổng kết nối của Thành
            $this->pdo = new PDO(
                "mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }
}