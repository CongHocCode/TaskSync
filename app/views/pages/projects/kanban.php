<section class="kanban-page container-fluid py-4">
    <style>
        .quick-action-btn {
            transition: all 0.2s;
        }

        .quick-action-btn:hover {
            color: #0d6efd !important;
            font-weight: 700 !important;
        }

        .dashed-divider {
            border-top: 1px dashed #cbd5e1;
            margin: 12px 0;
            opacity: 0.8;
        }

        .custom-filter-select {
            cursor: pointer;
            transition: all 0.2s;
        }

        .custom-filter-select:focus,
        .custom-filter-select:hover {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            outline: 0;
        }

        /* Tinh chỉnh cho Modal Jira-style mới */
        .cursor-pointer {
            cursor: pointer;
        }

        .editor-toolbar i:hover {
            color: #0d6efd !important;
        }

        .task-modal-right-col select,
        .task-modal-right-col input {
            font-size: 0.85rem;
            color: #334155;
        }

        /* Hiệu ứng kéo thả vùng chứa */
        .sub-kanban-column {
            transition: background-color 0.2s, border 0.2s;
            border: 2px solid transparent;
        }

        .sub-kanban-column.drag-over {
            background-color: #e2e8f0 !important;
            border: 2px dashed #94a3b8 !important;
            border-radius: 8px;
        }

        .kanban-item-card.dragging {
            opacity: 0.4;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-2 fw-bold text-dark d-flex align-items-center">
                <?= htmlspecialchars($data['project']['name'] ?? 'Frontend Reconstruction') ?> <i class="bi bi-pin-angle ms-2 text-muted" style="font-size: 1.4rem;"></i>
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;"><?= htmlspecialchars($data['project']['description'] ?? '') ?></p>
        </div>

        <div class="d-flex align-items-center gap-2 d-none d-md-flex">
            <div class="d-flex align-items-center me-2">
                <?php
                $avatarColors = ['06b6d4', 'f59e0b', '8b5cf6', '10b981', 'ec4899', '3b82f6'];
                $idx = 0;
                $maxDisplay = 4;
                $totalMembers = count($data['members'] ?? []);
                foreach (($data['members'] ?? []) as $member):
                    if ($idx >= $maxDisplay) break;
                    $color = $avatarColors[$idx % count($avatarColors)];
                    $fullName = $member['first_name'] . ' ' . $member['last_name'];
                    $displayName = !empty(trim($fullName)) ? $fullName : $member['username'];
                    $avatarUrl = !empty($member['avatar_url']) && $member['avatar_url'] !== 'default-avatar.png'
                        ? BASE_URL . '/uploads/avatars/' . $member['avatar_url']
                        : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=" . $color . "&color=fff";
                    $zIndex = $totalMembers - $idx;
                ?>
                    <img src="<?= $avatarUrl ?>"
                        class="rounded-circle border border-2 border-white shadow-sm"
                        width="32"
                        height="32"
                        style="margin-right: -10px; z-index: <?= $zIndex ?>; position: relative;"
                        title="<?= htmlspecialchars($displayName) ?> (<?= htmlspecialchars($member['role']) ?>)">
                <?php
                    $idx++;
                endforeach;
                if ($totalMembers > $maxDisplay):
                ?>
                    <span class="rounded-circle border border-2 border-white shadow-sm bg-secondary text-white d-flex align-items-center justify-content-center fw-bold small"
                        style="width: 32px; height: 32px; z-index: 0; position: relative; font-size: 0.75rem; margin-left: 5px;">
                        +<?= ($totalMembers - $maxDisplay) ?>
                    </span>
                <?php endif; ?>
            </div>
            <a href="<?= BASE_URL ?>/project/members/<?= $data['project']['id'] ?>" class="text-primary fw-semibold small text-decoration-none">Quản lý thành viên</a>

            <!-- Nút tạo issue nhỏ kích hoạt trực tiếp createIssueModal-->
            <button type="button" class="btn btn-sm btn-primary ms-3 rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createIssueModal">
                <i class="bi bi-plus-lg" style="font-size: 0.8rem;"></i> Tạo Issue
            </button>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 mb-4 py-3 border-bottom flex-wrap">
        <span class="text-muted small fw-bold me-2"><i class="bi bi-funnel text-secondary"></i> Bộ lọc:</span>
        <select id="filterAssignee" class="form-select form-select-sm w-auto bg-white border border-secondary-subtle text-dark ps-3 pe-4 rounded-pill fw-medium custom-filter-select" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Lọc theo thành viên">
            <option value="all">Tất cả người gán</option>
            <?php foreach (($data['members'] ?? []) as $member):
                $fullName = $member['first_name'] . ' ' . $member['last_name'];
                $displayName = !empty(trim($fullName)) ? $fullName : $member['username'];
            ?>
                <option value="<?= htmlspecialchars($member['username']) ?>"><?= htmlspecialchars($displayName) ?></option>
            <?php endforeach; ?>
        </select>
        <select id="filterPriority" class="form-select form-select-sm w-auto bg-white border border-secondary-subtle text-dark ps-3 pe-4 rounded-pill fw-medium custom-filter-select" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Lọc theo độ ưu tiên">
            <option value="all">Mọi độ ưu tiên</option>
            <option value="HIGHEST">Highest</option>
            <option value="HIGH">High</option>
            <option value="MEDIUM">Medium</option>
            <option value="LOW">Low</option>
        </select>
        <select id="filterType" class="form-select form-select-sm w-auto bg-white border border-secondary-subtle text-dark ps-3 pe-4 rounded-pill fw-medium custom-filter-select" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Lọc theo loại hình">
            <option value="all">Tất cả loại hình</option>
            <option value="Task">Task (Công việc)</option>
            <option value="Bug">Bug (Lỗi)</option>
        </select>
    </div>

    <div class="row g-3 kanban-board-wrapper">
        <?php
        $columns = [
            'todo' => ['label' => 'To Do', 'color' => '#64748b'],
            'in_progress' => ['label' => 'In Progress', 'color' => '#3b82f6'],
            'in_review' => ['label' => 'In Review', 'color' => '#f97316'],
            'done' => ['label' => 'Done', 'color' => '#10b981']
        ];

        foreach ($columns as $statusKey => $colInfo):
            // Lọc các task thuộc cột status này từ database
            $colTasks = array_filter($data['tasks'] ?? [], function ($t) use ($statusKey) {
                return $t['status'] === $statusKey;
            });
            $count = count($colTasks);
        ?>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="rounded-3 p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                    <div class="d-flex align-items-center gap-2 px-2 py-2 mb-1">
                        <span class="rounded-circle" style="width: 8px; height: 8px; background-color: <?= $colInfo['color'] ?>;"></span>
                        <span class="fw-bold text-secondary small text-uppercase"><?= $colInfo['label'] ?></span>
                        <span class="text-muted small ms-auto fw-bold count-badge"><?= $count ?></span>
                    </div>
                    <div class="sub-kanban-column d-flex flex-column gap-3 mt-1 p-1 overflow-y-auto" data-status="<?= $statusKey ?>" style="min-height: 480px;">
                        <?php foreach ($colTasks as $task):
                            // Định dạng màu và icon theo độ ưu tiên
                            $priority = strtoupper($task['priority']);
                            if ($priority === 'HIGHEST') {
                                $priorityBg = '#ffbdad';
                                $priorityColor = '#bf2600';
                                $priorityIcon = 'bi-lightning-fill';
                            } elseif ($priority === 'HIGH') {
                                $priorityBg = '#ffebe6';
                                $priorityColor = '#de350b';
                                $priorityIcon = 'bi-bookmark-fill';
                            } elseif ($priority === 'MEDIUM') {
                                $priorityBg = '#fff0b3';
                                $priorityColor = '#172b4d';
                                $priorityIcon = 'bi-check-square-fill text-warning';
                            } else { // LOW
                                $priorityBg = '#e3fcef';
                                $priorityColor = '#006644';
                                $priorityIcon = 'bi-arrow-down text-success';
                            }

                            // NOTE: Xử lý gán tên người thực hiện một cách an toàn và động
                            $assigneeFullName = trim(($task['assignee_first'] ?? '') . ' ' . ($task['assignee_last'] ?? ''));
                            if (!empty($assigneeFullName)) {
                                $assignee = $assigneeFullName;
                            } else {
                                $assignee = !empty($task['assignee_name']) ? $task['assignee_name'] : 'Unassigned';
                            }

                            // NOTE: Tạo màu nền ngẫu nhiên theo ID người gán để tránh cấu trúc rẽ nhánh if/else gán cứng cũ của T2
                            $assigneeId = $task['assignee_id'] ?? 0;
                            $avatarColors = ['06b6d4', 'f59e0b', '8b5cf6', '10b981', 'ec4899', '3b82f6'];
                            $avatarBg = $assigneeId ? $avatarColors[$assigneeId % count($avatarColors)] : '64748b';

                            $avatarUrl = !empty($task['assignee_avatar']) && $task['assignee_avatar'] !== 'default-avatar.png'
                                ? BASE_URL . '/uploads/avatars/' . $task['assignee_avatar']
                                : "https://ui-avatars.com/api/?name=" . urlencode($assignee) . "&background=" . $avatarBg . "&color=fff";

                            $isDone = ($statusKey === 'done');
                        ?>
                            <div class="card border border-light shadow-sm bg-white kanban-item-card rounded-3 <?= $isDone ? 'opacity-75' : '' ?>"
                                data-id="<?= $task['id'] ?>"
                                data-assignee="<?= htmlspecialchars($task['assignee_username'] ?? ($task['assignee_name'] ?? 'Unassigned')) ?>"
                                data-priority="<?= htmlspecialchars(strtoupper($task['priority'])) ?>"
                                data-type="<?= htmlspecialchars(ucfirst($task['type'])) ?>"
                                draggable="true"
                                role="button">
                                <div class="card-body p-3">
                                    <div class="task-code text-muted small fw-bold mb-2 <?= $isDone ? 'text-decoration-line-through' : '' ?>" style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($task['issue_key']) ?>
                                    </div>
                                    <h6 class="task-title fw-bold mb-0 <?= $isDone ? 'text-secondary text-decoration-line-through' : 'text-dark' ?>" style="line-height: 1.4; font-size: 0.95rem;">
                                        <?= htmlspecialchars($task['title']) ?>
                                    </h6>
                                    <div class="dashed-divider"></div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($task['type'] === 'bug'): ?>
                                                <i class="bi bi-bug-fill text-danger opacity-75" style="font-size: 0.85rem;"></i>
                                            <?php else: ?>
                                                <i class="bi bi-check-square-fill text-primary opacity-75" style="font-size: 0.85rem;"></i>
                                            <?php endif; ?>
                                            <span class="badge rounded px-2 py-1" style="background-color: <?= $priorityBg ?>; color: <?= $priorityColor ?>; font-size: 0.65rem;">
                                                <i class="bi <?= $priorityIcon ?>"></i> <?= $priority ?>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="task-assignee text-muted <?= $isDone ? 'text-decoration-line-through' : '' ?>" style="font-size: 0.75rem;"><?= htmlspecialchars($assignee) ?></span>
                                            <img src="<?= $avatarUrl ?>" class="rounded-circle" style="width: 24px; height: 24px;">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center px-1 quick-actions-container">
                                        <?php
                                        $actions = [
                                            'todo' => ['label' => 'To D', 'icon' => 'bi-arrow-right'],
                                            'in_progress' => ['label' => 'In P', 'icon' => 'bi-arrow-right'],
                                            'in_review' => ['label' => 'In R', 'icon' => 'bi-arrow-right'],
                                            'done' => ['label' => 'Done', 'icon' => 'bi-arrow-right']
                                        ];
                                        foreach ($actions as $actKey => $actVal):
                                            if ($actKey !== $statusKey):
                                        ?>
                                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, '<?= $actKey ?>')"><i class="bi <?= $actVal['icon'] ?>"></i> <?= $actVal['label'] ?></span>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Bootstrap Modal chứa chi tiết công việc (Sử dụng đúng chuẩn form của Quyền từ TaskSync) -->
    <?php require_once __DIR__ . '/../../partials/task_modal_right.php'; ?>
</section>