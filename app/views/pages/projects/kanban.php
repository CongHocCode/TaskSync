<section class="kanban-page container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: #1a1632 !important;">Bảng Kanban</h1>
            <p class="text-muted mb-0">Quản lý và theo dõi tiến độ công việc dự án.</p>
        </div>
        <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas">
            <i class="bi bi-layout-sidebar-reversed"></i> Xem chi tiết Task
        </button>
    </div>

    <div class="row g-3 kanban-board-wrapper">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title fw-bold mb-0" style="color: #1a1632 !important;">To Do</h5>
                    <span class="badge bg-secondary rounded-pill">2</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column" data-status="todo">

                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" data-task-id="101" role="button">
                        <h6 class="fw-bold mb-1" style="color: #1a1632 !important;">Thiết kế UI trang đăng nhập</h6>
                        <p class="text-muted small mb-3">Hoàn thiện prototype wireframe gửi khách hàng duyệt.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">TS-101</span>
                            <small class="text-danger fw-semibold"><i class="bi bi-clock"></i> 18/06</small>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" data-task-id="102" role="button">
                        <h6 class="fw-bold mb-1" style="color: #1a1632 !important;">Phân tích Database</h6>
                        <p class="text-muted small mb-3">Xác định các thực thể chính cho cơ sở dữ liệu.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">TS-102</span>
                            <small class="text-secondary"><i class="bi bi-clock"></i> 20/06</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title fw-bold mb-0" style="color: #1a1632 !important;">In Progress</h5>
                    <span class="badge bg-primary rounded-pill">1</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column" data-status="in_progress">

                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" data-task-id="201" role="button">
                        <h6 class="fw-bold mb-1" style="color: #1a1632 !important;">Phát triển API Xác thực</h6>
                        <p class="text-muted small mb-3">Cài đặt mã hóa mật khẩu và tạo JWT token.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">TS-201</span>
                            <small class="text-warning fw-semibold"><i class="bi bi-clock"></i> 22/06</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title fw-bold mb-0" style="color: #1a1632 !important;">In Review</h5>
                    <span class="badge bg-warning text-dark rounded-pill">1</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column" data-status="in_review">

                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" data-task-id="301" role="button">
                        <h6 class="fw-bold mb-1" style="color: #1a1632 !important;">Cấu hình responsive Sidebar</h6>
                        <p class="text-muted small mb-3">Kiểm tra hiển thị flexbox trên Mobile.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">TS-301</span>
                            <small class="text-secondary"><i class="bi bi-clock"></i> 25/06</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title fw-bold mb-0" style="color: #1a1632 !important;">Done</h5>
                    <span class="badge bg-success rounded-pill">1</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column" data-status="done">

                    <div class="card border-0 shadow-sm p-3 opacity-75 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" data-task-id="401" role="button">
                        <h6 class="fw-bold mb-1 text-decoration-line-through text-secondary">Khởi tạo Base Project MVC</h6>
                        <p class="text-muted small mb-3">Cài đặt cấu trúc thư mục app, public.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-muted border text-uppercase small">TS-401</span>
                            <small class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Hoàn thành</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <?php require_once __DIR__ . '/../../partials/task_modal_right.php'; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<section class="kanban-section w-100">

    <div class="d-flex align-items-center gap-2 mb-4 py-2 border-bottom flex-wrap">
        <span class="text-muted small fw-bold me-2"><i class="bi bi-funnel-fill text-secondary"></i> Bộ lọc:</span>
        <select class="form-select form-select-sm w-auto bg-white border text-dark px-3">
            <option>Tất cả người gán</option>
        </select>
        <select class="form-select form-select-sm w-auto bg-white border text-dark px-3">
            <option>Mọi độ ưu tiên</option>
        </select>
        <select class="form-select form-select-sm w-auto bg-white border text-dark px-3">
            <option>Tất cả loại hình</option>
        </select>
    </div>

    <div class="row g-3 kanban-board-wrapper">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-1.5">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #64748b;"></span>
                    <span class="fw-bold text-secondary small text-uppercase">To Do</span>
                    <span class="text-muted small ms-auto fw-bold">1</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-2 mt-2 p-1 overflow-y-auto" data-status="todo">

                    <div class="card border-0 shadow-sm p-3 bg-white kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <span class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">WEB-2</span>
                        <h6 class="fw-bold mb-3 text-dark style-title" style="line-height: 1.4;">Design high-contrast collapsible navigation...</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded px-2 py-1" style="background-color: #ffebe6; color: #de350b; font-size: 0.7rem;"><i class="bi bi-bookmark-fill"></i> HIGH</span>
                            <div class="d-flex align-items-center gap-1.5"><span class="text-muted" style="font-size: 0.75rem;">Sarah</span><img src="https://ui-avatars.com/api/?name=Sarah&background=f59e0b&color=fff" class="rounded-circle" style="width: 20px; height: 20px;"></div>
                        </div>
                        <div class="border-top pt-2 d-flex gap-1 flex-wrap quick-actions">
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> In P</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> In R</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> Done</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-1.5">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #3b82f6;"></span>
                    <span class="fw-bold text-primary small text-uppercase">In Progress</span>
                    <span class="text-muted small ms-auto fw-bold">2</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-2 mt-2 p-1 overflow-y-auto" data-status="in_progress">

                    <div class="card border-0 shadow-sm p-3 bg-white kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <span class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">WEB-1</span>
                        <h6 class="fw-bold mb-3 text-dark" style="line-height: 1.4;">Migrate active layouts to Tailwind v4 framework</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded px-2 py-1" style="background-color: #ffbdad; color: #bf2600; font-size: 0.7rem;"><i class="bi bi-lightning-fill"></i> HIGHEST</span>
                            <div class="d-flex align-items-center gap-1.5"><span class="text-muted" style="font-size: 0.75rem;">Alex</span><img src="https://ui-avatars.com/api/?name=Alex&background=06b6d4&color=fff" class="rounded-circle" style="width: 20px; height: 20px;"></div>
                        </div>
                        <div class="border-top pt-2 d-flex gap-1 flex-wrap quick-actions">
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> To D</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> In R</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> Done</button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-3 bg-white kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <span class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">WEB-5</span>
                        <h6 class="fw-bold mb-3 text-dark" style="line-height: 1.4;">Deploy preliminary localized storage caching</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded px-2 py-1" style="background-color: #ffebe6; color: #de350b; font-size: 0.7rem;"><i class="bi bi-bookmark-fill"></i> HIGH</span>
                            <div class="d-flex align-items-center gap-1.5"><span class="text-muted" style="font-size: 0.75rem;">Sarah</span><img src="https://ui-avatars.com/api/?name=Sarah&background=f59e0b&color=fff" class="rounded-circle" style="width: 20px; height: 20px;"></div>
                        </div>
                        <div class="border-top pt-2 d-flex gap-1 flex-wrap quick-actions">
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> To D</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> In R</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> Done</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-1.5">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #f97316;"></span>
                    <span class="fw-bold small text-uppercase" style="color: #ea580c !important;">In Review</span>
                    <span class="text-muted small ms-auto fw-bold">1</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-2 mt-2 p-1 overflow-y-auto" data-status="in_review">

                    <div class="card border-0 shadow-sm p-3 bg-white kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <span class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">WEB-3</span>
                        <h6 class="fw-bold mb-3 text-dark" style="line-height: 1.4;">Establish responsive user dashboard statistics cards</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded px-2 py-1" style="background-color: #fff0b3; color: #172b4d; font-size: 0.7rem;"><i class="bi bi-check-square-fill text-warning"></i> MEDIUM</span>
                            <div class="d-flex align-items-center gap-1.5"><span class="text-muted" style="font-size: 0.75rem;">Quyen</span><img src="https://ui-avatars.com/api/?name=Quyen&background=8b5cf6&color=fff" class="rounded-circle" style="width: 20px; height: 20px;"></div>
                        </div>
                        <div class="border-top pt-2 d-flex gap-1 flex-wrap quick-actions">
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> To D</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> In P</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> Done</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="rounded p-2 d-flex flex-column h-100" style="background-color: #f4f5f7; min-height: 550px;">
                <div class="d-flex align-items-center gap-2 px-2 py-1.5">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #10b981;"></span>
                    <span class="fw-bold text-success small text-uppercase">Done</span>
                    <span class="text-muted small ms-auto fw-bold">1</span>
                </div>
                <div class="sub-kanban-column d-flex flex-column gap-2 mt-2 p-1 overflow-y-auto" data-status="done">

                    <div class="card border-0 shadow-sm p-3 bg-white kanban-item-card opacity-75" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <span class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">WEB-4</span>
                        <h6 class="fw-bold mb-3 text-decoration-line-through text-secondary" style="line-height: 1.4;">Audit browser cookie security warnings</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded px-2 py-1" style="background-color: #e3fcef; color: #006644; font-size: 0.7rem;"><i class="bi bi-arrow-down text-success"></i> LOW</span>
                            <div class="d-flex align-items-center gap-1.5"><span class="text-muted" style="font-size: 0.75rem;">Marcus</span><img src="https://ui-avatars.com/api/?name=Marcus&background=10b981&color=fff" class="rounded-circle" style="width: 20px; height: 20px;"></div>
                        </div>
                        <div class="border-top pt-2 d-flex gap-1 flex-wrap quick-actions">
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> To D</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> In P</button>
                            <button class="btn btn-xs btn-light text-muted px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="bi bi-arrow-right"></i> In R</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <?php require_once __DIR__ . '/../../partials/task_modal_right.php'; ?>
</section>