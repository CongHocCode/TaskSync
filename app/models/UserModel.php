<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Kết nối database qua core
    }

    // Tìm user theo username để đăng nhập
    public function getByUsername($username) {
        $stmt = $this->db->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    // Lấy toàn bộ danh sách (cho trang Admin)
    public function getAll() {
        $stmt = $this->db->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }
}