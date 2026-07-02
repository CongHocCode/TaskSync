<style>
    .form-switch .form-check-input {
        width: 44px !important;
        height: 22px !important;
        margin-left: 0 !important;
        cursor: pointer;
        background-color: #cbd5e1 !important;
        border: 1px solid #cbd5e1 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e") !important;
        background-position: left center !important;
        transition: background-position .15s ease-in-out, background-color .15s ease-in-out !important;
    }

    .form-switch .form-check-input:checked {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
        background-position: right center !important;
    }
</style>

<div class="container-fluid py-4 text-dark">
    <div class="mb-4">
        <h1 class="h3 mb-2 fw-bold text-dark">
            <i class="bi bi-gear-wide-connected text-primary me-2"></i>Cấu hình hệ thống
        </h1>
        <p class="text-muted" style="font-size: 0.95rem;">Quản lý các thiết lập toàn cục, chế độ vận hành và bảo mật của máy chủ TaskSync.</p>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0 border-start border-4 border-success" role="alert" style="background-color: #f0fdf4; color: #15803d;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div class="fw-medium"><?= $_SESSION['flash_success'] ?></div>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                <h5 class="fw-bold mb-4 text-dark">Thiết lập tham số toàn cục</h5>

                <form action="<?= BASE_URL ?>/admin/settings" method="POST">
                    <div class="row g-4">
                        <!-- 1. Giới hạn dung lượng upload ảnh -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-secondary">Dung lượng tải lên tối đa (MB)</label>
                            <input type="number" class="form-control border-secondary-subtle text-dark" name="max_upload_size" value="<?= htmlspecialchars($data['settings']['max_upload_size'] ?? 2) ?>" min="1" max="10" required>
                        </div>

                        <div class="dashed-divider col-12 my-2" style="border-top: 1px dashed #cbd5e1; opacity: 0.8;"></div>

                        <!-- 2. Công tắc Đăng ký tự do (Allow Registration) -->
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Đăng ký tự do ngoài trang chủ</h6>
                                <p class="text-secondary mb-0 small" style="max-width: 500px;">Khi tắt chế độ này, nút Đăng ký ngoài trang chủ sẽ ẩn đi. Chỉ có Admin mới được tạo tài khoản trong Admin Console.</p>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" name="allow_registration" value="on" <?= ($data['settings']['allow_registration'] ?? 'on') === 'on' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="dashed-divider col-12 my-2" style="border-top: 1px dashed #cbd5e1; opacity: 0.8;"></div>

                        <!-- 3. Công tắc Bảo trì hệ thống (Maintenance Mode) -->
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Kích hoạt bảo trì hệ thống (Maintenance Mode)</h6>
                                <p class="text-secondary mb-0 small" style="max-width: 500px;">Khi bật chế độ này, chỉ tài khoản Quản trị viên (Admin) mới có thể đăng nhập vào hệ thống để bảo trì máy chủ.</p>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" name="maintenance_mode" value="on" <?= ($data['settings']['maintenance_mode'] ?? 'off') === 'on' ? 'checked' : '' ?>>
                            </div>
                        </div>
                    </div>

                    <div class="dashed-divider my-4" style="border-top: 1px dashed #cbd5e1; opacity: 0.8;"></div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold"><i class="bi bi-save me-1"></i>Lưu cấu hình</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>