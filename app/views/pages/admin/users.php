<div class="page-content text-dark">

    <!-- TIÊU ĐỀ TRANG VÀ NÚT TẠO MỚI -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Quản lý nhân sự</h2>
            <p class="text-muted mb-0">Danh sách toàn bộ nhân sự và phân quyền tài khoản hệ thống TaskSync</p>
        </div>
        <!-- Nút thêm nhân viên -->
        <a href="<?= BASE_URL ?>/admin/createUser" class="app-btn text-decoration-none">
            <i class="bi bi-person-plus-fill"></i> <span>Tạo nhân sự mới</span>
        </a>
    </div>

    <!-- BỘ THÔNG BÁO FLASH SESSIONS -->
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0 border-start border-4 border-danger" role="alert" style="background-color: #fef2f2; color: #b91c1c;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="fw-medium"><?= $_SESSION['flash_error'] ?></div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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

    <!-- BỘ THỐNG KÊ NHANH NHÂN SỰ TOÀN HỆ THỐNG (Đọc từ số liệu toàn cục thực tế) -->
    <div class="dashboard-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; display: grid;">
        <div class="app-card p-3 bg-white shadow-sm" style="border-radius: 12px; border-bottom: 3px solid var(--primary);">
            <small class="text-secondary d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng nhân sự hệ thống</small>
            <strong class="fs-3 text-dark"><?= htmlspecialchars($data['global_total'] ?? 0) ?></strong>
        </div>
        <div class="app-card p-3 bg-white shadow-sm" style="border-radius: 12px; border-bottom: 3px solid var(--success);">
            <small class="text-secondary d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Đang hoạt động</small>
            <strong class="fs-3 text-success"><?= htmlspecialchars($data['global_active'] ?? 0) ?></strong>
        </div>
        <div class="app-card p-3 bg-white shadow-sm" style="border-radius: 12px; border-bottom: 3px solid var(--danger);">
            <small class="text-secondary d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tài khoản bị khóa</small>
            <strong class="fs-3 text-danger"><?= htmlspecialchars($data['global_blocked'] ?? 0) ?></strong>
        </div>
    </div>

    <!-- THANH TÌM KIẾM NHÂN SỰ DỘNG -->
    <div class="mb-4">
        <form action="<?= BASE_URL ?>/admin/users" method="GET" class="d-flex gap-2" style="max-width: 500px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-secondary-subtle text-dark" name="q" value="<?= htmlspecialchars($data['search'] ?? '') ?>" placeholder="Tìm kiếm theo Tên, Username hoặc Email...">
            </div>
            <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">Tìm kiếm</button>
            <?php if (!empty($data['search'])): ?>
                <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH NHÂN SỰ -->
    <div class="app-card" style="padding: 0; overflow: hidden; border-radius: 12px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <table class="table" style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 0;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(11,18,32,0.08); background: #fbfbfd;">
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">NHÂN SỰ</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">EMAIL</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">VAI TRÒ</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">TRẠNG THÁI</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem; text-align: right;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['users'])): ?>
                    <tr>
                        <td colspan="5" style="padding: 32px; text-align: center; color: #5b6573;">Không tìm thấy nhân sự phù hợp trên hệ thống.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['users'] as $u):
                        $fullName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                        $displayName = !empty($fullName) ? $fullName : $u['username'];

                        $avatarUrl = (!empty($u['avatar_url']) && $u['avatar_url'] !== 'default-avatar.png')
                            ? BASE_URL . '/uploads/avatars/' . $u['avatar_url']
                            : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=7c3aed&color=fff";
                    ?>
                        <tr style="border-bottom: 1px solid rgba(11,18,32,0.04); transition: background 0.2s;" onmouseover="this.style.background='rgba(11,18,32,0.01)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 16px 24px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="<?= $avatarUrl ?>" class="rounded-circle" width="36" height="36" style="object-fit: cover; border: 1px solid rgba(0,0,0,0.08);">
                                    <div>
                                        <div style="font-weight: 600; color: #0b1220;"><?= htmlspecialchars($displayName) ?></div>
                                        <div style="font-size: 0.82rem; color: #5b6573;">@<?= htmlspecialchars($u['username']) ?> <span class="text-muted small">(ID: <?= $u['id'] ?>)</span></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 24px; color: #5b6573;"><?= htmlspecialchars($u['email']) ?></td>
                            <td style="padding: 16px 24px;">
                                <span class="priority-badge <?= $u['role'] === 'admin' ? 'highest' : 'medium' ?>">
                                    <?= strtoupper(htmlspecialchars($u['role'])) ?>
                                </span>
                            </td>
                            <td style="padding: 16px 24px;">
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 600; 
                                         background: <?= $u['status'] === 'active' ? 'rgba(57,192,141,0.12)' : 'rgba(241,101,101,0.12)' ?>;
                                         color: <?= $u['status'] === 'active' ? 'var(--success)' : 'var(--danger)' ?>;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: currentcolor;"></span>
                                    <?= $u['status'] === 'active' ? 'Hoạt động' : 'Bị khóa' ?>
                                </span>
                            </td>
                            <td style="padding: 16px 24px; text-align: right;">
                                <div style="display: inline-flex; gap: 8px; justify-content: flex-end; align-items: center;">

                                    <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                                        <a href="<?= BASE_URL ?>/admin/toggleUserStatus/<?= $u['id'] ?>"
                                            class="app-btn app-btn-sm text-decoration-none"
                                            style="background: <?= $u['status'] === 'active' ? 'var(--danger)' : 'var(--success)' ?>; border-radius: 12px; padding: 6px 12px;"
                                            onclick="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hoạt động của nhân sự này?')">
                                            <i class="bi <?= $u['status'] === 'active' ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                                            <span><?= $u['status'] === 'active' ? 'Khóa' : 'Mở khóa' ?></span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small px-2"><i class="bi bi-shield-fill-check"></i> Đang dùng</span>
                                    <?php endif; ?>

                                    <a href="<?= BASE_URL ?>/admin/editUser/<?= $u['id'] ?>" class="app-btn app-btn-sm app-btn-ghost" title="Sửa thông tin" style="text-decoration: none;">
                                        <i class="bi bi-pencil-square" style="color: var(--primary);"></i>
                                    </a>
                                    <button type="button" class="app-btn app-btn-sm app-btn-ghost p-1" title="Cấp lại mật khẩu" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?= $u['id'] ?>">
                                        <i class="bi bi-key-fill text-warning" style="font-size: 1.1rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- THANH PHÂN TRANG (PAGINATION BAR) -->
    <?php if (($data['total_pages'] ?? 1) > 1): ?>
        <nav aria-label="Page navigation" class="d-flex justify-content-center mt-4">
            <ul class="pagination shadow-sm" style="border-radius: 8px; overflow: hidden;">
                <!-- Nút trang trước -->
                <li class="page-item <?= $data['current_page'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/admin/users?q=<?= urlencode($data['search']) ?>&page=<?= $data['current_page'] - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Danh sách số trang -->
                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                    <li class="page-item <?= $data['current_page'] == $i ? 'active' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/admin/users?q=<?= urlencode($data['search']) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Nút trang sau -->
                <li class="page-item <?= $data['current_page'] >= $data['total_pages'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/admin/users?q=<?= urlencode($data['search']) ?>&page=<?= $data['current_page'] + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php if (!empty($data['users'])): ?>
    <?php foreach ($data['users'] as $u): 
        $fullName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
        $displayName = !empty($fullName) ? $fullName : $u['username'];
    ?>
        <div class="modal fade" id="resetPasswordModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true" style="color: #0b1220;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <!-- Modal Header -->
                    <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center"><i class="bi bi-key-fill text-warning me-2"></i>Cấp mật khẩu mới cho nhân sự</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Modal Form -->
                    <form action="<?= BASE_URL ?>/admin/resetUserPassword" method="POST">
                        <!-- ID Nhân sự được ẩn để gửi lên cho Controller hứng -->
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        
                        <div class="modal-body py-3 px-4 text-dark text-start">
                            <!-- Hiển thị thông tin người dùng được cấp -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Nhân sự được cấp:</label>
                                <input type="text" class="form-control border-secondary-subtle text-muted bg-light" value="<?= htmlspecialchars($displayName) ?> (@<?= htmlspecialchars($u['username']) ?>)" disabled>
                            </div>
                            
                            <!-- Nhập mật khẩu mới -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Nhập mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-secondary-subtle text-dark" name="new_password" placeholder="Nhập tối thiểu 6 ký tự..." required minlength="6">
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="modal-footer border-top-0 py-3 px-4 bg-light">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-warning px-4 py-2 fw-bold text-white" style="background-color: var(--danger); border-color: var(--danger);">Xác nhận cấp mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>