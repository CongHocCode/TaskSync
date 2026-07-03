<div class="page-content text-dark">
    
    <!-- Tinh chỉnh hiệu ứng tương tác -->
    <style>
        .interactive-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .interactive-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(112, 126, 174, 0.18) !important;
        }
        .interactive-item {
            transition: background-color 0.2s ease, padding-left 0.2s ease;
            cursor: pointer;
            text-decoration: none !important;
        }
        .interactive-item:hover {
            background-color: rgba(67, 24, 255, 0.04) !important;
            padding-left: 8px !important;
        }
    </style>

    <div class="page-header mb-4">
        <h2 class="fw-bold text-dark">Chào mừng quay lại, <?= htmlspecialchars($_SESSION['user']['display_name'] ?? 'ADMIN') ?>!</h2>
        <p class="text-muted mb-0">Giám sát số liệu thống kê hệ thống, quản lý nhanh các dự án và thành viên của máy chủ.</p>
    </div>

    <!-- 1. Hộp chứa biểu đồ trực quan (Chart.js) -->
    <div class="dashboard-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; display: grid;">
        <div class="app-card p-4 bg-white shadow-sm" style="border-radius: 12px;">
            <h3 class="h6 fw-bold mb-3" style="color: #1a1632 !important;">Tần suất xử lý công việc</h3>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="taskFrequencyChart"></canvas>
            </div>
        </div>

        <div class="app-card p-4 bg-white shadow-sm" style="border-radius: 12px;">
            <h3 class="h6 fw-bold mb-3" style="color: #1a1632 !important;">Thống kê người dùng mới</h3>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="newUsersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 2. Hộp chứa 3 Card đếm số lượng tổng quan hệ thống -->
    <div class="dashboard-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; display: grid;">
        
        <!-- Card 1: Tổng quan Task hệ thống -->
        <div class="app-card p-4 bg-white shadow-sm" style="border-radius: 12px;">
            <h3 class="h6 fw-bold mb-3" style="color: #1a1632 !important;">Tổng quan Task</h3>
            <div class="issues-list">
                <div class="issue-item py-2 border-bottom border-light">
                    <div>
                        <strong class="fs-4 text-dark"><?= htmlspecialchars($data['total_tasks'] ?? 0) ?></strong>
                        <div class="project-desc small text-muted">Tổng công việc hiện có</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Trạng thái dự án hệ thống (Bấm vào nhảy sang trang danh sách dự án của Admin) -->
        <a href="<?= BASE_URL ?>/admin/projects" class="app-card p-4 bg-white shadow-sm text-decoration-none interactive-card" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-start">
                <h3 class="h6 fw-bold mb-3" style="color: #1a1632 !important;">Trạng thái dự án</h3>
                <i class="bi bi-arrow-up-right-circle text-primary fs-5"></i>
            </div>
            <div class="issues-list">
                <div class="issue-item py-1">
                    <div>
                        <strong class="fs-4 text-dark"><?= htmlspecialchars($data['total_projects'] ?? 0) ?></strong>
                        <div class="project-desc small text-muted">Dự án đang hoạt động &rarr;</div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Card 3: Thống kê Nhân sự thật (Bấm vào nhảy thẳng sang trang quản lý nhân sự Admin) -->
        <a href="<?= BASE_URL ?>/admin/users" class="app-card p-4 bg-white shadow-sm text-decoration-none interactive-card" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-start">
                <h3 class="h6 fw-bold mb-3" style="color: #1a1632 !important;">Nhân sự hệ thống</h3>
                <i class="bi bi-arrow-up-right-circle text-primary fs-5"></i>
            </div>
            <div class="issues-list">
                <div class="issue-item py-1 border-bottom border-light d-flex justify-content-between align-items-center">
                    <div>
                        <span class="project-desc small text-muted d-block">Tổng số nhân sự</span>
                        <strong class="text-dark" style="font-size: 1.2rem;"><?= htmlspecialchars($data['total_users'] ?? 0) ?></strong>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded px-2">Users</span>
                </div>
                <div class="issue-item py-1 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="project-desc small text-muted d-block">Tài khoản bị khóa</span>
                        <strong class="text-danger" style="font-size: 1.2rem;"><?= htmlspecialchars($data['blocked_users'] ?? 0) ?></strong>
                    </div>
                    <span class="badge bg-danger-subtle text-danger rounded px-2">Blocked</span>
                </div>
            </div>
        </a>
    </div>

    <!-- 3. Khu vực liên kết chi tiết việc được giao và các dự án của Admin -->
    <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; display: grid;">
        
        <!-- Cột Trái: My Assigned Issues (Công việc của riêng Admin) -->
        <div class="app-card p-4 bg-white shadow-sm" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="h6 fw-bold m-0" style="color: #1a1632 !important;">My Assigned Issues</h3>
                    <p class="project-desc small text-muted m-0" style="color: #4b5563 !important;">Các công việc được giao (Bấm vào đầu việc để xem Kanban).</p>
                </div>
                <span class="badge bg-primary px-3 py-1.5 rounded-pill"><?= count($data['assigned_issues'] ?? []) ?></span>
            </div>
            <div class="issues-list" style="max-height: 250px; overflow-y: auto;">
                <?php if (empty($data['assigned_issues'])): ?>
                    <div class="text-muted text-center py-4 small">
                        <i class="bi bi-journal-check fs-4 d-block mb-1 opacity-50"></i> Bạn chưa được giao công việc nào!
                    </div>
                <?php else: ?>
                    <?php foreach ($data['assigned_issues'] as $task): 
                        $priority = strtoupper($task['priority'] ?? 'MEDIUM');
                        // Click vào sẽ dẫn thẳng đến đúng bảng Kanban của dự án đó
                        $kanbanUrl = BASE_URL . "/project/kanban/" . ($task['project_id_ref'] ?? '');
                    ?>
                        <a href="<?= $kanbanUrl ?>" class="issue-item py-2.5 px-2 border-bottom border-light d-flex justify-content-between align-items-center interactive-item">
                            <div class="d-flex align-items-center gap-2">
                                <span class="issue-id fw-bold text-primary" style="font-size: 0.85rem;"><?= htmlspecialchars($task['issue_key'] ?? '') ?></span>
                                <span class="issue-title text-dark small text-truncate" style="max-width: 250px; font-weight: 500;"><?= htmlspecialchars($task['title'] ?? '') ?></span>
                            </div>
                            <span class="badge px-2 py-1 rounded small <?= strtolower($priority) === 'highest' || strtolower($priority) === 'high' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' ?>" style="font-size: 0.65rem;">
                                <?= $priority ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột Phải: Danh sách Dự án hệ thống mà Admin đang theo dõi -->
        <div class="app-card p-4 bg-white shadow-sm" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="h6 fw-bold m-0" style="color: #1a1632 !important;">Danh sách Dự án hoạt động</h3>
                    <p class="project-desc small text-muted m-0" style="color: #4b5563 !important;">Danh sách các dự án đang chạy (Bấm vào dự án để xem Kanban).</p>
                </div>
                <span class="badge bg-primary px-3 py-1.5 rounded-pill"><?= count($data['projects'] ?? []) ?></span>
            </div>
            <div class="projects-list" style="max-height: 250px; overflow-y: auto;">
                <?php if (empty($data['projects'])): ?>
                    <div class="text-muted text-center py-4 small">
                        <i class="bi bi-folder-symlink fs-4 d-block mb-1 opacity-50"></i> Hệ thống chưa có dự án nào được khởi tạo.
                    </div>
                <?php else: ?>
                    <?php 
                    $limitProjects = array_slice($data['projects'], 0, 5);
                    foreach ($limitProjects as $p): 
                        // Click vào dự án sẽ dẫn thẳng đến đúng bảng Kanban của dự án đó
                        $projKanbanUrl = BASE_URL . "/project/kanban/" . $p['id'];
                    ?>
                        <a href="<?= $projKanbanUrl ?>" class="project-item py-2.5 px-2 border-bottom border-light d-flex justify-content-between align-items-center interactive-item">
                            <div>
                                <span class="badge bg-primary-subtle text-primary fw-bold me-2"><?= htmlspecialchars($p['key'] ?? '') ?></span>
                                <span class="project-name fw-semibold text-dark small" style="font-weight: 600;"><?= htmlspecialchars($p['name'] ?? '') ?></span>
                            </div>
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                <i class="bi bi-person-circle text-secondary me-1"></i> @<?= htmlspecialchars($p['owner_name'] ?? 'System') ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tải thư viện vẽ đồ thị Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Phun dữ liệu định dạng JSON từ PHP sang hằng số toàn cục Window của JavaScript -->
<script>
    window.taskFrequencyData = <?php echo json_encode($data['task_frequency'] ?? []); ?>;
    window.newUsersStatsData = <?php echo json_encode($data['new_users'] ?? []); ?>; 
</script>