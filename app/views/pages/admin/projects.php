<div class="page-content text-dark">
    
    <!-- TIÊU ĐỀ TRANG -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="fw-bold text-dark"><i class="bi bi-folder-fill text-primary me-2"></i>Quản lý toàn bộ dự án</h2>
            <p class="text-muted mb-0">Danh sách toàn hệ thống và điều phối quyền sở hữu dự án máy chủ TaskSync</p>
        </div>
    </div>

    <!-- THÔNG BÁO FLASH SESSIONS -->
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

    <!-- BỘ THỐNG KÊ NHANH TOÀN HỆ THỐNG -->
    <div class="dashboard-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; display: grid;">
        <div class="app-card p-3 bg-white shadow-sm" style="border-radius: 12px; border-bottom: 3px solid var(--primary);">
            <small class="text-secondary d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng số dự án</small>
            <strong class="fs-3 text-dark"><?= htmlspecialchars($data['global_total_projects'] ?? 0) ?></strong>
        </div>
        <div class="app-card p-3 bg-white shadow-sm" style="border-radius: 12px; border-bottom: 3px solid var(--success);">
            <small class="text-secondary d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng số công việc</small>
            <strong class="fs-3 text-success"><?= htmlspecialchars($data['global_total_tasks'] ?? 0) ?></strong>
        </div>
        <div class="app-card p-3 bg-white shadow-sm" style="border-radius: 12px; border-bottom: 3px solid var(--danger);">
            <small class="text-secondary d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng số thành viên</small>
            <strong class="fs-3 text-danger"><?= htmlspecialchars($data['global_total_users'] ?? 0) ?></strong>
        </div>
    </div>

    <!-- THANH TÌM KIẾM DỰ ÁN -->
    <div class="mb-4">
        <form action="<?= BASE_URL ?>/admin/projects" method="GET" class="d-flex gap-2" style="max-width: 500px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-secondary-subtle text-dark" name="q" value="<?= htmlspecialchars($data['search'] ?? '') ?>" placeholder="Tìm kiếm theo Tên, Key hoặc Trưởng dự án...">
            </div>
            <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">Tìm kiếm</button>
            <?php if (!empty($data['search'])): ?>
                <a href="<?= BASE_URL ?>/admin/projects" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH DỰ ÁN -->
    <div class="app-card" style="padding: 0; overflow: hidden; border-radius: 12px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <table class="table table-hover align-middle" style="width: 100; border-collapse: collapse; text-align: left; margin-bottom: 0;">
            <thead class="table-light">
                <tr style="font-size: 0.85rem;" class="text-secondary text-uppercase fw-bold">
                    <th style="padding: 16px 24px; width: 10%;">KEY</th>
                    <th style="padding: 16px 24px; width: 25%;">TÊN DỰ ÁN</th>
                    <th style="padding: 16px 24px; width: 20%;">MÔ TẢ</th>
                    <th style="padding: 16px 24px; width: 20%;">TRƯỞNG DỰ ÁN (OWNER)</th>
                    <th style="padding: 16px 24px; width: 15%;">SỐ LIỆU</th>
                    <th style="padding: 16px 24px; width: 10%; text-end">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['projects'])): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Không tìm thấy dự án phù hợp trên hệ thống.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['projects'] as $p):
                        $ownerFullName = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
                        $ownerDisplayName = !empty($ownerFullName) ? $ownerFullName : ($p['owner_name'] ?? 'System');

                        // Gọi đường dẫn tệp ảnh đại diện thật từ uploads
                        $avatarUrl = (!empty($p['avatar_url']) && $p['avatar_url'] !== 'default-avatar.png')
                            ? BASE_URL . '/uploads/avatars/' . $p['avatar_url']
                            : "https://ui-avatars.com/api/?name=" . urlencode($ownerDisplayName) . "&background=7c3aed&color=fff";
                    ?>
                        <tr style="border-bottom: 1px solid rgba(11,18,32,0.04); transition: background 0.2s;" onmouseover="this.style.background='rgba(11,18,32,0.01)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 16px 24px;">
                                <span class="priority-badge highest">
                                    <?= htmlspecialchars($p['key']) ?>
                                </span>
                            </td>
                            <!-- Tên dự án liên kết trỏ trực tiếp đến trang cấu hình dự án -->
                            <td style="padding: 16px 24px; font-weight: 600;">
                                <a href="<?= BASE_URL ?>/project/settings/<?= $p['id'] ?>" class="text-decoration-none" style="color: #0b1220; display: inline-flex; align-items: center; gap: 4px;" title="Đi đến cấu hình dự án">
                                    <span><?= htmlspecialchars($p['name']) ?></span>
                                    <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.72rem;"></i>
                                </a>
                            </td>
                            <td style="padding: 16px 24px; color: #5b6573; max-width: 200px;" class="text-truncate">
                                <?= htmlspecialchars($p['description'] ?? 'Không có mô tả.') ?>
                            </td>
                            <td style="padding: 16px 24px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="<?= $avatarUrl ?>" class="rounded-circle" width="30" height="30" style="object-fit: cover; border: 1px solid rgba(0,0,0,0.08);">
                                    <div>
                                        <div style="font-weight: 500; color: #0b1220; font-size: 0.9rem;"><?= htmlspecialchars($ownerDisplayName) ?></div>
                                        <div style="font-size: 0.75rem; color: #5b6573;">@<?= htmlspecialchars($p['owner_name'] ?? 'system') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 24px;">
                                <span class="badge bg-primary-subtle text-primary py-1 px-2.5 rounded me-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-people-fill"></i> <?= $p['member_count'] ?? 0 ?> Members
                                </span>
                                <span class="badge bg-success-subtle text-success py-1 px-2.5 rounded" style="font-size: 0.72rem;">
                                    <i class="bi bi-journal-text"></i> <?= $p['issue_count'] ?? 0 ?> Issues
                                </span>
                            </td>
                            <td style="padding: 16px 24px; text-align: right;">
                                <div style="display: inline-flex; gap: 8px; justify-content: flex-end; align-items: center;">

                                    <!-- Nút mở Modal thay đổi Owner -->
                                    <button type="button"
                                        class="app-btn app-btn-sm text-decoration-none"
                                        style="background: var(--primary); border-radius: 12px; padding: 6px 12px;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changeOwnerModal<?= $p['id'] ?>">
                                        <i class="bi bi-person-gear"></i>
                                        <span>Đổi Owner</span>
                                    </button>

                                    <!-- Nút xóa dự án vĩnh viễn -->
                                    <a href="<?= BASE_URL ?>/admin/deleteProject/<?= $p['id'] ?>"
                                        class="app-btn app-btn-sm text-decoration-none"
                                        style="background: var(--danger); border-radius: 12px; padding: 6px 12px;"
                                        onclick="return confirm('CẢNH BÁO CHÍ MẠNG: Bạn có chắc chắn muốn xóa vĩnh viễn dự án này cùng toàn bộ công việc và thành viên liên quan? Hành động này không thể hoàn tác!')">
                                        <i class="bi bi-trash3-fill"></i>
                                        <span>Xóa</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- THANH PHÂN TRANG -->
    <?php if (($data['total_pages'] ?? 1) > 1): ?>
        <nav aria-label="Page navigation" class="d-flex justify-content-center mt-4">
            <ul class="pagination shadow-sm" style="border-radius: 8px; overflow: hidden;">
                <li class="page-item <?= $data['current_page'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/admin/projects?q=<?= urlencode($data['search']) ?>&page=<?= $data['current_page'] - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                    <li class="page-item <?= $data['current_page'] == $i ? 'active' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/admin/projects?q=<?= urlencode($data['search']) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $data['current_page'] >= $data['total_pages'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/admin/projects?q=<?= urlencode($data['search']) ?>&page=<?= $data['current_page'] + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- ==============================================================
     VÙNG CHỨA CÁC ĐỐI TƯỢNG MODAL ĐỔI OWNER 
     ============================================================== -->
<?php if (!empty($data['projects'])): ?>
    <?php foreach ($data['projects'] as $p): ?>
        <div class="modal fade" id="changeOwnerModal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-gear text-primary me-2"></i>Thay đổi Trưởng dự án</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= BASE_URL ?>/admin/changeProjectOwner" method="POST">
                        <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                        <div class="modal-body py-3 px-4 text-dark">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Dự án hiện tại:</label>
                                <input type="text" class="form-control border-secondary-subtle text-muted bg-light" value="<?= htmlspecialchars($p['name']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Chọn Trưởng dự án mới <span class="text-danger">*</span></label>
                                <select class="form-select border-secondary-subtle text-dark" name="new_owner_id" required>
                                    <option value="" disabled selected>Chọn nhân viên quản lý...</option>
                                    <?php foreach (($data['users'] ?? []) as $user):
                                        $uFullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                        $uDisplayName = !empty($uFullName) ? $uFullName : $user['username'];
                                    ?>
                                        <option value="<?= $user['id'] ?>" <?= $user['id'] == $p['owner_id'] ? 'disabled' : '' ?>>
                                            <?= htmlspecialchars($uDisplayName) ?> (@<?= htmlspecialchars($user['username']) ?>) <?= $user['id'] == $p['owner_id'] ? '(Hiện tại)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 py-3 px-4 bg-light">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">Xác nhận thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>