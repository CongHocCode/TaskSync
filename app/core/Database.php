<?php
class Database
{
    private $host = "localhost";
    private $port = "3307"; // Thêm cổng 3307 của XAMPP vào đây
    private $dbname = "task_sync";
    private $username = "root";
    private $password = "";
    public $pdo;

    public function __construct()
    {
        try {
            // Đã chèn thêm port=$this->port vào chuỗi kết nối PDO dưới đây
            $this->pdo = new PDO(
                // "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",

                //NOTE CHO THÀNH: Comment dòng trên và bỏ comment dòng dưới để chạy
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
