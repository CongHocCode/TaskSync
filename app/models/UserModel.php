<?php
class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(); // Kết nối database qua core
    }

    public function getById($id)
    {
        $stmt = $this->db->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, role = ? WHERE id = ?";
        $stmt = $this->db->pdo->prepare($sql);
        return $stmt->execute([
            $data['username'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['role'],
            $id
        ]);
    }


    public function updateStatus($id, $status)
    {
        $stmt = $this->db->pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getByUsernameOrEmail($input)
    {
        // Tìm kiếm đồng thời ở cả 2 cột username và email
        $stmt = $this->db->pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$input, $input]);
        return $stmt->fetch();
    }

    // Kiểm tra xem Username hoặc Email đã có ai dùng chưa
    public function checkExists($username, $email)
    {
        $stmt = $this->db->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() ? true : false;
    }

    // Hàm tạo tài khoản mới (Mặc định đăng ký mới là role 'user')
    public function create($data)
    {
        $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, 'user')";
        $stmt = $this->db->pdo->prepare($sql);
        return $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['first_name'],
            $data['last_name']
        ]);
    }

    // Lấy toàn bộ danh sách (cho trang Admin)
    public function getAll()
    {
        $stmt = $this->db->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }
}
