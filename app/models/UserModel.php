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

    // Cập nhật mật khẩu mới (Bảo mật bằng cách kiểm tra mật khẩu cũ)
    public function updatePassword($userId, $currentPassword, $newPassword)
    {
        // Lấy hash mật khẩu hiện tại từ CSDL để so sánh
        $sqlSelect = "SELECT password_hash FROM users WHERE id = :id";
        $stmtSelect = $this->db->pdo->prepare($sqlSelect);
        $stmtSelect->execute(['id' => $userId]);
        $user = $stmtSelect->fetch();

        // Nếu mật khẩu cũ nhập vào khớp với CSDL
        if ($user && password_verify($currentPassword, $user['password_hash'])) {
            // Băm mật khẩu mới và cập nhật
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sqlUpdate = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
            $stmtUpdate = $this->db->pdo->prepare($sqlUpdate);
            return $stmtUpdate->execute([
                'id' => $userId,
                'password_hash' => $newPasswordHash
            ]);
        }
        return false; // Trả về false nếu mật khẩu cũ không khớp
    }

    public function updateAvatar($id, $avatarUrl) {
        $stmt = $this->db->pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
        return $stmt->execute([$avatarUrl, $id]);
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

    // Xóa nhân sự và giải quyết các khóa ngoại liên quan
    public function delete($id, $adminId)
    {
        try {
            $this->db->pdo->beginTransaction();

            // 1. Cập nhật assignee_id của các task được giao cho user này thành NULL
            $stmt1 = $this->db->pdo->prepare("UPDATE issues SET assignee_id = NULL WHERE assignee_id = ?");
            $stmt1->execute([$id]);

            // 2. Cập nhật reporter_id của các task do user này tạo thành Admin thực hiện xóa
            $stmt2 = $this->db->pdo->prepare("UPDATE issues SET reporter_id = ? WHERE reporter_id = ?");
            $stmt2->execute([$adminId, $id]);

            // 3. Xóa user (nút cascade sẽ tự động xóa các bản ghi liên quan ở projects, project_members, comments)
            $stmt3 = $this->db->pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt3->execute([$id]);

            $this->db->pdo->commit();
            return $result;
        } catch (Exception $e) {
            if ($this->db->pdo->inTransaction()) {
                $this->db->pdo->rollBack();
            }
            return false;
        }
    }
}
