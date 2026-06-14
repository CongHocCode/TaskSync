<section class="kanban-page container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Bảng Kanban</h1>
            <p class="text-muted mb-0">Quản lý trạng thái công việc theo 4 cột quy trình tiêu chuẩn.</p>
        </div>
        <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas">
            <i class="bi bi-layout-sidebar-reversed"></i> Mở Chi Tiết
        </button>
    </div>

    <div class="row g-3 kanban-board-wrapper">
        
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title text-dark fw-bold mb-0">To Do</h5>
                    <span class="badge bg-secondary rounded-pill">2</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column">
                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <h6 class="text-dark fw-bold mb-1">Thiết kế UI trang đăng nhập</h6>
                        <p class="text-muted small mb-3">Hoàn thiện mẫu giao diện và gửi review cho team.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">WEB-1</span>
                            <small class="text-danger fw-semibold"><i class="bi bi-clock"></i> 05/06</small>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <h6 class="text-dark fw-bold mb-1">Lập kế hoạch sprint mới</h6>
                        <p class="text-muted small mb-3">Chuẩn bị backlog và phân công nhiệm vụ.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">WEB-2</span>
                            <small class="text-secondary"><i class="bi bi-clock"></i> 06/06</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title text-dark fw-bold mb-0">In Progress</h5>
                    <span class="badge bg-primary rounded-pill">1</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column">
                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <h6 class="text-dark fw-bold mb-1">Phát triển API đăng nhập</h6>
                        <p class="text-muted small mb-3">Hoàn tất xác thực và trả về token bảo mật JWT.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">AUTH-1</span>
                            <small class="text-warning fw-semibold"><i class="bi bi-clock"></i> 07/06</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title text-dark fw-bold mb-0">In Review</h5>
                    <span class="badge bg-warning text-dark rounded-pill">1</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column">
                    <div class="card border-0 shadow-sm p-3 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <h6 class="text-dark fw-bold mb-1">Đánh giá UX flow trang chủ</h6>
                        <p class="text-muted small mb-3">Nhận phản hồi từ nhóm thiết kế để tối ưu hóa trải nghiệm.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-dark border text-uppercase small">UX-3</span>
                            <small class="text-secondary"><i class="bi bi-clock"></i> 09/06</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="card-title text-dark fw-bold mb-0">Done</h5>
                    <span class="badge bg-success rounded-pill">1</span>
                </div>
                <div class="card-body d-flex flex-column gap-2 overflow-auto sub-kanban-column">
                    <div class="card bg-white border-0 shadow-sm p-3 opacity-75 kanban-item-card" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" role="button">
                        <h6 class="text-decoration-line-through text-secondary fw-bold mb-1">Cài đặt môi trường Dev</h6>
                        <p class="text-muted small mb-3">Đã hoàn thành cấu hình Docker container và Mysql Database.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-light text-muted border text-uppercase small">SYS-1</span>
                            <small class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Hoàn tất</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php require_once __DIR__ . '/../../partials/task_modal_right.php'; ?>
</section>