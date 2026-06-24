<?php
class TaskModel {
    private $db;

    public function __construct() {
        $databaseInstance = new Database();
        $this->db = $databaseInstance->pdo;
    }

    // Lấy thông tin chi tiết một task bằng ID
    public function getById($id) {
        $sql = "SELECT i.*, 
                       CONCAT(u1.first_name, ' ', u1.last_name) AS assignee_full_name,
                       u1.username AS assignee_username,
                       u1.avatar_url AS assignee_avatar,
                       CONCAT(u2.first_name, ' ', u2.last_name) AS reporter_full_name,
                       u2.username AS reporter_username,
                       p.key AS project_key,
                       p.name AS project_name
                FROM issues i
                LEFT JOIN users u1 ON i.assignee_id = u1.id
                LEFT JOIN users u2 ON i.reporter_id = u2.id
                LEFT JOIN projects p ON i.project_id = p.id
                WHERE i.id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách sub-tasks của một task
    public function getSubtasksByTaskId($taskId) {
        $sql = "SELECT * FROM issues WHERE parent_issue_id = :task_id ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái của task
    public function updateStatus($id, $status) {
        $sql = "UPDATE issues SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    // Lấy toàn bộ task của một dự án cụ thể
    public function getIssuesByProjectId($projectId) {
        $sql = "SELECT i.*, 
                       u.username AS assignee_name, 
                       u.avatar_url AS assignee_avatar
                FROM issues i
                LEFT JOIN users u ON i.assignee_id = u.id
                WHERE i.project_id = :project_id
                ORDER BY i.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tần suất xử lý công việc của các thành viên (Số lượng task được giao)
    public function getTaskFrequency() {
        $sql = "SELECT u.username AS member_name, COUNT(i.id) AS task_count
                FROM users u
                LEFT JOIN issues i ON u.id = i.assignee_id
                GROUP BY u.id, u.username
                ORDER BY task_count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thống kê số lượng người dùng đăng ký theo ngày
    public function getNewUsersStats() {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS reg_date, COUNT(*) AS user_count
                FROM users
                GROUP BY reg_date
                ORDER BY reg_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách công việc được gán cho một user cụ thể (chưa hoàn thành)
    public function getAssignedIssuesByUserId($userId) {
        $sql = "SELECT i.*, p.key AS project_key
                FROM issues i
                LEFT JOIN projects p ON i.project_id = p.id
                WHERE i.assignee_id = :user_id AND i.status != 'done'
                ORDER BY CASE i.priority 
                            WHEN 'highest' THEN 1 
                            WHEN 'high' THEN 2 
                            WHEN 'medium' THEN 3 
                            WHEN 'low' THEN 4 
                            ELSE 5 
                         END, i.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
