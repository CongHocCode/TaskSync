<section class="task-list-page container-fluid py-4">
    <style>
        .task-title-link {
            transition: color 0.15s ease-in-out;
            cursor: pointer;
            color: #1e293b;
        }
        .task-title-link:hover {
            color: #4f46e5 !important;
        }
        .work-badge {
            background-color: #f1f5f9;
            color: #4f46e5;
            border: 1px solid #e2e8f0;
            font-family: var(--bs-font-monospace);
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
        }
        .badge-priority {
            font-size: 0.7rem;
            font-weight: 800;
            border-radius: 6px;
            padding: 3px 8px;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-status {
            font-size: 0.7rem;
            font-weight: 800;
            border-radius: 6px;
            padding: 3px 8px;
            text-transform: uppercase;
            display: inline-block;
        }
        .table-hover tbody tr {
            transition: background-color 0.15s ease-in-out;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc !important;
            cursor: pointer;
        }
        .avatar-img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-2 fw-bold text-dark d-flex align-items-center">
                Danh sách công việc
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Xem tất cả các công việc dưới dạng danh sách bảng.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-bold text-uppercase me-1" style="font-size: 0.8rem; letter-spacing: 0.05em;">Lọc theo:</span>
            <select id="filterAssigneeList" class="form-select form-select-sm w-auto bg-white border border-secondary-subtle text-dark px-3 rounded-pill fw-medium" style="min-width: 160px; cursor: pointer; transition: all 0.2s;">
                <option value="all">Mọi người</option>
                <?php foreach (($data['members'] ?? []) as $member): 
                    $fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
                    $displayName = !empty($fullName) ? $fullName : $member['username'];
                ?>
                    <option value="<?= htmlspecialchars($member['username']) ?>"><?= htmlspecialchars($displayName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="card border border-light-subtle rounded-4 shadow-sm overflow-hidden" style="background-color: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 1000px;">
                    <thead class="bg-light-subtle border-bottom border-light-subtle">
                        <tr>
                            <th class="py-3 px-4 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 10%;">Work</th>
                            <th class="py-3 px-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 35%;">Tóm tắt (Title)</th>
                            <th class="py-3 px-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 15%;">Assignee</th>
                            <th class="py-3 px-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 15%;">Reporter</th>
                            <th class="py-3 px-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 10%;">Priority</th>
                            <th class="py-3 px-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 10%;">Status</th>
                            <th class="py-3 px-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 12%;">Created Date</th>
                            <th class="py-3 px-4 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem; width: 12%;">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['tasks'])): ?>
                            <?php foreach ($data['tasks'] as $task): 
                                // Gán tên và avatar Assignee
                                $assigneeName = !empty(trim($task['assignee_full_name'] ?? '')) ? $task['assignee_full_name'] : ($task['assignee_username'] ?? 'Unassigned');
                                $assigneeId = $task['assignee_id'] ?? 0;
                                $avatarColors = ['06b6d4', 'f59e0b', '8b5cf6', '10b981', 'ec4899', '3b82f6'];
                                $assigneeBg = $assigneeId ? $avatarColors[$assigneeId % count($avatarColors)] : '64748b';
                                $assigneeAvatar = !empty($task['assignee_avatar']) && $task['assignee_avatar'] !== 'default-avatar.png'
                                    ? BASE_URL . '/uploads/avatars/' . $task['assignee_avatar']
                                    : "https://ui-avatars.com/api/?name=" . urlencode($assigneeName) . "&background=" . $assigneeBg . "&color=fff";

                                // Gán tên và avatar Reporter
                                $reporterName = !empty(trim($task['reporter_full_name'] ?? '')) ? $task['reporter_full_name'] : ($task['reporter_username'] ?? 'Unassigned');
                                $reporterId = $task['reporter_id'] ?? 0;
                                $reporterBg = $reporterId ? $avatarColors[$reporterId % count($avatarColors)] : '64748b';
                                $reporterAvatar = !empty($task['reporter_avatar']) && $task['reporter_avatar'] !== 'default-avatar.png'
                                    ? BASE_URL . '/uploads/avatars/' . $task['reporter_avatar']
                                    : "https://ui-avatars.com/api/?name=" . urlencode($reporterName) . "&background=" . $reporterBg . "&color=fff";

                                // Định dạng độ ưu tiên
                                $priority = strtoupper($task['priority'] ?? 'MEDIUM');
                                if ($priority === 'HIGHEST') {
                                    $priorityBg = '#ffbdad';
                                    $priorityColor = '#bf2600';
                                } elseif ($priority === 'HIGH') {
                                    $priorityBg = '#ffebe6';
                                    $priorityColor = '#de350b';
                                } elseif ($priority === 'MEDIUM') {
                                    $priorityBg = '#fff0b3';
                                    $priorityColor = '#5e4310';
                                } else { // LOW
                                    $priorityBg = '#e3fcef';
                                    $priorityColor = '#006644';
                                }

                                // Định dạng trạng thái
                                $status = strtolower($task['status'] ?? 'todo');
                                if ($status === 'done') {
                                    $statusBg = '#dcfce7';
                                    $statusColor = '#15803d';
                                    $statusLabel = 'DONE';
                                } elseif ($status === 'in_progress') {
                                    $statusBg = '#e0f2fe';
                                    $statusColor = '#0369a1';
                                    $statusLabel = 'IN PROGRESS';
                                } elseif ($status === 'in_review') {
                                    $statusBg = '#faf5ff';
                                    $statusColor = '#7e22ce';
                                    $statusLabel = 'IN REVIEW';
                                } else { // todo
                                    $statusBg = '#f1f5f9';
                                    $statusColor = '#475569';
                                    $statusLabel = 'TO DO';
                                }

                                $createdAt = date('Y-m-d', strtotime($task['created_at']));
                                $dueDate = !empty($task['due_date']) ? date('d/m/Y H:i', strtotime($task['due_date'])) : '';
                            ?>
                                <tr class="task-row" data-id="<?= $task['id'] ?>" data-assignee="<?= htmlspecialchars($task['assignee_username'] ?? '') ?>" onclick="window.openTaskDetailModal(<?= $task['id'] ?>)">
                                    <td class="py-3 px-4">
                                        <span class="work-badge"><?= htmlspecialchars($task['issue_key']) ?></span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="task-title-link fw-semibold">
                                            <?= htmlspecialchars($task['title']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= $assigneeAvatar ?>" class="avatar-img" alt="Assignee">
                                            <span class="text-dark small fw-medium"><?= htmlspecialchars($assigneeName) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= $reporterAvatar ?>" class="avatar-img" alt="Reporter">
                                            <span class="text-muted small"><?= htmlspecialchars($reporterName) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="badge-priority" style="background-color: <?= $priorityBg ?>; color: <?= $priorityColor ?>;">
                                            <?= $priority ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="badge-status" style="background-color: <?= $statusBg ?>; color: <?= $statusColor ?>;">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-muted small">
                                        <?= $createdAt ?>
                                    </td>
                                    <td class="py-3 px-4 text-muted small">
                                        <?= !empty($dueDate) ? $dueDate : '<span class="text-light-emphasis">-</span>' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Không có công việc nào trong dự án này.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal chứa chi tiết công việc chuẩn Jira-style -->
    <?php require_once __DIR__ . '/../../partials/task_modal_right.php'; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('filterAssigneeList');
    
    if (filterSelect) {
        filterSelect.addEventListener('change', function () {
            const selectedVal = this.value;
            const rows = document.querySelectorAll('.task-row');
            
            rows.forEach(row => {
                if (selectedVal === 'all') {
                    row.style.display = '';
                } else {
                    const assignee = row.getAttribute('data-assignee');
                    if (assignee === selectedVal) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }

    // Lắng nghe sự kiện click trên toàn bộ bảng công việc
    const taskTableBody = document.querySelector('.table-hover tbody');
    if (taskTableBody) {
        taskTableBody.addEventListener('click', function (e) {
            // Tìm ngược lên thẻ tr gần nhất có class .task-row
            const row = e.target.closest('.task-row');
            if (!row) return;

            // Lấy ID chuẩn xác từ thuộc tính data-id của hàng
            const taskId = row.getAttribute('data-id');
            if (taskId && window.openTaskDetailModal) {
                window.openTaskDetailModal(parseInt(taskId, 10));
            }
        });
    }
});
</script>
