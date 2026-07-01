<?php
class ProjectModel
{
    private $db;

    public function __construct()
    {
        $databaseInstance = new Database();

        $this->db = $databaseInstance->pdo;
    }

    // Lấy TẤT CẢ các dự án trên hệ thống 
    public function getAllProjects()
    {
        $sql = "SELECT p.*, u.username as owner_name 
                FROM projects p
                LEFT JOIN users u ON p.owner_id = u.id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách các dự án mà một user cụ thể tham gia (myProjects) chủ yếu cho trang admin
    public function getProjectsByUserId($userId)
    {
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
    public function getProjectById($projectId)
    {
        $sql = "SELECT * FROM projects WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //  Hàm THÊM dự án mới (Dùng transaction để đảm bảo lưu đồng thời vào bảng projects và project_members)
    public function createProject($name, $key, $description, $githubRepoUrl, $ownerId)
    {
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
        } catch (Exception $e) {
            $this->db->rollBack(); // Hoàn tác (hủy bỏ) nếu xảy ra bất kỳ lỗi SQL nào
            return false;
        }
    }

    // Lấy danh sách các dự án kèm theo số lượng thành viên và số lượng công việc mà user tham gia
    public function getProjectsWithCountsByUserId($userId)
    {
        $sql = "SELECT p.*, pm.role, 
                       (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) AS member_count,
                       (SELECT COUNT(*) FROM issues WHERE project_id = p.id) AS issue_count
                FROM projects p 
                JOIN project_members pm ON p.id = pm.project_id 
                WHERE pm.user_id = :user_id AND pm.status = 'active'
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách thành viên tham gia dự án
    public function getProjectMembers($projectId)
    {
        $sql = "SELECT pm.role, pm.status, u.id, u.username, u.first_name, u.last_name, u.avatar_url , u.email
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = :project_id
                ORDER BY pm.role = 'manager' DESC, u.first_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Lấy nhân sự chưa tham gia project để tránh add trùng
    public function getNonMembersOfProject($projectId)
    {
        $sql = "SELECT id, username, first_name, last_name 
                FROM users 
                WHERE status = 'active' AND id NOT IN (
                    SELECT user_id FROM project_members WHERE project_id = :project_id
                )
                ORDER BY first_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchNonMembersOfProject($projectId, $searchTerm)
    {
        $sql = "SELECT id, username, first_name, last_name, avatar_url, email 
                FROM users 
                WHERE status = 'active' 
                  AND (username LIKE :q1 OR email LIKE :q2 OR first_name LIKE :q3 OR last_name LIKE :q4)
                  AND id NOT IN (
                      SELECT user_id FROM project_members WHERE project_id = :project_id
                  )
                ORDER BY first_name ASC 
                LIMIT 10"; // Giới hạn 10 kết quả để bảo vệ tối đa hiệu năng máy chủ và trình duyệt

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'q1' => "%$searchTerm%",
            'q2' => "%$searchTerm%",
            'q3' => "%$searchTerm%",
            'q4' => "%$searchTerm%",
            'project_id' => $projectId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMemberToProject($projectId, $userId, $role = 'member', $invitedBy = null)
    {
        $sql = "INSERT INTO project_members (project_id, user_id, role, invited_by) 
                VALUES (:project_id, :user_id, :role, :invited_by)
                ON DUPLICATE KEY UPDATE role = :role_update, invited_by = :invited_by_update";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'project_id'         => $projectId,
            'user_id'            => $userId,
            'role'               => $role,
            'invited_by'         => $invitedBy,
            'role_update'        => $role,
            'invited_by_update'  => $invitedBy
        ]);
    }

    // Xóa thành viên ra khỏi dự án (member chỉ mất tư cách tham gia dự án các issue cũ được gán sẽ vẫn còn)
    public function removeMemberFromProject($projectId, $userId)
    {
        $sql = "DELETE FROM project_members WHERE project_id = :project_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId
        ]);
    }

    // Lấy danh sách các dự án sắp xếp thông minh theo mức độ hoạt động và thời gian tạo để làm Sidebar mặc định
    public function getProjectsOrderedForSidebar($userId)
    {
        $sql = "SELECT p.*, COUNT(i.id) AS user_task_count
                FROM projects p
                LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.status = 'active'
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

    //Lấy danh sách dự án mà nhân viên cụ thể tham gia
    public function getProjectsByMemberId($userId)
    {
        $sql = "SELECT p.name, p.key, pm.role 
                FROM project_members pm
                JOIN projects p ON pm.project_id = p.id
                WHERE pm.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Lấy SỐ LƯỢNG công việc được giao cho một nhân viên theo trạng thái (TODO, DONE,....) (khác với hàm bên IssueModel là lấy các cột khác)
    public function getMemberTaskStats($userId, $projectId = null)
    {
        $sql = "SELECT status, COUNT(*) as count 
                FROM issues 
                WHERE assignee_id = :user_id";

        $params = ['user_id' => $userId];

        if ($projectId !== null) {
            $sql .= " AND project_id = :project_id";
            $params['project_id'] = $projectId;
        }

        $sql .= " GROUP BY status";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra xem user có quyền quản lý (manager) dự án không
    public function isProjectManager($projectId, $userId)
    {
        $sql = "SELECT role FROM project_members WHERE project_id = :project_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'project_id' => $projectId,
            'user_id' => $userId
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && strtolower($result['role']) === 'manager';
    }

    // Lấy vai trò của user trong dự án
    public function getProjectUserRole($projectId, $userId)
    {
        $sql = "SELECT role FROM project_members WHERE project_id = :project_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'project_id' => $projectId,
            'user_id' => $userId
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? strtolower($result['role']) : null;
    }

    public function isProjectMember($projectId, $userId)
    {
        // Chỉ cần chọn 1 cột bất kỳ và check xem có dòng active nào tồn tại không
        // Một user đã được xem là member nếu đã tồn tại trong bảng này và trạng thái là active
        $sql = "SELECT 1 FROM project_members 
                WHERE project_id = :project_id AND user_id = :user_id AND status = 'active' 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId
        ]);

        $result = $stmt->fetch();

        // Trả về true nếu $result là mảng (tìm thấy), trả về false nếu $result là false (không tìm thấy)
        return (bool)$result;
    }

    // Cập nhật thông tin dự án và đồng bộ key công việc cascade
    public function updateProject($projectId, $name, $key, $description, $githubRepoUrl)
    {
        try {
            $this->db->beginTransaction();

            // Lấy key cũ của project trước để so sánh
            $sqlGetOld = "SELECT `key` FROM projects WHERE id = :id FOR UPDATE";
            $stmtGetOld = $this->db->prepare($sqlGetOld);
            $stmtGetOld->execute(['id' => $projectId]);
            $oldProject = $stmtGetOld->fetch(PDO::FETCH_ASSOC);
            $oldKey = $oldProject ? $oldProject['key'] : '';

            // Cập nhật bảng projects
            $sql = "UPDATE projects 
                    SET `name` = :name, `key` = :key, `description` = :description, `github_repo_url` = :github_repo_url 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id' => $projectId,
                'name' => $name,
                'key' => $key,
                'description' => $description,
                'github_repo_url' => $githubRepoUrl
            ]);

            // Nếu key thay đổi, cập nhật lại issue_key cho các công việc cũ thuộc dự án này
            if ($oldKey !== '' && $oldKey !== $key) {
                // Ví dụ: WEB-12 thành ABC-12
                $sqlUpdateIssues = "UPDATE issues 
                                    SET issue_key = CONCAT(:new_key, '-', SUBSTRING(issue_key, LENGTH(:old_key) + 2)) 
                                    WHERE project_id = :project_id";
                $stmtUpdateIssues = $this->db->prepare($sqlUpdateIssues);
                $stmtUpdateIssues->execute([
                    'new_key' => $key,
                    'old_key' => $oldKey,
                    'project_id' => $projectId
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    //Lấy thống kê số task đã hoàn thành
    public function getProjectStats($projectId)
    {
        $sql = "SELECT
                    COUNT(id) as total_tasks,
                    SUM(CASE WHEN status ='done' THEN 1 ELSE 0 END) as completed_tasks
                FROM issues
                WHERE project_id = :project_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Xóa dự án (Cơ sở dữ liệu tự động ON DELETE CASCADE cho các bảng liên quan)
    public function deleteProject($projectId)
    {
        try {
            $sql = "DELETE FROM projects WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $projectId]);
        } catch (Exception $e) {
            return false;
        }
    }

    //CÁC HÀM XỬ LÝ LỜI MỜI VÀO DỰ ÁN

    // Lấy danh sách lời mời dự án đang chờ của một User
    public function getPendingInvitations($userId)
    {
        $sql = "SELECT pm.project_id, p.name as project_name, p.key as project_key,
                       CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.avatar_url as sender_avatar
                FROM project_members pm
                JOIN projects p ON pm.project_id = p.id
                JOIN users u ON pm.invited_by = u.id -- Lấy chính xác người gửi lời mời thật
                WHERE pm.user_id = :user_id AND pm.status = 'pending'
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Chấp nhận lời mời tham gia dự án
    public function acceptInvitation($projectId, $userId)
    {
        $sql = "UPDATE project_members 
                SET status = 'active' 
                WHERE project_id = :project_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId
        ]);
    }

    // Từ chối lời mời (Xóa bản ghi khỏi bảng trung gian)
    public function declineInvitation($projectId, $userId)
    {
        $sql = "DELETE FROM project_members 
                WHERE project_id = :project_id AND user_id = :user_id AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId
        ]);
    }

    // Lấy danh sách toàn bộ dự án kèm thông tin Owner, số thành viên, số tasks (cho admin)
    public function getAllProjectsWithCounts()
    {
        $sql = "SELECT p.*, 
                       u.first_name, u.last_name, u.username, u.avatar_url,
                       (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) AS member_count,
                       (SELECT COUNT(*) FROM issues WHERE project_id = p.id) AS task_count
                FROM projects p
                LEFT JOIN users u ON p.owner_id = u.id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đổi chủ sở hữu (Owner) của dự án
    public function changeProjectOwner($projectId, $newOwnerId)
    {
        try {
            $this->db->beginTransaction();

            // 1. Cập nhật owner_id trong bảng projects
            $sql = "UPDATE projects SET owner_id = :new_owner_id WHERE id = :project_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'new_owner_id' => $newOwnerId,
                'project_id'   => $projectId
            ]);

            // 2. Đảm bảo new owner là member của dự án với vai trò 'manager' và status 'active'
            $sqlMember = "INSERT INTO project_members (project_id, user_id, role, status) 
                          VALUES (:project_id, :user_id, 'manager', 'active')
                          ON DUPLICATE KEY UPDATE role = 'manager', status = 'active'";
            $stmtMember = $this->db->prepare($sqlMember);
            $stmtMember->execute([
                'project_id' => $projectId,
                'user_id'    => $newOwnerId
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Lấy tất cả thành viên hoạt động của tất cả các dự án, gom nhóm theo project_id
    public function getAllActiveProjectMembersGrouped()
    {
        $sql = "SELECT pm.project_id, u.id as user_id, u.username, u.first_name, u.last_name, u.role
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.status = 'active'
                ORDER BY u.first_name ASC, u.last_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['project_id']][] = $row;
        }
        return $grouped;
    }
}

