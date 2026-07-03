<?php
// Khởi tạo kết nối CSDL nhanh để đọc cấu hình Đăng ký tự do (Chống lỗi phụ thuộc Controller) [210]
$db = new Database();
$pdo = $db->pdo;

try {
    $stmt = $pdo->query("SELECT `value` FROM system_settings WHERE `key` = 'allow_registration' LIMIT 1");
    $allowRegistration = $stmt ? $stmt->fetchColumn() : 'on';
} catch (Exception $e) {
    $allowRegistration = 'on'; // Dự phòng mặc định nếu chưa chạy SQL seed
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'Đăng nhập & Đăng ký | TaskSync' ?></title>
    <!-- Nhúng Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Nhúng Font chữ Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #0a0e27;
            --surface: #140d2c;
            --text: #eef6ff;
            --muted: #a3b1cc;
            --primary: #4318ff;
            --primary-strong: #3311de;
            --accent: #707eae;
            --danger: #f16565;
            --success: #39c08d;
            --border: rgba(255, 255, 255, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body,
        html {
            height: 100%;
            background-color: var(--bg);
            overflow: hidden;
        }

        .split-screen {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* PANEL TRÁI */
        .panel-left {
            flex: 1;
            background: radial-gradient(circle at top, rgba(167, 139, 250, 0.12), transparent 40%), #0a0e27;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            color: var(--text);
            position: relative;
        }

        .panel-left::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2;
        }

        .logo-box {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: #fff;
            font-weight: 800;
            font-size: 1.1rem;
            display: grid;
            place-items: center;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .promo-content {
            max-width: 520px;
            margin: auto 0;
            z-index: 2;
        }

        .promo-tag {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--accent);
            margin-bottom: 1rem;
            display: inline-block;
        }

        .promo-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .promo-desc {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .promo-badges {
            display: flex;
            gap: 0.75rem;
            margin-top: 2rem;
        }

        .promo-badge {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.5rem 1rem;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #fff;
        }

        .promo-badge i {
            color: var(--accent);
        }

        /* PANEL PHẢI */
        .panel-right {
            flex: 1;
            background-color: #f6f8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .auth-card {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(11, 18, 32, 0.06);
            box-shadow: 0 20px 45px rgba(11, 18, 32, 0.05);
            padding: 40px;
        }

        .auth-header {
            margin-bottom: 1.5rem;
        }

        .auth-header h2 {
            color: #0b1220;
            font-size: 1.85rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: #5b6573;
            font-size: 1rem;
        }

        .name-row {
            display: flex;
            gap: 1rem;
        }

        .name-col {
            flex: 1;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            color: #374151;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .auth-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            background: #f9fafb;
            color: #0b1220;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .auth-input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.08);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.95rem;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background: var(--primary-strong);
        }

        .auth-footer-links {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.92rem;
            color: #4b5563;
        }

        .auth-footer-links a {
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
        }

        .auth-footer-links a:hover {
            text-decoration: underline;
        }

        /* ALERTS */
        .app-alert {
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        .app-alert.danger {
            border: 1px solid rgba(241, 101, 101, 0.2);
            background: rgba(241, 101, 101, 0.08);
            color: var(--danger);
        }

        .app-alert.success {
            border: 1px solid rgba(57, 192, 141, 0.2);
            background: rgba(57, 192, 141, 0.08);
            color: var(--success);
        }

        /* CLASS ẨN CON */
        .hidden {
            display: none !important;
        }

        /* QUICK LOGIN FOR TEST */
        .quick-accounts-section {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px dashed #e5e7eb;
        }

        .quick-accounts-title {
            text-align: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .quick-btn {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.55rem 0.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
            width: 100%;
        }

        .quick-btn:hover {
            background: #eef2ff;
            border-color: var(--accent);
            transform: translateY(-1px);
        }

        .quick-btn i {
            font-size: 1rem;
            color: #4b5563;
        }

        .quick-btn:hover i {
            color: var(--primary);
        }

        .quick-role {
            font-size: 0.78rem;
            font-weight: 700;
            color: #1f2937;
        }

        .quick-name {
            font-size: 0.65rem;
            color: #6b7280;
        }

        @media screen and (max-width: 992px) {
            .panel-left {
                display: none;
            }

            .panel-right {
                padding: 20px;
            }
        }

        @media screen and (max-width: 480px) {
            .auth-card {
                padding: 24px 20px !important;
            }

            .quick-grid {
                grid-template-columns: 1fr !important;
                gap: 0.5rem;
            }

            .name-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>

    <div class="split-screen">
        <!-- PANEL TRÁI (DÙNG CHUNG) -->
        <div class="panel-left">
            <div class="brand-logo-area">
                <div class="logo-box">TS</div>
                <span class="brand-name">TaskSync</span>
            </div>
            <div class="promo-content">
                <span class="promo-tag">Next-gen Project Management</span>
                <h2 class="promo-title">Sắp xếp hoàn hảo.<br>Công việc trôi chảy.</h2>
                <p class="promo-desc">
                    Hệ thống quản lý công việc và dự án tinh gọn dành cho các đội nhóm.
                </p>
                <div class="promo-badges">
                    <div class="promo-badge"><i class="bi bi-lightning-charge-fill"></i> Epic Tracking</div>
                    <div class="promo-badge"><i class="bi bi-kanban"></i> Agile Sprint</div>
                    <div class="promo-badge"><i class="bi bi-sliders"></i> Admin Control</div>
                </div>
            </div>
            <div style="color: var(--muted); font-size: 0.85rem; z-index: 2;">
                TaskSync v1.0 | 2026
            </div>
        </div>

        <!-- PANEL PHẢI -->
        <div class="panel-right">
            <div class="auth-card">

                <!-- THÔNG BÁO LỖI / THÀNH CÔNG -->
                <?php if (isset($data['error'])): ?>
                    <div class="app-alert danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span><?= $data['error'] ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($data['success'])): ?>
                    <div class="app-alert success">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?= $data['success'] ?></span>
                    </div>
                <?php endif; ?>

                <!-- FORM ĐĂNG NHẬP -->
                <div id="login-section">
                    <div class="auth-header">
                        <h2>Chào mừng trở lại</h2>
                        <p>Đăng nhập vào không gian làm việc của bạn</p>
                    </div>

                    <form action="<?= BASE_URL ?>/auth/login" method="POST">
                        <div class="form-group">
                            <label for="login-username">EMAIL HOẶC USERNAME</label>
                            <input type="text" id="login-username" name="username" class="auth-input" placeholder="E.g., username or email" required>
                        </div>

                        <div class="form-group">
                            <div style="display: flex; justify-content: space-between;">
                                <label for="login-password">MẬT KHẨU</label>
                                <a href="#" class="forgot-link" style="font-size: 0.82rem;">Quên mật khẩu?</a>
                            </div>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input type="password" id="login-password" name="password" class="auth-input" placeholder="••••••••••••" required style="padding-right: 2.75rem;">
                                <button type="button" id="toggle-password-btn" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; height: 100%; padding: 0 4px; outline: none;" aria-label="Hiện/Ẩn mật khẩu">
                                    <i class="bi bi-eye" id="toggle-password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Đăng nhập</span> <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>

                    <!-- Nút Đăng ký chỉ hiện nếu trạng thái allowRegistration là 'on' -->
                    <?php if ($allowRegistration === 'on'): ?>
                        <div class="auth-footer-links">
                            Chưa có tài khoản? <a onclick="toggleAuth(true)">Đăng ký ngay</a>
                        </div>
                    <?php endif; ?>

                    <!-- Các thẻ đăng nhập nhanh -->
                    <div class="quick-accounts-section">
                        <div class="quick-accounts-title">Thử nghiệm nhanh hệ thống (Quick Accounts)</div>
                        <div class="quick-grid">
                            <button class="quick-btn" onclick="fillQuickAccount('admin', '123456')">
                                <i class="bi bi-shield-lock-fill"></i>
                                <span class="quick-role">Admin</span>
                                <span class="quick-name">Nguyễn Át Min</span>
                            </button>
                            <button class="quick-btn" onclick="fillQuickAccount('hung_le', '123456')">
                                <i class="bi bi-person-workspace"></i>
                                <span class="quick-role">PM</span>
                                <span class="quick-name">Lê Mạnh Hùng</span>
                            </button>
                            <button class="quick-btn" onclick="fillQuickAccount('member1', '123456')">
                                <i class="bi bi-person-fill"></i>
                                <span class="quick-role">Member</span>
                                <span class="quick-name">Văn Cường</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- FORM ĐĂNG KÝ (CHỈ HOẠT ĐỘNG KHI ĐƯỢC BẬT CẤU HÌNH) -->
                <?php if ($allowRegistration === 'on'): ?>
                    <div id="register-section" class="hidden">
                        <div class="auth-header">
                            <h2>Tạo tài khoản mới</h2>
                            <p>Bắt đầu thiết lập hệ thống quản lý công việc ngay lập tức</p>
                        </div>

                        <form action="<?= BASE_URL ?>/auth/register" method="POST">
                            <div class="name-row">
                                <div class="form-group name-col">
                                    <label for="last_name">HỌ</label>
                                    <input type="text" id="last_name" name="last_name" class="auth-input" placeholder="E.g., Nguyen" required>
                                </div>
                                <div class="form-group name-col">
                                    <label for="first_name">TÊN</label>
                                    <input type="text" id="first_name" name="first_name" class="auth-input" placeholder="E.g., Van A" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reg-email">EMAIL</label>
                                <input type="email" id="reg-email" name="email" class="auth-input" placeholder="E.g., email@example.com" required>
                            </div>

                            <div class="form-group">
                                <label for="reg-username">USERNAME</label>
                                <input type="text" id="reg-username" name="username" class="auth-input" placeholder="E.g., username" required>
                            </div>

                            <div class="form-group">
                                <label for="reg-password">MẬT KHẨU</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <input type="password" id="reg-password" name="password" class="auth-input" placeholder="••••••••" required style="padding-right: 2.75rem;">
                                    <button type="button" id="toggle-reg-password-btn" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; height: 100%; padding: 0 4px; outline: none;" aria-label="Hiện/Ẩn mật khẩu">
                                        <i class="bi bi-eye" id="toggle-reg-password-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="bi bi-person-plus-fill"></i> <span>Đăng ký thành viên</span>
                            </button>
                        </form>

                        <div class="auth-footer-links">
                            Đã có tài khoản? <a onclick="toggleAuth(false)">Đăng nhập</a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- JAVASCRIPT ĐIỀU KHIỂN CHUYỂN ĐỔI FORM -->
    <script>
        function toggleAuth(showRegister) {
            const loginSec = document.getElementById('login-section');
            const registerSec = document.getElementById('register-section');

            if (showRegister && registerSec) {
                loginSec.classList.add('hidden');
                registerSec.classList.remove('hidden');
            } else if (loginSec) {
                if (registerSec) registerSec.classList.add('hidden');
                loginSec.classList.remove('hidden');
            }
        }

        // Hàm tự động điền tài khoản nhanh
        function fillQuickAccount(username, password) {
            document.getElementById('login-username').value = username;
            document.getElementById('login-password').value = password;
        }

        // Hiện/Ẩn mật khẩu - Đăng nhập
        const loginPasswordInput = document.getElementById('login-password');
        const togglePasswordBtn = document.getElementById('toggle-password-btn');
        const togglePasswordIcon = document.getElementById('toggle-password-icon');

        if (togglePasswordBtn && loginPasswordInput && togglePasswordIcon) {
            togglePasswordBtn.addEventListener('click', function() {
                const type = loginPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                loginPasswordInput.setAttribute('type', type);
                if (type === 'text') {
                    togglePasswordIcon.classList.remove('bi-eye');
                    togglePasswordIcon.classList.add('bi-eye-slash');
                } else {
                    togglePasswordIcon.classList.remove('bi-eye-slash');
                    togglePasswordIcon.classList.add('bi-eye');
                }
            });
        }

        // Hiện/Ẩn mật khẩu - Đăng ký
        const regPasswordInput = document.getElementById('reg-password');
        const toggleRegPasswordBtn = document.getElementById('toggle-reg-password-btn');
        const toggleRegPasswordIcon = document.getElementById('toggle-reg-password-icon');

        if (toggleRegPasswordBtn && regPasswordInput && toggleRegPasswordIcon) {
            toggleRegPasswordBtn.addEventListener('click', function() {
                const type = regPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                regPasswordInput.setAttribute('type', type);
                if (type === 'text') {
                    toggleRegPasswordIcon.classList.remove('bi-eye');
                    toggleRegPasswordIcon.classList.add('bi-eye-slash');
                } else {
                    toggleRegPasswordIcon.classList.remove('bi-eye-slash');
                    toggleRegPasswordIcon.classList.add('bi-eye');
                }
            });
        }
    </script>

</body>

</html>