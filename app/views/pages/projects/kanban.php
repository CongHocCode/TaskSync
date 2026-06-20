<section class="kanban-page container-fluid py-4">
    <style>
        .quick-action-btn { transition: all 0.2s; }
        .quick-action-btn:hover { color: #0d6efd !important; font-weight: 700 !important; }
        .dashed-divider { border-top: 1px dashed #cbd5e1; margin: 12px 0; opacity: 0.8; }
        
        .custom-filter-select { cursor: pointer; transition: all 0.2s; }
        .custom-filter-select:focus, .custom-filter-select:hover {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            outline: 0;
        }

        /* Tinh chỉnh cho Modal Jira-style mới */
        .cursor-pointer { cursor: pointer; }
        .editor-toolbar i:hover { color: #0d6efd !important; }
        .task-modal-right-col select, .task-modal-right-col input {
            font-size: 0.85rem;
            color: #334155;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-2 fw-bold text-dark d-flex align-items-center">
                Frontend Reconstruction <i class="bi bi-pin-angle ms-2 text-muted" style="font-size: 1.4rem;"></i>
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Rebuild core visual elements, improve standard responsiveness, and integrate a high-contrast theme.</p>
        </div>
        
        <div class="d-flex align-items-center gap-2 d-none d-md-flex">
            <div class="d-flex align-items-center me-2">
                <img src="https://ui-avatars.com/api/?name=Alex&background=06b6d4&color=fff" class="rounded-circle border border-2 border-white shadow-sm" width="32" height="32" style="margin-right: -10px; z-index: 4; position: relative;">
                <img src="https://ui-avatars.com/api/?name=Sarah&background=f59e0b&color=fff" class="rounded-circle border border-2 border-white shadow-sm" width="32" height="32" style="margin-right: -10px; z-index: 3; position: relative;">
                <img src="https://ui-avatars.com/api/?name=Quyen&background=8b5cf6&color=fff" class="rounded-circle border border-2 border-white shadow-sm" width="32" height="32" style="margin-right: -10px; z-index: 2; position: relative;">
                <img src="https://ui-avatars.com/api/?name=Marcus&background=10b981&color=fff" class="rounded-circle border border-2 border-white shadow-sm" width="32" height="32" style="z-index: 1; position: relative;">
            </div>
            <a href="#" class="text-primary fw-semibold small text-decoration-none">Quản lý thành viên</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="d-flex align-items-center gap-2 mb-4 py-3 border-bottom flex-wrap">
        <span class="text-muted small fw-bold me-2"><i class="bi bi-funnel text-secondary"></i> Bộ lọc:</span>
        <select id="filterAssignee" class="form-select form-select-sm w-auto bg-white border border-secondary-subtle text-dark ps-3 pe-4 rounded-pill fw-medium custom-filter-select" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Lọc theo thành viên">
            <option value="all">Tất cả người gán</option>
            <option value="Alex">Alex</option>
            <option value="Sarah">Sarah</option>
            <option value="Quyen">Quyen</option>
            <option value="Marcus">Marcus</option>
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

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded-3 p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-2 mb-1">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #64748b;"></span>
                    <span class="fw-bold text-secondary small text-uppercase">To Do</span>
                    <span class="text-muted small ms-auto fw-bold count-badge">1</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-3 mt-1 p-1 overflow-y-auto" data-status="todo">
                    
                    <div class="card border border-light shadow-sm bg-white kanban-item-card rounded-3" data-bs-toggle="modal" data-bs-target="#taskDetailModal" role="button">
                        <div class="card-body p-3">
                            <div class="task-code text-muted small fw-bold mb-2" style="font-size: 0.75rem;">WEB-2</div>
                            <h6 class="task-title fw-bold text-dark mb-0" style="line-height: 1.4; font-size: 0.95rem;">Design high-contrast collapsible navigation system</h6>
                            <div class="dashed-divider"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-layout-sidebar-inset text-primary opacity-75" style="font-size: 0.85rem;"></i>
                                    <span class="badge rounded px-2 py-1" style="background-color: #ffebe6; color: #de350b; font-size: 0.65rem;"><i class="bi bi-bookmark-fill"></i> HIGH</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="task-assignee text-muted" style="font-size: 0.75rem;">Sarah</span>
                                    <img src="https://ui-avatars.com/api/?name=Sarah&background=f59e0b&color=fff" class="rounded-circle" style="width: 24px; height: 24px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center px-1 quick-actions-container">
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'in_progress')"><i class="bi bi-arrow-right"></i> In P</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'in_review')"><i class="bi bi-arrow-right"></i> In R</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'done')"><i class="bi bi-arrow-right"></i> Done</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded-3 p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-2 mb-1">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #3b82f6;"></span>
                    <span class="fw-bold text-primary small text-uppercase">In Progress</span>
                    <span class="text-muted small ms-auto fw-bold count-badge">2</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-3 mt-1 p-1 overflow-y-auto" data-status="in_progress">
                    
                    <div class="card border border-light shadow-sm bg-white kanban-item-card rounded-3" data-bs-toggle="modal" data-bs-target="#taskDetailModal" role="button">
                        <div class="card-body p-3">
                            <div class="task-code text-muted small fw-bold mb-2" style="font-size: 0.75rem;">WEB-1</div>
                            <h6 class="task-title fw-bold text-dark mb-0" style="line-height: 1.4; font-size: 0.95rem;">Migrate active layouts to Tailwind v4 framework</h6>
                            <div class="dashed-divider"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-lightning text-danger opacity-75" style="font-size: 0.85rem;"></i>
                                    <span class="badge rounded px-2 py-1" style="background-color: #ffbdad; color: #bf2600; font-size: 0.65rem;"><i class="bi bi-lightning-fill"></i> HIGHEST</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="task-assignee text-muted" style="font-size: 0.75rem;">Alex</span>
                                    <img src="https://ui-avatars.com/api/?name=Alex&background=06b6d4&color=fff" class="rounded-circle" style="width: 24px; height: 24px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center px-1 quick-actions-container">
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'todo')"><i class="bi bi-arrow-right"></i> To D</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'in_review')"><i class="bi bi-arrow-right"></i> In R</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'done')"><i class="bi bi-arrow-right"></i> Done</span>
                            </div>
                        </div>
                    </div>

                    <div class="card border border-light shadow-sm bg-white kanban-item-card rounded-3" data-bs-toggle="modal" data-bs-target="#taskDetailModal" role="button">
                        <div class="card-body p-3">
                            <div class="task-code text-muted small fw-bold mb-2" style="font-size: 0.75rem;">WEB-5</div>
                            <h6 class="task-title fw-bold text-dark mb-0" style="line-height: 1.4; font-size: 0.95rem;">Deploy preliminary localized storage caching</h6>
                            <div class="dashed-divider"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-hdd-network text-success opacity-75" style="font-size: 0.85rem;"></i>
                                    <span class="badge rounded px-2 py-1" style="background-color: #ffebe6; color: #de350b; font-size: 0.65rem;"><i class="bi bi-bookmark-fill"></i> HIGH</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="task-assignee text-muted" style="font-size: 0.75rem;">Sarah</span>
                                    <img src="https://ui-avatars.com/api/?name=Sarah&background=f59e0b&color=fff" class="rounded-circle" style="width: 24px; height: 24px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center px-1 quick-actions-container">
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'todo')"><i class="bi bi-arrow-right"></i> To D</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'in_review')"><i class="bi bi-arrow-right"></i> In R</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'done')"><i class="bi bi-arrow-right"></i> Done</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded-3 p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-2 mb-1">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #f97316;"></span>
                    <span class="fw-bold small text-uppercase" style="color: #ea580c !important;">In Review</span>
                    <span class="text-muted small ms-auto fw-bold count-badge">1</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-3 mt-1 p-1 overflow-y-auto" data-status="in_review">
                    
                    <div class="card border border-light shadow-sm bg-white kanban-item-card rounded-3" data-bs-toggle="modal" data-bs-target="#taskDetailModal" role="button">
                        <div class="card-body p-3">
                            <div class="task-code text-muted small fw-bold mb-2" style="font-size: 0.75rem;">WEB-3</div>
                            <h6 class="task-title fw-bold text-dark mb-0" style="line-height: 1.4; font-size: 0.95rem;">Establish responsive user dashboard statistics cards</h6>
                            <div class="dashed-divider"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-bar-chart text-info opacity-75" style="font-size: 0.85rem;"></i>
                                    <span class="badge rounded px-2 py-1" style="background-color: #fff0b3; color: #172b4d; font-size: 0.65rem;"><i class="bi bi-check-square-fill text-warning"></i> MEDIUM</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="task-assignee text-muted" style="font-size: 0.75rem;">Quyen</span>
                                    <img src="https://ui-avatars.com/api/?name=Quyen&background=8b5cf6&color=fff" class="rounded-circle" style="width: 24px; height: 24px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center px-1 quick-actions-container">
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'todo')"><i class="bi bi-arrow-right"></i> To D</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'in_progress')"><i class="bi bi-arrow-right"></i> In P</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'done')"><i class="bi bi-arrow-right"></i> Done</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded-3 p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-2 mb-1">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #10b981;"></span>
                    <span class="fw-bold text-success small text-uppercase">Done</span>
                    <span class="text-muted small ms-auto fw-bold count-badge">1</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-3 mt-1 p-1 overflow-y-auto" data-status="done">
                    
                    <div class="card border border-light shadow-sm bg-white kanban-item-card rounded-3 opacity-75" data-bs-toggle="modal" data-bs-target="#taskDetailModal" role="button">
                        <div class="card-body p-3">
                            <div class="task-code text-muted small fw-bold mb-2 text-decoration-line-through" style="font-size: 0.75rem;">WEB-4</div>
                            <h6 class="task-title fw-bold text-secondary text-decoration-line-through mb-0" style="line-height: 1.4; font-size: 0.95rem;">Audit browser cookie security warnings</h6>
                            <div class="dashed-divider"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check text-muted opacity-75" style="font-size: 0.85rem;"></i>
                                    <span class="badge rounded px-2 py-1" style="background-color: #e3fcef; color: #006644; font-size: 0.65rem;"><i class="bi bi-arrow-down text-success"></i> LOW</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="task-assignee text-muted text-decoration-line-through" style="font-size: 0.75rem;">Marcus</span>
                                    <img src="https://ui-avatars.com/api/?name=Marcus&background=10b981&color=fff" class="rounded-circle" style="width: 24px; height: 24px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center px-1 quick-actions-container">
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'todo')"><i class="bi bi-arrow-right"></i> To D</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'in_progress')"><i class="bi bi-arrow-right"></i> In P</span>
                                <span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, 'in_review')"><i class="bi bi-arrow-right"></i> In R</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            </div>
        </div>
    </div>

    <script>
    // Định nghĩa biến toàn cục lưu thẻ Card đang được chỉnh sửa
    window.targetKanbanCard = null;

    // Hàm click chuyển cột nhanh
    function moveTask(buttonElement, targetStatus) {
        const card = buttonElement.closest('.kanban-item-card');
        const targetColumn = document.querySelector(`.sub-kanban-column[data-status="${targetStatus}"]`);
        
        if (card && targetColumn) {
            targetColumn.appendChild(card);
            updateQuickActionsMenu(card, targetStatus);
            updateColumnCounts();
            
            // Cập nhật lại hiệu ứng gạch ngang / mờ của Done column
            toggleDoneStyles(card, targetStatus);
        }
    }

    // Hàm cập nhật nút chuyển cột
    function updateQuickActionsMenu(card, currentStatus) {
        const container = card.querySelector('.quick-actions-container');
        if (!container) return;

        const actions = {
            'todo': { label: 'To D', icon: 'bi-arrow-right' },
            'in_progress': { label: 'In P', icon: 'bi-arrow-right' },
            'in_review': { label: 'In R', icon: 'bi-arrow-right' },
            'done': { label: 'Done', icon: 'bi-arrow-right' }
        };

        let newHtml = '';
        for (const [statusKey, action] of Object.entries(actions)) {
            if (statusKey !== currentStatus) {
                newHtml += `<span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, '${statusKey}')"><i class="bi ${action.icon}"></i> ${action.label}</span>`;
            }
        }
        container.innerHTML = newHtml;
    }

    // Hàm bật/tắt CSS cho cột DONE
    function toggleDoneStyles(card, status) {
        const titleEl = card.querySelector('.task-title');
        const codeEl = card.querySelector('.task-code');
        const assigneeEl = card.querySelector('.task-assignee');

        if (status === 'done') {
            card.classList.add('opacity-75');
            if (titleEl) {
                titleEl.classList.add('text-secondary', 'text-decoration-line-through');
                titleEl.classList.remove('text-dark');
            }
            if (codeEl) codeEl.classList.add('text-decoration-line-through');
            if (assigneeEl) assigneeEl.classList.add('text-decoration-line-through');
        } else {
            card.classList.remove('opacity-75');
            if (titleEl) {
                titleEl.classList.remove('text-secondary', 'text-decoration-line-through');
                titleEl.classList.add('text-dark');
            }
            if (codeEl) codeEl.classList.remove('text-decoration-line-through');
            if (assigneeEl) assigneeEl.classList.remove('text-decoration-line-through');
        }
    }

    // Hàm đếm số lượng thẻ 
    function updateColumnCounts() {
        const kanbanColumns = document.querySelectorAll('.sub-kanban-column');
        kanbanColumns.forEach(column => {
            const visibleCards = Array.from(column.querySelectorAll('.kanban-item-card')).filter(card => card.style.display !== 'none');
            const headerDiv = column.previousElementSibling;
            if (headerDiv) {
                const countSpan = headerDiv.querySelector('.count-badge');
                if (countSpan) countSpan.innerText = visibleCards.length;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        
        // Khởi tạo Tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Xử lý Lọc dữ liệu
        const filterAssignee = document.getElementById('filterAssignee');
        const filterPriority = document.getElementById('filterPriority');

        function applyFilters() {
            const selectedAssignee = filterAssignee.value;
            const selectedPriority = filterPriority.value;

            document.querySelectorAll('.kanban-item-card').forEach(card => {
                const assigneeText = card.querySelector('.task-assignee').innerText.trim();
                const priorityText = card.querySelector('.badge').innerText.trim();

                const matchAssignee = (selectedAssignee === 'all') || (assigneeText === selectedAssignee);
                const matchPriority = (selectedPriority === 'all') || (priorityText.includes(selectedPriority));

                if (matchAssignee && matchPriority) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            updateColumnCounts();
        }

        filterAssignee.addEventListener('change', applyFilters);
        filterPriority.addEventListener('change', applyFilters);
        
        // Gán hàm filter vào window để gọi từ các sự kiện bên ngoài
        window.applyFilters = applyFilters;


        // ==============================================================
        // TÍNH NĂNG MỚI: ĐỔ DỮ LIỆU VÀO MODAL JIRA-STYLE 2 CỘT
        // ==============================================================
        const modalElement = document.getElementById('taskDetailModal');
        if(modalElement) {
            modalElement.addEventListener('show.bs.modal', function (event) {
                const card = event.relatedTarget;
                if (!card) return;

                // Khóa mục tiêu card đang chỉnh sửa
                window.targetKanbanCard = card;

                // 1. Lấy dữ liệu từ thẻ card
                const taskCode = card.querySelector('.task-code').innerText.trim();
                const taskTitle = card.querySelector('.task-title').innerText.trim();
                
                // Phân tích độ ưu tiên từ badge hiện tại
                const badgeText = card.querySelector('.badge').innerText.trim();
                
                // Lấy cột hiện tại để xác định Trạng thái
                const currentColumn = card.closest('.sub-kanban-column');
                const taskStatus = currentColumn ? currentColumn.getAttribute('data-status') : '';
                
                const assigneeEl = card.querySelector('.task-assignee');
                const taskAssignee = assigneeEl ? assigneeEl.innerText.trim() : 'Unassigned';

                // 2. Chọn khung chứa nội dung của Modal
                const modalContent = modalElement.querySelector('.modal-content');

                // 3. Đắp cấu trúc HTML 2 Cột giống hệt hình Jira vào Modal
                modalContent.innerHTML = `
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="text-muted small fw-semibold" style="letter-spacing: 1px; font-size: 0.75rem;">
                                DỰ ÁN / WEB / <span class="text-dark fw-bold">${taskCode}</span>
                            </div>
                            <button type="button" class="btn text-muted fw-medium d-flex align-items-center gap-1" data-bs-dismiss="modal" style="font-size: 0.9rem;">
                                <i class="bi bi-x-lg"></i> Close Modal
                            </button>
                        </div>
                    </div>
                    
                    <div class="modal-body px-4 pt-3 pb-4">
                        <div class="row g-4">
                            
                            <div class="col-12 col-lg-8 pe-lg-4 border-end">
                                <p class="text-muted fw-bold small mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">TÊN ISSUE</p>
                                <h3 class="fw-bold text-dark mb-4" style="line-height: 1.4;">${taskTitle}</h3>

                                <p class="text-muted fw-bold small mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">MÔ TẢ CÔNG VIỆC (EDITABLE)</p>
                                <div class="border border-secondary-subtle rounded-3 mb-4">
                                    <div class="border-bottom border-secondary-subtle bg-light p-2 d-flex gap-3 rounded-top-3 editor-toolbar">
                                        <i class="bi bi-type-bold text-dark cursor-pointer"></i>
                                        <i class="bi bi-type-italic text-dark cursor-pointer"></i>
                                        <i class="bi bi-type-underline text-dark cursor-pointer"></i>
                                        <i class="bi bi-code-slash text-dark cursor-pointer"></i>
                                    </div>
                                    <textarea class="form-control border-0 p-3 shadow-none text-secondary" rows="4" style="resize: none;">Dữ liệu mô tả công việc sẽ được lấy tự động vào đây...</textarea>
                                    <div class="text-end p-2 border-top border-secondary-subtle"><i class="bi bi-arrows-angle-expand text-muted cursor-pointer" style="font-size: 0.8rem;"></i></div>
                                </div>

                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="text-muted fw-bold small mb-0" style="font-size: 0.75rem; letter-spacing: 0.5px;">SUB-TASKS CHECKLIST</p>
                                    <span class="badge bg-light text-secondary border">1 / 2</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar" style="width: 50%; background-color: #6366f1;"></div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <input class="form-check-input me-2 shadow-none cursor-pointer" type="checkbox" checked>
                                    <span class="text-secondary text-decoration-line-through" style="font-size: 0.9rem;">Build custom storage observer wrapper</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <input class="form-check-input me-2 shadow-none cursor-pointer" type="checkbox">
                                    <span class="text-dark fw-medium" style="font-size: 0.9rem;">Bind state triggers</span>
                                </div>
                                <div class="input-group input-group-sm mb-4">
                                    <input type="text" class="form-control shadow-none border-secondary-subtle" placeholder="Thêm sub-task...">
                                    <button class="btn btn-light border border-secondary-subtle text-dark fw-medium" type="button">+ Thêm</button>
                                </div>

                                <p class="text-muted fw-bold small mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="bi bi-chat-square-text me-1"></i> LỊCH SỬ & THẢO LUẬN</p>
                                <div class="text-center text-muted mb-3" style="font-size: 0.85rem; font-family: monospace;">Chưa có bình luận nào.</div>
                                <div class="position-relative">
                                    <textarea class="form-control border-secondary-subtle shadow-none" rows="3" placeholder="Viết phản hồi hoặc đính kèm ý kiến..." style="resize: none; padding-right: 80px;"></textarea>
                                    <button class="btn text-white position-absolute bottom-0 end-0 m-2 rounded-2 fw-semibold" style="padding: 4px 16px; font-size: 0.85rem; background-color: #4f46e5;">Gửi đi</button>
                                </div>
                            </div>

                            <div class="col-12 col-lg-4 task-modal-right-col">
                                <div class="bg-light rounded-3 p-4 border border-light-subtle h-100">
                                    
                                    <div class="mb-3">
                                        <label class="text-muted fw-bold small mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">TRẠNG THÁI (STATUS)</label>
                                        <select class="form-select fw-medium shadow-none border-secondary-subtle rounded-2 py-2" id="modalStatusSelect">
                                            <option value="todo" ${taskStatus === 'todo' ? 'selected' : ''}>To Do</option>
                                            <option value="in_progress" ${taskStatus === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                            <option value="in_review" ${taskStatus === 'in_review' ? 'selected' : ''}>In Review</option>
                                            <option value="done" ${taskStatus === 'done' ? 'selected' : ''}>Done</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-muted fw-bold small mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">NGƯỜI XỬ LÝ (ASSIGNEE)</label>
                                        <select class="form-select fw-medium shadow-none border-secondary-subtle rounded-2 py-2" id="modalAssigneeSelect">
                                            <option value="Alex" ${taskAssignee === 'Alex' ? 'selected' : ''}>Alex</option>
                                            <option value="Sarah" ${taskAssignee === 'Sarah' ? 'selected' : ''}>Sarah</option>
                                            <option value="Quyen" ${taskAssignee === 'Quyen' ? 'selected' : ''}>Quyen</option>
                                            <option value="Marcus" ${taskAssignee === 'Marcus' ? 'selected' : ''}>Marcus</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-muted fw-bold small mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">NGƯỜI BÁO CÁO (REPORTER)</label>
                                        <div class="form-control form-control-sm bg-secondary bg-opacity-10 text-muted fw-medium border-0 py-2 rounded-2" style="font-size: 0.85rem;">
                                            <em>Quyen Gia</em>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-muted fw-bold small mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">ĐỘ ƯU TIÊN (PRIORITY)</label>
                                        <select class="form-select fw-medium shadow-none border-secondary-subtle rounded-2 py-2" id="modalPrioritySelect">
                                            <option value="HIGHEST" ${badgeText.includes('HIGHEST') ? 'selected' : ''}>Highest ⚡</option>
                                            <option value="HIGH" ${badgeText.includes('HIGH') ? 'selected' : ''}>High 🔴</option>
                                            <option value="MEDIUM" ${badgeText.includes('MEDIUM') ? 'selected' : ''}>Medium 🟡</option>
                                            <option value="LOW" ${badgeText.includes('LOW') ? 'selected' : ''}>Low 🟢</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-muted fw-bold small mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">HẠN HOÀN THÀNH (DUE DATE)</label>
                                        <input type="date" class="form-control fw-medium shadow-none border-secondary-subtle rounded-2 py-2" value="2026-03-06">
                                    </div>

                                    <div class="mb-4">
                                        <label class="text-muted fw-bold small mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">GITHUB BRANCH URL</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white shadow-none border-secondary-subtle font-monospace text-muted py-2" style="font-size: 0.75rem;" value="https://github.com/myorg/fron..." readonly>
                                            <button class="btn border border-secondary-subtle bg-white text-secondary py-2" type="button"><i class="bi bi-files"></i></button>
                                        </div>
                                    </div>

                                    <hr class="text-muted opacity-25 my-4">
                                    
                                    <button class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2" style="background-color: #fff0f2; color: #e11d48; border: 1px solid #ffe4e6; font-size: 0.85rem;" onclick="if(confirm('Chắc chắn muốn xóa công việc này?')) alert('Đã gửi lệnh xóa!');">
                                        <i class="bi bi-trash3"></i> HỦY SỐ ĐĂNG KÝ (DELETE)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        // KÉO THẢ KANBAN
        const kanbanColumns = document.querySelectorAll('.sub-kanban-column');
        kanbanColumns.forEach(column => {
            new Sortable(column, {
                group: 'kanban-board', 
                animation: 150,
                ghostClass: 'opacity-50',
                dragClass: 'shadow-lg',
                onEnd: function (evt) {
                    if (evt.to === evt.from) return; 
                    const card = evt.item;
                    const newStatus = evt.to.getAttribute('data-status');
                    updateQuickActionsMenu(card, newStatus);
                    toggleDoneStyles(card, newStatus);
                    updateColumnCounts();
                }
            });
        });

        // ==============================================================
        // LẮNG NGHE SỰ KIỆN THAY ĐỔI TRÊN MODAL ĐỂ CẬP NHẬT KANBAN BOARD
        // ==============================================================
        document.addEventListener('change', function (e) {
            if (!window.targetKanbanCard) return;
            if (!e.target.closest('#taskDetailModal')) return;

            // 1. Thay đổi Trạng thái (Status)
            if (e.target.id === 'modalStatusSelect') {
                const newStatus = e.target.value;
                const destColumn = document.querySelector(`.sub-kanban-column[data-status="${newStatus}"]`);
                
                if (destColumn) {
                    destColumn.appendChild(window.targetKanbanCard);
                    updateQuickActionsMenu(window.targetKanbanCard, newStatus);
                    toggleDoneStyles(window.targetKanbanCard, newStatus);
                    updateColumnCounts();
                    if (typeof window.applyFilters === 'function') window.applyFilters();
                }
            }

            // 2. Thay đổi Người xử lý (Assignee)
            if (e.target.id === 'modalAssigneeSelect') {
                const selectedAssignee = e.target.value;
                
                // Cập nhật text Tên
                const nameEl = window.targetKanbanCard.querySelector('.task-assignee');
                if (nameEl) nameEl.innerText = selectedAssignee;

                // Cập nhật Avatar ảnh
                const avatarImg = window.targetKanbanCard.querySelector('img');
                const avatarMap = {
                    'Alex': '06b6d4',
                    'Sarah': 'f59e0b',
                    'Quyen': '8b5cf6',
                    'Marcus': '10b981'
                };
                if (avatarImg && avatarMap[selectedAssignee]) {
                    avatarImg.src = `https://ui-avatars.com/api/?name=${selectedAssignee}&background=${avatarMap[selectedAssignee]}&color=fff`;
                }

                if (typeof window.applyFilters === 'function') window.applyFilters();
            }

            // 3. Thay đổi Độ ưu tiên (Priority)
            if (e.target.id === 'modalPrioritySelect') {
                const selectedPriority = e.target.value;
                const priorityBadge = window.targetKanbanCard.querySelector('.badge');
                
                if (priorityBadge) {
                    if (selectedPriority === 'HIGHEST') {
                        priorityBadge.style.backgroundColor = '#ffbdad';
                        priorityBadge.style.color = '#bf2600';
                        priorityBadge.innerHTML = '<i class="bi bi-lightning-fill"></i> HIGHEST';
                    } else if (selectedPriority === 'HIGH') {
                        priorityBadge.style.backgroundColor = '#ffebe6';
                        priorityBadge.style.color = '#de350b';
                        priorityBadge.innerHTML = '<i class="bi bi-bookmark-fill"></i> HIGH';
                    } else if (selectedPriority === 'MEDIUM') {
                        priorityBadge.style.backgroundColor = '#fff0b3';
                        priorityBadge.style.color = '#172b4d';
                        priorityBadge.innerHTML = '<i class="bi bi-check-square-fill text-warning"></i> MEDIUM';
                    } else if (selectedPriority === 'LOW') {
                        priorityBadge.style.backgroundColor = '#e3fcef';
                        priorityBadge.style.color = '#006644';
                        priorityBadge.innerHTML = '<i class="bi bi-arrow-down text-success"></i> LOW';
                    }
                }

                if (typeof window.applyFilters === 'function') window.applyFilters();
            }
        });
    });
    </script>
</section>