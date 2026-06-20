<style>
    /* Đổi màu label sang màu tối */
    .app-card label {
        color: #374151 !important;
        font-weight: 700;
        margin-bottom: 8px;
    }

    /* Chỉnh lại màu ô nhập liệu cho nổi hơn */
    .app-card .app-input {
        background-color: #f9fafb !important;
        /* Đổi sang nền xám nhạt cực sạch */
        border: 1px solid #d1d5db !important;
        /* Hiện rõ viền màu xám */
        color: #0b1220 !important;
        /* Chữ gõ vào màu đen đậm */
        transition: all 0.2s ease;
    }

    .app-card .app-input::placeholder {
        color: #9ca3af !important;
        /* Màu xám vừa phải, dễ đọc */
        opacity: 1;
    }

    .app-card .app-input:focus {
        border-color: var(--primary) !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1) !important;
        /* Hào quang tím nhạt chuẩn SaaS */
    }
</style>
<div class="page-content">
    <div class="page-header" style="max-width: 600px; margin: 0 auto 24px;">
        <h2>Tạo nhân sự mới</h2>
        <p>Thiết lập tài khoản và cấp quyền truy cập hệ thống TaskSync cho thành viên mới</p>
    </div>

    <!-- Khung Card bọc Form của T2 -->
    <div class="app-card" style="max-width: 600px; margin: 0 auto;">

        <!-- HIỂN THỊ THÔNG BÁO LỖI NẾU CÓ -->
        <?php if (isset($data['error'])): ?>
            <div class="app-alert danger" style="margin-bottom: 20px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?= $data['error'] ?></span>
            </div>
        <?php endif; ?>

        <!-- FORM SUBMIT VỀ ACTION CREATEUSER -->
        <form action="<?= BASE_URL ?>/admin/createUser" method="POST">
            <!-- HỌ VÀ TÊN SONG SONG NẰM TRÊN MỘT HÀNG -->
            <div style="display: flex; gap: 1rem;">
                <div class="app-form-group" style="flex: 1;">
                    <label for="last_name">HỌ</label>
                    <input type="text" id="last_name" name="last_name" class="app-input" placeholder="E.g., Nguyễn" required>
                </div>
                <div class="app-form-group" style="flex: 1;">
                    <label for="first_name">TÊN</label>
                    <input type="text" id="first_name" name="first_name" class="app-input" placeholder="E.g., Văn A" required>
                </div>
            </div>

            <div class="app-form-group">
                <label for="email">ĐỊA CHỈ EMAIL</label>
                <input type="email" id="email" name="email" class="app-input" placeholder="E.g., member@tasksync.vn" required>
            </div>

            <div class="app-form-group">
                <label for="username">TÊN ĐĂNG NHẬP (USERNAME)</label>
                <input type="text" id="username" name="username" class="app-input" placeholder="E.g., user123" required>
            </div>

            <div class="app-form-group">
                <label for="password">MẬT KHẨU KHỞI TẠO</label>
                <input type="password" id="password" name="password" class="app-input" placeholder="••••••••" required>
            </div>

            <div class="app-form-group">
                <label for="role">VAI TRÒ TRÊN HỆ THỐNG</label>
                <select id="role" name="role" class="app-input" style="background: #ffffff; color: #0b1220; border: 1px solid #d1d5db;">
                    <option value="user">MEMBER (Nhân viên thông thường)</option>
                    <option value="admin">ADMIN (Quản trị viên hệ thống)</option>
                </select>
            </div>

            <!-- NÚT ĐỒNG Ý / HỦY BỎ -->
            <div style="display: flex; gap: 1rem; margin-top: 24px;">
                <button type="submit" class="app-btn" style="flex: 1;">
                    <i class="bi bi-person-plus-fill"></i> <span>Cấp tài khoản</span>
                </button>
                <a href="<?= BASE_URL ?>/admin/users" class="app-btn app-btn-ghost" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>