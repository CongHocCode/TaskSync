<?php
$tasks  = $data['tasks']  ?? [];
$stats  = $data['stats']  ?? ['total' => 0, 'todo' => 0, 'in_progress' => 0, 'in_review' => 0, 'done' => 0];
$user   = $_SESSION['user'] ?? [];
$firstName = $user['display_name'] ?? ($user['username'] ?? 'Bạn');
$parts = explode(' ', $firstName);
$shortName = end($parts);
?>

<section class="my-tasks-page container-fluid py-4">
<style>
    /* -------- Stat Cards -------- */
    .stat-card {
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.09) !important;
    }
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    .stat-card .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-top: 4px;
    }

    /* -------- Filter Tabs -------- */
    .filter-tab-group .filter-tab {
        background: #f1f5f9;
        border: 1.5px solid transparent;
        border-radius: 10px;
        padding: 6px 16px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.18s;
        white-space: nowrap;
    }
    .filter-tab-group .filter-tab:hover,
    .filter-tab-group .filter-tab.active {
        background: #4f46e5;
        color: #fff;
        border-color: #4f46e5;
    }

    /* -------- Task Rows -------- */
    .task-item {
        border-radius: 14px;
        background: #ffffff;
        border: 1.5px solid #f1f5f9;
        padding: 1rem 1.25rem;
        transition: border-color 0.18s, box-shadow 0.18s, transform 0.18s;
        cursor: pointer;
    }
    .task-item:hover {
        border-color: #c7d2fe;
        box-shadow: 0 4px 18px rgba(79, 70, 229, 0.07);
        transform: translateX(3px);
    }
    .task-item.done-task {
        background: #f8fafc;
        opacity: 0.75;
    }

    /* -------- Issue Key Badge -------- */
    .issue-key-badge {
        font-family: var(--bs-font-monospace);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 7px;
        background: #ede9fe;
        color: #4f46e5;
        white-space: nowrap;
        letter-spacing: 0.02em;
    }

    /* -------- Priority Pill -------- */
    .priority-pill {
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 9px;
        border-radius: 20px;
        text-transform: uppercase;
        display: inline-block;
    }

    /* -------- Status Pill -------- */
    .status-pill {
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        display: inline-block;
        white-space: nowrap;
    }

    /* -------- Project Tag -------- */
    .project-tag {
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        border-radius: 6px;
        padding: 2px 8px;
        white-space: nowrap;
    }

    /* -------- Empty State -------- */
    .empty-state-icon {
        font-size: 3.5rem;
        color: #c7d2fe;
    }

    /* -------- Due date warning -------- */
    .due-overdue { color: #dc2626; font-weight: 700; }
    .due-soon    { color: #d97706; font-weight: 700; }
    .due-normal  { color: #94a3b8; }
</style>

<!-- ===== HEADER ===== -->
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fw-bold mb-1" style="color: #1e1b4b; font-size: 1.55rem;">
            Công việc của tôi
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            Tất cả các nhiệm vụ được giao cho bạn, được sắp xếp theo mức độ ưu tiên.
        </p>
    </div>
</div>

<!-- ===== STAT CARDS ===== -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card shadow-sm" data-filter-trigger="all" style="background:#f0fdf4; border: 1.5px solid #bbf7d0; cursor: pointer;" title="Xem tất cả công việc">
            <div class="stat-number" style="color:#15803d;"><?= $stats['total'] ?></div>
            <div class="stat-label" style="color:#166534;">Tổng cộng</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card shadow-sm" data-filter-trigger="todo" style="background:#f8fafc; border: 1.5px solid #e2e8f0; cursor: pointer;" title="Lọc: To Do">
            <div class="stat-number" style="color:#475569;"><?= $stats['todo'] ?></div>
            <div class="stat-label" style="color:#64748b;">To Do</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card shadow-sm" data-filter-trigger="in_progress" style="background:#eff6ff; border: 1.5px solid #bfdbfe; cursor: pointer;" title="Lọc: In Progress">
            <div class="stat-number" style="color:#1d4ed8;"><?= $stats['in_progress'] ?></div>
            <div class="stat-label" style="color:#1e40af;">In Progress</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card shadow-sm" data-filter-trigger="in_review" style="background:#faf5ff; border: 1.5px solid #e9d5ff; cursor: pointer;" title="Lọc: In Review">
            <div class="stat-number" style="color:#7e22ce;"><?= $stats['in_review'] ?></div>
            <div class="stat-label" style="color:#6b21a8;">In Review</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card shadow-sm" data-filter-trigger="done" style="background:#f0fdf4; border: 1.5px solid #86efac; cursor: pointer;" title="Lọc: Done">
            <div class="stat-number" style="color:#16a34a;"><?= $stats['done'] ?></div>
            <div class="stat-label" style="color:#15803d;">Done</div>
        </div>
    </div>
</div>

<!-- ===== FILTER TABS + SEARCH ===== -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="d-flex gap-2 filter-tab-group flex-wrap">
        <button class="filter-tab active" data-filter="all">Tất cả</button>
        <button class="filter-tab" data-filter="todo">To Do</button>
        <button class="filter-tab" data-filter="in_progress">In Progress</button>
        <button class="filter-tab" data-filter="in_review">In Review</button>
        <button class="filter-tab" data-filter="done">Done</button>
    </div>
    <div class="position-relative" style="min-width: 220px;">
        <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none;"></i>
        <input id="taskSearchInput" type="text" class="form-control form-control-sm border-secondary-subtle rounded-pill ps-4" placeholder="Tìm kiếm công việc..." style="font-size: 0.85rem;">
    </div>
</div>

<!-- ===== TASK LIST ===== -->
<div id="taskListContainer" class="d-flex flex-column gap-2">
    <?php if (empty($tasks)): ?>
        <div class="text-center py-5">
            <div class="empty-state-icon mb-3">
                <i class="bi bi-journal-check"></i>
            </div>
            <h5 class="fw-bold text-muted">Chưa có công việc nào!</h5>
            <p class="text-muted small">Bạn chưa được gán công việc nào trong hệ thống.</p>
        </div>
    <?php else: ?>
        <?php foreach ($tasks as $task):
            // ---- Priority styles ----
            $priority = strtolower($task['priority'] ?? 'medium');
            if ($priority === 'highest') {
                $prioBg = '#ffbdad'; $prioColor = '#bf2600';
            } elseif ($priority === 'high') {
                $prioBg = '#ffebe6'; $prioColor = '#de350b';
            } elseif ($priority === 'medium') {
                $prioBg = '#fff0b3'; $prioColor = '#5e4310';
            } else {
                $prioBg = '#e3fcef'; $prioColor = '#006644';
            }

            // ---- Status styles ----
            $status = strtolower($task['status'] ?? 'todo');
            if ($status === 'done') {
                $statusBg = '#dcfce7'; $statusColor = '#15803d'; $statusLabel = 'DONE';
            } elseif ($status === 'in_progress') {
                $statusBg = '#dbeafe'; $statusColor = '#1d4ed8'; $statusLabel = 'IN PROGRESS';
            } elseif ($status === 'in_review') {
                $statusBg = '#faf5ff'; $statusColor = '#7e22ce'; $statusLabel = 'IN REVIEW';
            } else {
                $statusBg = '#f1f5f9'; $statusColor = '#475569'; $statusLabel = 'TO DO';
            }

            // ---- Due date logic ----
            $dueDate    = $task['due_date'] ?? null;
            $dueCssClass = 'due-normal';
            $dueText    = 'Không có hạn';
            if (!empty($dueDate)) {
                $dueTs = strtotime($dueDate);
                $now   = time();
                $dueText = date('d/m/Y H:i', $dueTs);
                if ($dueTs < $now && $status !== 'done') {
                    $dueCssClass = 'due-overdue';
                } elseif ($dueTs - $now < 86400 * 3 && $status !== 'done') {
                    $dueCssClass = 'due-soon';
                }
            }

            // ---- Reporter ----
            $reporterName = !empty(trim($task['reporter_full_name'] ?? ''))
                ? $task['reporter_full_name']
                : ($task['reporter_username'] ?? 'N/A');
        ?>
        <div class="task-item <?= $status === 'done' ? 'done-task' : '' ?>"
             data-status="<?= htmlspecialchars($status) ?>"
             data-title="<?= htmlspecialchars(strtolower($task['title'])) ?>"
             data-id="<?= $task['id'] ?>"
             onclick="window.openTaskDetailModal(<?= $task['id'] ?>)">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                <!-- Trái: Key + Title + Tags -->
                <div class="d-flex align-items-center gap-3 flex-wrap" style="min-width: 0; flex: 1;">
                    <span class="issue-key-badge"><?= htmlspecialchars($task['issue_key']) ?></span>

                    <span class="fw-semibold <?= $status === 'done' ? 'text-decoration-line-through text-muted' : '' ?>"
                          style="font-size: 0.93rem; color: #1e293b; max-width: 420px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">
                        <?= htmlspecialchars($task['title']) ?>
                    </span>

                    <span class="project-tag">
                        <?= htmlspecialchars($task['project_key'] ?? '') ?>
                        <?php if (!empty($task['project_name'])): ?>
                            &nbsp;·&nbsp; <?= htmlspecialchars($task['project_name']) ?>
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Phải: Priority + Status + Due + Reporter -->
                <div class="d-flex align-items-center gap-3 flex-wrap flex-shrink-0">
                    <!-- Priority -->
                    <span class="priority-pill" style="background:<?= $prioBg ?>; color:<?= $prioColor ?>;">
                        <?= strtoupper($priority) ?>
                    </span>

                    <!-- Status -->
                    <span class="status-pill" style="background:<?= $statusBg ?>; color:<?= $statusColor ?>;">
                        <?= $statusLabel ?>
                    </span>

                    <!-- Due date -->
                    <span class="small <?= $dueCssClass ?>" style="min-width: 90px; text-align: right;">
                        <i class="bi bi-calendar3 me-1"></i><?= $dueText ?>
                    </span>

                    <!-- Reporter avatar -->
                    <?php
                    $reporterId = $task['reporter_id'] ?? 0;
                    $avatarColors = ['06b6d4', 'f59e0b', '8b5cf6', '10b981', 'ec4899', '3b82f6'];
                    $reporterBg = $reporterId ? $avatarColors[$reporterId % count($avatarColors)] : '64748b';
                    $reporterAvatarUrl = !empty($task['reporter_avatar']) && $task['reporter_avatar'] !== 'default-avatar.png'
                        ? BASE_URL . '/uploads/avatars/' . $task['reporter_avatar']
                        : "https://ui-avatars.com/api/?name=" . urlencode($reporterName) . "&background=" . $reporterBg . "&color=fff";
                    ?>
                    <img src="<?= $reporterAvatarUrl ?>"
                         title="Reporter: <?= htmlspecialchars($reporterName) ?>"
                         style="width:26px; height:26px; border-radius:50%; object-fit:cover; border:2px solid #fff; box-shadow:0 1px 4px rgba(0,0,0,0.12);">

                    <!-- Arrow -->
                    <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal chi tiết công việc Jira-style -->
<?php require_once __DIR__ . '/../../partials/task_modal_right.php'; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---- Filter tabs ----
    const filterTabs   = document.querySelectorAll('.filter-tab');
    const taskItems    = document.querySelectorAll('.task-item');
    const searchInput  = document.getElementById('taskSearchInput');

    let currentFilter = 'all';
    let currentSearch = '';

    function applyFilters() {
        taskItems.forEach(item => {
            const statusMatch  = currentFilter === 'all' || item.dataset.status === currentFilter;
            const searchMatch  = currentSearch === '' || item.dataset.title.includes(currentSearch);
            item.style.display = (statusMatch && searchMatch) ? '' : 'none';
        });

        // Hiển thị trạng thái rỗng nếu không có gì
        const container = document.getElementById('taskListContainer');
        const visible = Array.from(taskItems).filter(i => i.style.display !== 'none');
        const existingEmpty = container.querySelector('.filter-empty-state');
        if (visible.length === 0 && taskItems.length > 0) {
            if (!existingEmpty) {
                const el = document.createElement('div');
                el.className = 'filter-empty-state text-center py-4 text-muted';
                el.innerHTML = '<i class="bi bi-funnel" style="font-size:2rem;opacity:.4;"></i><p class="mt-2 mb-0 small">Không có công việc nào phù hợp bộ lọc.</p>';
                container.appendChild(el);
            }
        } else if (existingEmpty) {
            existingEmpty.remove();
        }
    }

    // Helper: kích hoạt một filter theo giá trị status
    function activateFilter(filterValue) {
        filterTabs.forEach(t => t.classList.remove('active'));
        const matchTab = Array.from(filterTabs).find(t => t.dataset.filter === filterValue);
        if (matchTab) matchTab.classList.add('active');
        currentFilter = filterValue;
        applyFilters();

        // Cuộn mượt xuống danh sách
        document.getElementById('taskListContainer')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Click filter tab
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            activateFilter(this.dataset.filter);
        });
    });

    // Click stat card → kích hoạt filter tương ứng
    document.querySelectorAll('.stat-card[data-filter-trigger]').forEach(card => {
        card.addEventListener('click', function () {
            activateFilter(this.dataset.filterTrigger);
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = this.value.trim().toLowerCase();
            applyFilters();
        });
    }
});
</script>
