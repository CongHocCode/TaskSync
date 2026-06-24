<?php
class ProjectModel {
    private $db;

    public function __construct() {
        $databaseInstance = new Database();

        $this->db = $databaseInstance->pdo; 
    }

    // Lấy TẤT CẢ các dự án trên hệ thống 
    public function getAllProjects() {
        $sql = "SELECT p.*, u.username as owner_name 
                FROM projects p
                LEFT JOIN users u ON p.owner_id = u.id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách các dự án mà một user cụ thể tham gia (myProjects) chủ yếu cho trang admin
    public function getProjectsByUserId($userId) {
        $sql = "SELECT p.*, pm.role 
                FROM projects p 
                JOIN project_members pm ON p.id = pm.project_id 
                WHERE pm.user_id = :user_id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin chi tiết của một dự án cụ thể theo ID (Dùng cho Kanban, List, Members...)
    public function getProjectById($projectId) {
        $sql = "SELECT * FROM projects WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //  Hàm THÊM dự án mới (Dùng transaction để đảm bảo lưu đồng thời vào bảng projects và project_members)
    public function createProject($name, $key, $description, $githubRepoUrl, $ownerId) {
    try {
        $this->db->beginTransaction(); 

        // 1. Thêm vào bảng projects (Đủ 5 cột và 5 nhãn giữ chỗ)
        $sqlProject = "INSERT INTO projects (`name`, `key`, `description`, `github_repo_url`, `owner_id`) 
                       VALUES (:name, :key, :description, :github_repo_url, :owner_id)";
        
        $stmt1 = $this->db->prepare($sqlProject);
        
        // Đếm kỹ: Mảng này phải có đúng 5 key tương ứng với 5 nhãn ở trên
        $stmt1->execute([
            'name'            => $name,
            'key'             => $key,
            'description'     => $description,
            'github_repo_url' => $githubRepoUrl,
            'owner_id'        => $ownerId
        ]);

        // Lấy ID tự tăng của dự án vừa tạo
        $projectId = $this->db->lastInsertId();

        // 2. Thêm quyền cho người tạo vào bảng project_members (Đủ 2 nhãn)
        $sqlMember = "INSERT INTO project_members (`project_id`, `user_id`, `role`) 
                      VALUES (:project_id, :user_id, 'manager')";
        
        $stmt2 = $this->db->prepare($sqlMember);
        
        // Mảng này phải có đúng 2 key tương ứng với 2 nhãn ở trên
        $stmt2->execute([
            'project_id' => $projectId,
            'user_id'    => $ownerId
        ]);

        $this->db->commit(); 
        return true;

    } 
    catch (Exception $e) {
            $this->db->rollBack(); // Hoàn tác (hủy bỏ) nếu xảy ra bất kỳ lỗi SQL nào
            return false;
        }
    }

    // Lấy danh sách các dự án kèm theo số lượng thành viên và số lượng công việc mà user tham gia
    public function getProjectsWithCountsByUserId($userId) {
        $sql = "SELECT p.*, pm.role, 
                       (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) AS member_count,
                       (SELECT COUNT(*) FROM issues WHERE project_id = p.id) AS issue_count
                FROM projects p 
                JOIN project_members pm ON p.id = pm.project_id 
                WHERE pm.user_id = :user_id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách thành viên tham gia dự án
    public function getProjectMembers($projectId) {
        $sql = "SELECT pm.role, u.id, u.username, u.first_name, u.last_name, u.avatar_url 
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = :project_id
                ORDER BY pm.role = 'manager' DESC, u.first_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách các dự án sắp xếp thông minh theo mức độ hoạt động và thời gian tạo để làm Sidebar mặc định
    public function getProjectsOrderedForSidebar($userId) {
        $sql = "SELECT p.*, COUNT(i.id) AS user_task_count
                FROM projects p
                LEFT JOIN project_members pm ON p.id = pm.project_id
                LEFT JOIN issues i ON p.id = i.project_id AND i.assignee_id = :user_id
                WHERE pm.user_id = :user_id_member OR p.owner_id = :owner_id
                GROUP BY p.id
                ORDER BY user_task_count DESC, p.created_at ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'user_id_member' => $userId,
            'owner_id' => $userId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}