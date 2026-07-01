<?php
class TaskModel
{
    private $db;

    public function __construct()
    {
        $databaseInstance = new Database();
        $this->db = $databaseInstance->pdo;
    }

    // Lấy thông tin chi tiết một task bằng ID
    public function getById($id)
    {
        $sql = "SELECT i.*, 
                       CONCAT(u1.first_name, ' ', u1.last_name) AS assignee_full_name,
                       u1.username AS assignee_username,
                       u1.avatar_url AS assignee_avatar,
                       CONCAT(u2.first_name, ' ', u2.last_name) AS reporter_full_name,
                       u2.username AS reporter_username,
                       p.key AS project_key,
                       p.name AS project_name,
                       pi.issue_key AS parent_issue_key,
                       pi.title AS parent_title
                FROM issues i
                LEFT JOIN users u1 ON i.assignee_id = u1.id
                LEFT JOIN users u2 ON i.reporter_id = u2.id
                LEFT JOIN projects p ON i.project_id = p.id
                LEFT JOIN issues pi ON i.parent_issue_id = pi.id
                WHERE i.id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách sub-tasks của một task
    public function getSubtasksByTaskId($taskId)
    {
        $sql = "SELECT * FROM issues WHERE parent_issue_id = :task_id ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái của task
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE issues SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    public function createIssue($data)
    {
        try {
            // Bắt đầu Transaction để bảo toàn tính toàn vẹn dữ liệu
            $this->db->beginTransaction();

            // 1. Tăng counter của dự án lên 1
            $sqlCounter = "UPDATE projects SET issue_counter = issue_counter + 1 WHERE id = :project_id";
            $stmtCounter = $this->db->prepare($sqlCounter);
            $stmtCounter->execute(['project_id' => $data['project_id']]);

            // 2. Lấy mã Key và Counter mới của dự án
            $sqlProj = "SELECT `key`, issue_counter FROM projects WHERE id = :project_id";
            $stmtProj = $this->db->prepare($sqlProj);
            $stmtProj->execute(['project_id' => $data['project_id']]);
            $project = $stmtProj->fetch();

            // Ghép mã (ví dụ: TS-12)
            $issueKey = $project['key'] . '-' . $project['issue_counter'];

            // 3. Thực hiện lưu Task mới vào bảng issues
            $sqlInsert = "INSERT INTO issues (project_id, parent_issue_id, issue_key, title, description, type, status, priority, reporter_id, assignee_id, due_date, created_at) 
                      VALUES (:project_id, :parent_issue_id, :issue_key, :title, :description, :type, 'todo', :priority, :reporter_id, :assignee_id, :due_date, NOW())";

            $stmtInsert = $this->db->prepare($sqlInsert);
            $stmtInsert->execute([
                'project_id' => $data['project_id'],
                'parent_issue_id' => $data['parent_issue_id'] ?? null,
                'issue_key'  => $issueKey,
                'title'      => $data['title'],
                'description' => $data['description'],
                'type'       => $data['type'],
                'priority'   => $data['priority'],
                'reporter_id' => $data['reporter_id'], // Thường là ID của chính User đang đăng nhập
                'assignee_id' => $data['assignee_id'],
                'due_date'   => $data['due_date'] ?? null
            ]);

            // Hoàn tất lưu mọi thay đổi
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Có lỗi xảy ra, hoàn tác lại toàn bộ để tránh sai lệch dữ liệu
            $this->db->rollBack();
            file_put_contents('C:\xampp\htdocs\TaskSync\debug.log', date('Y-m-d H:i:s') . " - CreateIssue Error: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    //Xóa task
    public function deleteTask($taskId)
    {
        $sql = "DELETE FROM issues WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $taskId]);
    }

    // Lấy toàn bộ task của một dự án cụ thể kèm thông tin người được gán và người tạo
    public function getIssuesByProjectId($projectId)
    {
        $sql = "SELECT i.*, 
                       CONCAT(u1.first_name, ' ', u1.last_name) AS assignee_full_name,
                       u1.username AS assignee_username,
                       u1.avatar_url AS assignee_avatar,
                       u1.first_name AS assignee_first,
                       u1.last_name AS assignee_last,
                       CONCAT(u2.first_name, ' ', u2.last_name) AS reporter_full_name,
                       u2.username AS reporter_username,
                       u2.avatar_url AS reporter_avatar,
                       u2.first_name AS reporter_first,
                       u2.last_name AS reporter_last
                FROM issues i
                LEFT JOIN users u1 ON i.assignee_id = u1.id
                LEFT JOIN users u2 ON i.reporter_id = u2.id
                WHERE i.project_id = :project_id
                ORDER BY i.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy ID dự án của một Task cụ thể
    public function getProjectIdByTaskId($taskId)
    {
        $sql = "SELECT project_id FROM issues WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $taskId]);
        return $stmt->fetchColumn(); // Trả về con số ID dự án
    }

    // Lấy tổng số task
    public function getTotalTasksCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM issues");
        return $stmt->fetchColumn();
    }

    // Lấy số task theo người thực hiện
    public function getSystemTaskFrequency()
    {
        $sql = "SELECT CONCAT(u.first_name, ' ', u.last_name) as member_name, COUNT(i.id) as task_count 
                FROM issues i 
                JOIN users u ON i.assignee_id = u.id 
                GROUP BY i.assignee_id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách công việc được gán cho một user (chưa hoàn thành - dùng cho Dashboard)
    public function getAssignedIssuesByUserId($userId)
    {
        $sql = "SELECT i.*, 
                       p.key AS project_key,
                       p.name AS project_name,
                       CONCAT(u2.first_name, ' ', u2.last_name) AS reporter_full_name,
                       u2.username AS reporter_username,
                       u2.avatar_url AS reporter_avatar
                FROM issues i
                LEFT JOIN projects p ON i.project_id = p.id
                LEFT JOIN users u2 ON i.reporter_id = u2.id
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

    // Lấy TẤT CẢ công việc của user (kể cả done - dùng cho trang My Tasks đầy đủ)
    public function getAllIssuesByUserId($userId)
    {
        $sql = "SELECT i.*, 
                       p.key AS project_key,
                       p.name AS project_name,
                       p.id AS project_id_ref,
                       CONCAT(u2.first_name, ' ', u2.last_name) AS reporter_full_name,
                       u2.username AS reporter_username,
                       u2.avatar_url AS reporter_avatar
                FROM issues i
                LEFT JOIN projects p ON i.project_id = p.id
                LEFT JOIN users u2 ON i.reporter_id = u2.id
                WHERE i.assignee_id = :user_id
                ORDER BY CASE i.status
                            WHEN 'in_progress' THEN 1
                            WHEN 'in_review' THEN 2
                            WHEN 'todo' THEN 3
                            WHEN 'done' THEN 4
                            ELSE 5
                         END,
                         CASE i.priority 
                            WHEN 'highest' THEN 1 
                            WHEN 'high' THEN 2 
                            WHEN 'medium' THEN 3 
                            WHEN 'low' THEN 4 
                            ELSE 5 
                         END,
                         i.due_date ASC, i.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTaskAssignee($taskId, $assigneeId)
    {
        $sql = "UPDATE issues SET assignee_id = :assignee_id, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'assignee_id' => $assigneeId,
            'id' => $taskId
        ]);
    }

    // Cập nhật hạn hoàn thành của task
    public function updateDueDate($taskId, $dueDate)
    {
        $sql = "UPDATE issues SET due_date = :due_date, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'due_date' => $dueDate ?: null,
            'id' => $taskId
        ]);
    }

    // Lấy danh sách bình luận của một task kèm thông tin user
    public function getCommentsByTaskId($taskId)
    {
        $sql = "SELECT c.*, 
                       CONCAT(u.first_name, ' ', u.last_name) AS user_full_name,
                       u.username AS username,
                       u.avatar_url AS avatar_url
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.issue_id = :task_id
                ORDER BY c.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm bình luận mới cho task
    public function addComment($taskId, $userId, $content)
    {
        $sql = "INSERT INTO comments (issue_id, user_id, content, created_at) VALUES (:task_id, :user_id, :content, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'task_id' => $taskId,
            'user_id' => $userId,
            'content' => $content
        ]);
    }

    // Lấy chi tiết một bình luận vừa tạo
    public function getCommentById($id)
    {
        $sql = "SELECT c.*, 
                       CONCAT(u.first_name, ' ', u.last_name) AS user_full_name,
                       u.username AS username,
                       u.avatar_url AS avatar_url
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy ID tự tăng của bình luận vừa chèn
    public function getLastInsertedId()
    {
        return $this->db->lastInsertId();
    }

    // Cập nhật loại công việc (type)
    public function updateType($taskId, $type)
    {
        $sql = "UPDATE issues SET type = :type, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'type' => $type,
            'id' => $taskId
        ]);
    }
}
