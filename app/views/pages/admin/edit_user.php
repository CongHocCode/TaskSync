<style>
    .app-card label { color: #374151 !important; font-weight: 700; margin-bottom: 8px; }
    .app-card .app-input { background-color: #f9fafb !important; border: 1px solid #d1d5db !important; color: #0b1220 !important; transition: all 0.2s ease; }
    .app-card .app-input:focus { border-color: var(--primary) !important; background-color: #ffffff !important; box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1) !important; }
</style>

<div class="page-content">
    <div class="page-header" style="max-width: 600px; margin: 0 auto 24px;">
        <h2>Sửa thông tin nhân sự</h2>
        <p>Cập nhật hồ sơ và vai trò hệ thống của thành viên: <strong><?= $data['user']['first_name'] . ' ' . $data['user']['last_name'] ?></strong></p>
    </div>

    <div class="app-card" style="max-width: 600px; margin: 0 auto;">
        <?php if (isset($data['error'])): ?>
            <div class="app-alert danger" style="margin-bottom: 20px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?= $data['error'] ?></span>
            </div>
        <?php endif; ?>

        <!-- FORM SUBMIT VỀ ACTION EDITUSER KÈM ID -->
        <form action="<?= BASE_URL ?>/admin/editUser/<?= $data['user']['id'] ?>" method="POST">
            <div style="display: flex; gap: 1rem;">
                <div class="app-form-group" style="flex: 1;">
                    <label for="last_name">HỌ</label>
                    <input type="text" id="last_name" name="last_name" class="app-input" value="<?= $data['user']['last_name'] ?>" required>
                </div>
                <div class="app-form-group" style="flex: 1;">
                    <label for="first_name">TÊN</label>
                    <input type="text" id="first_name" name="first_name" class="app-input" value="<?= $data['user']['first_name'] ?>" required>
                </div>
            </div>

            <div class="app-form-group">
                <label for="email">ĐỊA CHỈ EMAIL</label>
                <input type="email" id="email" name="email" class="app-input" value="<?= $data['user']['email'] ?>" required>
            </div>

            <div class="app-form-group">
                <label for="username">TÊN ĐĂNG NHẬP (USERNAME)</label>
                <input type="text" id="username" name="username" class="app-input" value="<?= $data['user']['username'] ?>" required>
            </div>

            <div class="app-form-group">
                <label for="role">VAI TRÒ TRÊN HỆ THỐNG</label>
                <select id="role" name="role" class="app-input" style="background: #ffffff; color: #0b1220; border: 1px solid #d1d5db;">
                    <option value="user" <?= $data['user']['role'] === 'user' ? 'selected' : '' ?>>MEMBER (Nhân viên thông thường)</option>
                    <option value="admin" <?= $data['user']['role'] === 'admin' ? 'selected' : '' ?>>ADMIN (Quản trị viên hệ thống)</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 24px;">
                <button type="submit" class="app-btn" style="flex: 1;">
                    <i class="bi bi-save-fill"></i> <span>Lưu thay đổi</span>
                </button>
                <a href="<?= BASE_URL ?>/admin/users" class="app-btn app-btn-ghost" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>