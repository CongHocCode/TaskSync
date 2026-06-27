<!-- app/views/pages/projects/settings.php -->
<div class="container-fluid py-4">
    <!-- Tiêu đề trang -->
    <div class="mb-4">
        <h1 class="h3 mb-2 fw-bold text-dark">
            <i class="bi bi-gear-fill text-primary me-2"></i>Cấu hình dự án: <span class="text-primary"><?= htmlspecialchars($data['project']['name']) ?></span>
        </h1>
        <p class="text-muted" style="font-size: 0.95rem;">Quản lý thông tin chung, cập nhật mã viết tắt (Key), kho GitHub và theo dõi tiến độ sức khỏe của dự án.</p>
    </div>

    <!-- HIỂN THỊ BẢO MẬT: Bộ thông báo phản hồi (Flash Session) từ Controller -->
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0 border-start border-4 border-danger" role="alert" style="background-color: #fef2f2;">
            <div class="d-flex align-items-center text-danger">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="fw-medium"><?= $_SESSION['flash_error'] ?></div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0 border-start border-4 border-success" role="alert" style="background-color: #f0fdf4;">
            <div class="d-flex align-items-center text-success">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div class="fw-medium"><?= $_SESSION['flash_success'] ?></div>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- CỘT TRÁI (65%): FORM CHỈNH SỬA & VÙNG NGUY HIỂM (DANGER ZONE) -->
        <div class="col-12 col-xl-8">
            <div class="d-flex flex-column gap-4">

                <!-- KHỐI 1: FORM CẤU HÌNH THÔNG TIN CHUNG -->
                <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                    <h5 class="fw-bold mb-4 text-dark">Thông tin chung dự án</h5>

                    <form action="<?= BASE_URL ?>/project/update" method="POST">
                        <input type="hidden" name="id" value="<?= $data['project']['id'] ?>">

                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-bold text-secondary">Tên dự án <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-secondary-subtle text-dark" name="name" value="<?= htmlspecialchars($data['project']['name']) ?>" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Mã dự án (Key) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-secondary-subtle text-dark" name="key" value="<?= htmlspecialchars($data['project']['key']) ?>" maxlength="10" placeholder="Ví dụ: WEB, API" required style="text-transform: uppercase;">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">* Lưu ý: Thay đổi Key sẽ tự động cập nhật lại mã hiển thị của tất cả Issue con.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Kho mã nguồn GitHub (Repository URL)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-github"></i></span>
                                    <input type="text" class="form-control border-secondary-subtle text-dark" name="github_repo_url" value="<?= htmlspecialchars($data['project']['github_repo_url'] ?? '') ?>" placeholder="https://github.com/username/repository">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Mô tả dự án</label>
                                <textarea class="form-control border-secondary-subtle text-dark" name="description" rows="5"><?= htmlspecialchars($data['project']['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="dashed-divider my-4" style="border-top: 1px dashed #cbd5e1; opacity: 0.8;"></div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= BASE_URL ?>/project/kanban/<?= $data['project']['id'] ?>" class="btn btn-outline-secondary px-4 py-2 rounded-pill">Quay lại Kanban</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold"><i class="bi bi-save me-1"></i>Lưu cấu hình</button>
                        </div>
                    </form>
                </div>

                <!-- KHỐI 2: DANGER ZONE (XÓA DỰ ÁN) -->
                <div class="card border border-danger-subtle p-4 bg-white shadow-sm" style="border-radius: 12px; background-color: #fffafb;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                        <h5 class="fw-bold text-danger mb-0">Vùng nguy hiểm (Danger Zone)</h5>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div style="max-width: 500px;">
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Xóa dự án này vĩnh viễn</h6>
                            <p class="text-secondary mb-0 small" style="line-height: 1.5;">Một khi đã xóa dự án, toàn bộ dữ liệu bao gồm thành viên, công việc (task), và lịch sử hoạt động sẽ biến mất vĩnh viễn và không thể khôi phục lại.</p>
                        </div>

                        <!-- Form gửi yêu cầu xóa bằng phương thức POST bảo mật -->
                        <form action="<?= BASE_URL ?>/project/delete" method="POST" onsubmit="return confirm('CẢNH BÁO CHÍ MẠNG: Bạn có chắc chắn muốn xóa dự án này cùng toàn bộ công việc liên quan? Hành động này không thể hoàn tác!');">
                            <input type="hidden" name="id" value="<?= $data['project']['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-1">
                                <i class="bi bi-trash3-fill"></i> Xóa dự án
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- CỘT PHẢI (35%): "SỨC KHỎE DỰ ÁN" DỊCH CHUYỂN TỪ TRANG MEMBERS SANG -->
        <div class="col-12 col-xl-4">
            <?php
            $total = $data['stats']['total_tasks'] ?? 0;
            $completed = $data['stats']['completed_tasks'] ?? 0;
            $percent = $total ? round(($completed / $total) * 100) : 0;
            ?>
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-activity text-danger me-2"></i>Sức khỏe dự án</h5>
                <p class="text-muted small">Chỉ số tiến độ hoàn thành các công việc được giao thuộc dự án này.</p>

                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-medium">Tiến độ hoàn thành:</span>
                    <span class="text-dark fw-bold"><?= $percent ?>%</span>
                </div>
                <div class="progress mb-3" style="height: 10px; border-radius: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%; border-radius: 10px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="row g-2 text-center mt-2">
                    <div class="col-6 py-2 bg-light rounded shadow-xs border-bottom border-3 border-primary">
                        <small class="text-secondary d-block" style="font-size: 0.8rem;">Tổng số Task</small>
                        <span class="h4 fw-bold text-dark"><?= $total ?></span>
                    </div>
                    <div class="col-6 py-2 bg-light rounded shadow-xs border-bottom border-3 border-success">
                        <small class="text-secondary d-block" style="font-size: 0.8rem;">Đã hoàn thành</small>
                        <span class="h4 fw-bold text-dark"><?= $completed ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>