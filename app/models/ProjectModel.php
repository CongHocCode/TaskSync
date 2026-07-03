<?php
class ProjectModel
{
    private $db;

    public function __construct()
    {
        $databaseInstance = new Database();
        $this->db = $databaseInstance->pdo;
    }

    //THAO TÁC CƠ BẢN VỚI PROJECT
    //Thêm
    public function createProject($name, $key, $description, $githubRepoUrl, $ownerId)
    {
        try {
            $this->db->beginTransaction();

            // 1. Thêm vào bảng projects (Đủ 5 cột và 5 nhãn giữ chỗ)
            $sqlProject = "INSERT INTO projects (`name`, `key`, `description`, `github_repo_url`, `owner_id`) 
                           VALUES (:name, :key, :description, :github_repo_url, :owner_id)";

            $stmt1 = $this->db->prepare($sqlProject);
            $stmt1->execute([
                'name'            => $name,
                'key'             => $key,
                'description'     => $description,
                'github_repo_url' => $githubRepoUrl,
                'owner_id'        => $ownerId
            ]);

            // Lấy ID tự tăng của dự án vừa tạo
            $projectId = $this->db->lastInsertId();

            // 2. Thêm quyền cho người tạo vào bảng project_members (đặt status là active để tránh lỗi manager không vào được project do mình tạo)
            $sqlMember = "INSERT INTO project_members (`project_id`, `user_id`, `role`, `status`) 
                          VALUES (:project_id, :user_id, 'manager', 'active')";

            $stmt2 = $this->db->prepare($sqlMember);
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

    // Lấy thông tin chi tiết theo id
    public function getProjectById($projectId)
    {
        $sql = "SELECT * FROM projects WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật
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

    //CÁC HÀM PHỤC VỤ CHO TRANG CÁ NHÂN
    //Lấy project theo user
    public function getProjectsByUserId($userId)
    {
        $sql = "SELECT p.*, pm.role 
                FROM projects p 
                JOIN project_members pm ON p.id = pm.project_id 
                WHERE pm.user_id = :user_id  AND pm.status = 'active'
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    // Lấy danh sách các dự án sắp xếp theo mức độ hoạt động và thời gian tạo để làm Sidebar mặc định
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

    // QUẢN LÝ THÀNH VIÊN TRONG TỪNG DỰ ÁN
    // Lấy danh sách thành viên
    public function getProjectMembers($projectId)
    {
        $sql = "SELECT pm.role, pm.status, u.id, u.username, u.first_name, u.last_name, u.avatar_url, u.email
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = :project_id
                ORDER BY pm.role = 'manager' DESC, u.first_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy toàn bộ nhân sự chưa tham gia để tránh add trùng
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

    // Tìm kiếm nhân sự bằng từ khóa
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
                LIMIT 10";

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

    // Thêm thành viên mới hoặc cập nhật vai trò/người mời (Hỗ trợ ON DUPLICATE KEY UPDATE)
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

    // Xóa thành viên ra khỏi dự án (Lịch sử công việc cũ vẫn được bảo toàn)
    public function removeMemberFromProject($projectId, $userId)
    {
        $sql = "DELETE FROM project_members WHERE project_id = :project_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId
        ]);
    }

    // Lấy danh sách lời mời dự án đang chờ của một User
    public function getPendingInvitations($userId)
    {
        $sql = "SELECT pm.project_id, p.name as project_name, p.key as project_key,
                       CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.avatar_url as sender_avatar
                FROM project_members pm
                JOIN projects p ON pm.project_id = p.id
                JOIN users u ON pm.invited_by = u.id
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

    // Từ chối lời mời (Xóa bản ghi khỏi bảng liên kết)
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

    // PHÂN QUYỀN VÀ CHỈ SỐ DỰ ÁN
    // Kiểm tra xem user có quyền quản lý (Manager) dự án không
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

    // Kiểm tra xem user có phải là thành viên hoạt động (Active Member) của dự án không
    public function isProjectMember($projectId, $userId)
    {
        $sql = "SELECT 1 FROM project_members 
                WHERE project_id = :project_id AND user_id = :user_id AND status = 'active' 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId
        ]);

        $result = $stmt->fetch();
        return (bool)$result;
    }

    // Lấy thống kê số task đã hoàn thành và tổng task (Dùng vẽ thanh tiến độ sức khỏe)
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

    // Lấy danh sách dự án mà một nhân viên cụ thể tham gia
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

    // Thống kê số lượng công việc được giao cho nhân viên theo trạng thái
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

    //HÀM CHUYÊN DỤNG CHO ADMIN
    // Lấy TẤT CẢ các dự án trên hệ thống kèm tên Owner
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

    // Lấy danh sách dự án hệ thống có phân trang, tìm kiếm và tự động gom thống kê
    public function getProjectsPaginatedAndSearched($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT p.*, u.username as owner_name, u.first_name, u.last_name, u.avatar_url,
                       (SELECT COUNT(*) FROM project_members WHERE project_id = p.id AND status = 'active') AS member_count,
                       (SELECT COUNT(*) FROM issues WHERE project_id = p.id) AS issue_count
                FROM projects p
                LEFT JOIN users u ON p.owner_id = u.id
                WHERE 1=1"; // Giữ mệnh đề 1=1 để nối AND tìm kiếm động an toàn

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (p.name LIKE ? OR p.key LIKE ? OR p.description LIKE ? OR u.username LIKE ?)";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam, $searchParam];
        }

        $sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);

        $paramIdx = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIdx++, $param, PDO::PARAM_STR);
        }
        $stmt->bindValue($paramIdx++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIdx++, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectCountByUserId($userId)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM projects p 
                JOIN project_members pm ON p.id = pm.project_id 
                WHERE pm.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }
    
    // Đếm tổng số lượng dự án tìm được để tính toán số trang cho Admin
    public function getProjectsCountForSearch($search = '')
    {
        $sql = "SELECT COUNT(*) FROM projects p LEFT JOIN users u ON p.owner_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.name LIKE ? OR p.key LIKE ? OR p.description LIKE ? OR u.username LIKE ?)";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam, $searchParam];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    // Đếm thuần số lượng tất cả dự án cho biểu đồ/card Dashboard Admin
    public function getTotalProjectsCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM projects");
        return $stmt->fetchColumn();
    }

    // Thay đổi Trưởng dự án (Owner) và phân quyền tương thích
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
        $sql = "SELECT pm.role, u.id, u.username, u.first_name, u.last_name, u.avatar_url, pm.project_id 
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.status = 'active'
                ORDER BY pm.role = 'manager' DESC, u.first_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($members as $m) {
            $grouped[$m['project_id']][] = $m;
        }
        return $grouped;
    }
}
