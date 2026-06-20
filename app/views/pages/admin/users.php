<div class="page-content">
    <!-- TIÊU ĐỀ TRANG VÀ NÚT TẠO MỚI -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2>Quản lý nhân sự</h2>
            <p>Danh sách toàn bộ nhân sự và phân quyền tài khoản hệ thống TaskSync</p>
        </div>
        <!-- Nút thêm nhân viên chuẩn style T2 -->
        <a href="<?= BASE_URL ?>/admin/createUser" class="app-btn">
            <i class="bi bi-person-plus-fill"></i> <span>Tạo nhân sự mới</span>
        </a>
    </div>

    <!-- BẢNG DANH SÁCH NHÂN SỰ -->
    <div class="app-card" style="padding: 0; overflow: hidden;">
        <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(11,18,32,0.08); background: #fbfbfd;">
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">ID</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">HỌ VÀ TÊN</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">EMAIL</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">VAI TRÒ</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem;">TRẠNG THÁI</th>
                    <th style="padding: 16px 24px; font-weight: 600; color: #5b6573; font-size: 0.85rem; text-align: right;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['users'])): ?>
                <tr>
                    <td colspan="6" style="padding: 32px; text-align: center; color: #5b6573;">Chưa có nhân sự nào trên hệ thống.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($data['users'] as $u): ?>
                    <tr style="border-bottom: 1px solid rgba(11,18,32,0.04); transition: background 0.2s;" onmouseover="this.style.background='rgba(11,18,32,0.01)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px 24px; color: #5b6573;"><?= $u['id'] ?></td>
                        <td style="padding: 16px 24px;">
                            <div style="font-weight: 600; color: #0b1220;"><?= $u['first_name'] . ' ' . $u['last_name'] ?></div>
                            <div style="font-size: 0.82rem; color: #5b6573;">@<?= $u['username'] ?></div>
                        </td>
                        <td style="padding: 16px 24px; color: #5b6573;"><?= $u['email'] ?></td>
                        <td style="padding: 16px 24px;">
                            <!-- Sử dụng badge vai trò của T2 -->
                            <span class="priority-badge <?= $u['role'] === 'admin' ? 'high' : 'medium' ?>">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td style="padding: 16px 24px;">
                            <!-- Trạng thái hoạt động dạng Pill mềm mại -->
                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 600; 
                                         background: <?= $u['status'] === 'active' ? 'rgba(57,192,141,0.12)' : 'rgba(241,101,101,0.12)' ?>;
                                         color: <?= $u['status'] === 'active' ? 'var(--success)' : 'var(--danger)' ?>;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: currentcolor;"></span>
                                <?= $u['status'] === 'active' ? 'Hoạt động' : 'Bị khóa' ?>
                            </span>
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <!-- Nút thay đổi trạng thái (Bật/Tắt khóa) -->
                                <a href="<?= BASE_URL ?>/admin/toggleUserStatus/<?= $u['id'] ?>" 
                                   class="app-btn app-btn-sm" 
                                   style="background: <?= $u['status'] === 'active' ? 'var(--danger)' : 'var(--success)' ?>; border-radius: 12px; padding: 6px 12px;"
                                   onclick="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hoạt động của nhân sự này?')">
                                    <i class="bi <?= $u['status'] === 'active' ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                                    <span><?= $u['status'] === 'active' ? 'Khóa' : 'Mở khóa' ?></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>