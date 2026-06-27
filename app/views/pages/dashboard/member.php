<div class="page-content">
    <div class="page-header mb-4">
        <?php
        // Lấy tên đầu tiên của người dùng để chào mừng
        $fullName = $_SESSION['user']['display_name'] ?? 'Sarah';
        $parts = explode(' ', $fullName);
        $firstName = end($parts); // Lấy phần tên cuối làm tên gọi
        ?>
        <h2>Chào mừng quay lại, <?= htmlspecialchars($firstName) ?>!</h2>
        <p>Giám sát nhanh các Task và Dự án của bạn hàng ngày.</p>
    </div>
    <style>
        .priority-badge.highest {
            background: #fee2e2 !important;
            color: #ef4444 !important;
        }
        .priority-badge.high {
            background: #fef3c7 !important;
            color: #d97706 !important;
        }
        .priority-badge.medium {
            background: #dbeafe !important;
            color: #2563eb !important;
        }
        .priority-badge.low {
            background: #f3f4f6 !important;
            color: #4b5563 !important;
        }
        
        .member-project-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            background: #ffffff;
            border: 1px solid rgba(11, 18, 32, 0.05);
            box-shadow: 0 4px 12px rgba(11, 18, 32, 0.01);
            transition: all 0.2s ease;
        }
        .member-project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(11, 18, 32, 0.04);
            border-color: rgba(11, 18, 32, 0.1);
        }
        
        .project-role-tag {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .role-manager {
            background: #f3e8ff;
            color: #7c3aed;
        }
        .role-member {
            background: #e0e7ff;
            color: #4f46e5;
        }
        .role-viewer {
            background: #f3f4f6;
            color: #6b7280;
        }
        .issue-item {
    transition: all 0.2s ease-in-out; 
    cursor: pointer; 
}

.issue-item:hover {
    background: #f1f1f5 !important; 
    border-color: rgba(11, 18, 32, 0.15) !important; 
    transform: translateY(-2px); 
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); 
}
    </style>
    <div class="row g-4">
        <!-- Cột trái: My Assigned Issues -->
        <div class="col-lg-6 col-md-12">
            <div class="app-card h-100 mb-0" style="background: #fff; border: 1px solid rgba(11, 18, 32, 0.06); box-shadow: 0 12px 30px rgba(11, 18, 32, 0.06);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="color: #1a1632 !important; font-weight: 700 !important; margin: 0; font-size: 1.2rem;">My Assigned Issues (<?= count($data['assigned_issues'] ?? []) ?>)</h3>
                    <a href="<?= BASE_URL ?>/task/myTasks" class="text-decoration-none" style="color: #7c3aed; font-weight: 700; font-size: 0.9rem; transition: color 0.2s;">Xem tất cả</a>
                </div>
                <p style="color: #6b7280; font-size: 0.85rem; margin-top: -8px; margin-bottom: 1.5rem;">Các công việc đang được gán cho tài khoản cá nhân</p>
                <div class="issues-list">
                    <?php if (empty($data['assigned_issues'])): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-journal-check" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Bạn chưa được giao công việc nào!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data['assigned_issues'] as $issue): ?>
                            <div class="issue-item" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-radius: 14px; margin-bottom: 0.75rem; background: #fbfbfd; border: 1px solid rgba(11, 18, 32, 0.04);">
                                <div style="display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 0;">
                                    <span class="issue-id" style="font-weight: 700; color: #7c3aed; font-size: 0.9rem; min-width: 70px;"><?= htmlspecialchars($issue['issue_key']) ?></span>
                                    <span class="issue-title" style="font-size: 0.9rem; font-weight: 600; color: #1a1632; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;"><?= htmlspecialchars($issue['title']) ?></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem; margin-left: 1rem;">
                                    <span class="priority-badge <?= htmlspecialchars(strtolower($issue['priority'])) ?>" style="padding: 0.3rem 0.75rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; text-transform: capitalize;">
                                        <?= htmlspecialchars($issue['priority']) ?>
                                    </span>
                                    <span class="due-date" style="font-size: 0.8rem; color: #9ca3af; white-space: nowrap; font-weight: 600;">
                                        <?= date('Y-m-d', strtotime($issue['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Cột phải: Danh sách Dự án -->
        <div class="col-lg-6 col-md-12">
            <div class="app-card h-100 mb-0" style="background: #fff; border: 1px solid rgba(11, 18, 32, 0.06); box-shadow: 0 12px 30px rgba(11, 18, 32, 0.06);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="color: #1a1632 !important; font-weight: 700 !important; margin: 0; font-size: 1.2rem;">Danh sách Dự án (<?= count($data['my_projects'] ?? []) ?>)</h3>
                </div>
                <p style="color: #6b7280; font-size: 0.85rem; margin-top: -8px; margin-bottom: 1.5rem;">Các không gian dự án bạn đang tham gia</p>
                <div class="projects-list">
                    <?php if (empty($data['my_projects'])): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Bạn chưa tham gia dự án nào!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data['my_projects'] as $project): ?>
                            <?php
                            $roleClass = 'role-member';
                            $roleLabel = 'MEMBER';
                            if ($project['role'] === 'manager') {
                                $roleClass = 'role-manager';
                                $roleLabel = 'PROJECT MANAGER';
                            } elseif ($project['role'] === 'viewer') {
                                $roleClass = 'role-viewer';
                                $roleLabel = 'VIEWER';
                            }
                            ?>
                            <div class="member-project-card">
                                <div style="display: flex; flex-direction: column; gap: 0.35rem; flex: 1; min-width: 0; padding-right: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                        <span class="project-name" style="font-weight: 700; font-size: 0.75rem; color: #4b5563; text-transform: uppercase; background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 6px; letter-spacing: 0.02em;"><?= htmlspecialchars($project['key']) ?></span>
                                        <span class="project-role-tag <?= $roleClass ?>"><?= $roleLabel ?></span>
                                    </div>
                                    <h4 style="margin: 0.5rem 0 0.15rem 0; font-size: 0.95rem; font-weight: 700; color: #1a1632;"><a href="<?= BASE_URL ?>/project/kanban/<?= $project['id'] ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($project['name']) ?></a></h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;"><?= htmlspecialchars($project['description'] ?? '') ?></p>
                                </div>
                                <div style="font-size: 0.75rem; color: #9ca3af; font-weight: 700; white-space: nowrap; text-align: right; align-self: center;">
                                    <?= $project['member_count'] ?> ms / <?= $project['issue_count'] ?> issues
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>