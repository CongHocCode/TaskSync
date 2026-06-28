<!-- app/views/pages/workspace/profile.php -->
<div class="container py-4" style="max-width: 900px;">
    <!-- Tiêu đề trang -->
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold text-dark">
            <i class="bi bi-person-gear text-primary me-2"></i>Cài đặt tài khoản
        </h1>
        <p class="text-muted mb-0">Cập nhật thông tin cá nhân của bạn và thiết lập bảo mật mật khẩu.</p>
    </div>

    <!-- BỘ BÁO LỖI FLASH SESSION -->
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
        <!-- CỘT 1 (35%): AVATAR & THÔNG TIN CHUNG -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center bg-white" style="border-radius: 12px;">
                <?php
                $displayName = trim(($data['user']['first_name'] ?? '') . ' ' . ($data['user']['last_name'] ?? ''));
                $displayName = !empty($displayName) ? $displayName : $data['user']['username'];
                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=7c3aed&color=fff&size=100";
                ?>
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-sm" width="100" height="100" alt="Avatar">
                </div>

                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($displayName) ?></h5>
                <span class="badge bg-secondary-subtle text-secondary text-uppercase mb-3 px-3 py-1.5 small">
                    <?= htmlspecialchars($data['user']['role'] ?? 'MEMBER') ?>
                </span>

                <div class="dashed-divider my-3" style="border-top: 1px dashed #cbd5e1; opacity: 0.8;"></div>

                <div class="text-start">
                    <small class="text-secondary d-block">Tên đăng nhập:</small>
                    <span class="text-dark fw-semibold">@<?= htmlspecialchars($data['user']['username'] ?? '') ?></span>

                    <small class="text-secondary d-block mt-2">Trạng thái tài khoản:</small>
                    <span class="badge bg-success-subtle text-success small px-2 py-1">
                        <i class="bi bi-check-circle me-1"></i>Hoạt động
                    </span>
                </div>
            </div>
        </div>

        <!-- CỘT 2 (65%): CÁC FORM CHỈNH SỬA CHI TIẾT -->
        <div class="col-12 col-md-8">
            <div class="d-flex flex-column gap-4">

                <!-- KHỐI FORM 1: CẬP NHẬT THÔNG TIN CÁ NHÂN -->
                <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Thông tin cá nhân</h5>

                    <form action="<?= BASE_URL ?>/user/updateInfo" method="POST">
                        <input type="hidden" name="update_info" value="1">

                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-bold text-secondary">Họ (First Name)</label>
                                <input type="text" class="form-control border-secondary-subtle text-dark" name="first_name" value="<?= htmlspecialchars($data['user']['first_name'] ?? '') ?>" placeholder="Nhập họ của bạn...">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-bold text-secondary">Tên (Last Name)</label>
                                <input type="text" class="form-control border-secondary-subtle text-dark" name="last_name" value="<?= htmlspecialchars($data['user']['last_name'] ?? '') ?>" placeholder="Nhập tên của bạn...">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Địa chỉ Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control border-secondary-subtle text-dark" name="email" value="<?= htmlspecialchars($data['user']['email'] ?? '') ?>" required placeholder="username@example.com">
                            </div>
                        </div>

                        <div class="dashed-divider my-4" style="border-top: 1px dashed #cbd5e1; opacity: 0.8;"></div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Lưu thông tin</button>
                        </div>
                    </form>
                </div>

                <!-- KHỐI FORM 2: ĐỔI MẬT KHẨU -->
                <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Đổi mật khẩu bảo mật</h5>

                    <form action="<?= BASE_URL ?>/user/changePassword" method="POST">
                        <input type="hidden" name="change_password" value="1">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-secondary-subtle text-dark" name="current_password" required placeholder="Nhập mật khẩu đang dùng...">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-bold text-secondary">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-secondary-subtle text-dark" name="new_password" required placeholder="Tối thiểu 6 ký tự...">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-bold text-secondary">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-secondary-subtle text-dark" name="confirm_password" required placeholder="Nhập lại mật khẩu mới...">
                            </div>
                        </div>

                        <div class="dashed-divider my-4" style="border-top: 1px dashed #cbd5e1; opacity: 0.8;"></div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Cập nhật mật khẩu</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>