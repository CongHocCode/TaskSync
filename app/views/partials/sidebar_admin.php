<!-- Sidebar của admin -->
<?php
// Thông tin admin và ảnh đại diện
$userSession = $_SESSION['user'] ?? [];
$displayName = $userSession['display_name'] ?? ($userSession['username'] ?? 'Admin');
$avatarFile = $userSession['avatar_url'] ?? '';

// Nếu có file ảnh thật, lấy ảnh thật. Ngược lại, lấy ảnh chữ với nền đỏ #f16565
$adminAvatarUrl = (!empty($avatarFile) && $avatarFile !== 'default-avatar.png')
    ? BASE_URL . '/uploads/avatars/' . $avatarFile
    : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=f16565&color=fff";
?>

<style>
    @media (min-width: 992px) {
        .app-sidebar {
            position: relative;
            width: 280px;
            min-width: 75px;
            max-width: 500px;
            transition: width 0.05s ease;
            overflow-x: hidden;
        }

        .sidebar-resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 100%;
            cursor: col-resize;
            background: transparent;
            z-index: 10;
            transition: background 0.2s;
        }

        .sidebar-resizer:hover,
        .sidebar-resizer.active {
            background: rgba(255, 255, 255, 0.15);
        }

        .app-sidebar.collapsed {
            width: 75px !important;
        }

        .app-sidebar.collapsed h1,
        .app-sidebar.collapsed .sidebar-section-label,
        .app-sidebar.collapsed .sidebar-link span,
        .app-sidebar.collapsed .sidebar-link .badge,
        .app-sidebar.collapsed .sidebar-project-toggle span,
        .app-sidebar.collapsed .sidebar-project-toggle .bi-chevron-down,
        .app-sidebar.collapsed .user-info,
        .app-sidebar.collapsed .user-menu-btn,
        .app-sidebar.collapsed .sidebar-project-nav {
            display: none !important;
        }

        .app-sidebar.collapsed .app-btn-create-issue {
            font-size: 0 !important;
            padding: 10px 0;
            justify-content: center;
        }

        .app-sidebar.collapsed .app-btn-create-issue i {
            font-size: 1.5rem !important;
            margin: 0;
        }

        .app-sidebar.collapsed .sidebar-collapse i {
            transform: rotate(180deg);
            display: inline-block;
        }
    }

    .sidebar-link,
    .sidebar-project-toggle,
    .sidebar-brand,
    .sidebar-user {
        display: flex;
        align-items: center;
        white-space: nowrap;
        width: 100%;
        min-width: 0;
    }

    .sidebar-link span,
    .sidebar-project-toggle span,
    .user-info .user-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: left;
    }

    .app-sidebar i {
        min-width: 20px;
    }
</style>

<aside class="app-sidebar">
    <div class="sidebar-resizer"></div>

    <div class="sidebar-brand">
        <!-- Logo của Admin:chữ A (Admin) -->
        <div class="sidebar-logo" style="background: rgba(241, 101, 101, 0.15); color: var(--danger); cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/admin/dashboard'">A</div>
        <div style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/admin/dashboard'">
            <h1>Admin Console</h1>
        </div>
        <button class="sidebar-collapse" aria-label="Thu gọn sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <!-- Nút hành động nhanh của Admin: Đổi thành Thêm Nhân Viên Mới -->
    <a href="<?= BASE_URL ?>/admin/createUser" class="app-btn app-btn-create-issue" style="background: var(--primary);">
        <i class="bi bi-person-plus-fill"></i> Tạo nhân sự mới
    </a>

    <!-- PHẦN 1: QUẢN TRỊ HỆ THỐNG -->
    <div class="sidebar-section">
        <div class="sidebar-section-label">HỆ THỐNG</div>
        <nav class="sidebar-nav">
            <!-- Link về trang Thống kê tổng của Admin -->
            <a href="<?= BASE_URL ?>/admin/dashboard" class="sidebar-link active">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Thống kê hệ thống</span>
            </a>
            <!-- Link về trang CRUD User -->
            <a href="<?= BASE_URL ?>/admin/users" class="sidebar-link">
                <i class="bi bi-people-fill"></i>
                <span>Quản lý nhân sự</span>
            </a>
            <!-- Link về trang quản lý toàn bộ Dự án của công ty -->
            <a href="<?= BASE_URL ?>/admin/projects" class="sidebar-link">
                <i class="bi bi-folder-fill"></i>
                <span>Quản lý dự án</span>
            </a>
        </nav>
    </div>

    <!-- PHẦN 2: CẤU HÌNH -->
    <div class="sidebar-section">
        <div class="sidebar-section-label">CẤU HÌNH</div>
        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>/admin/settings" class="sidebar-link">
                <i class="bi bi-gear-fill"></i>
                <span>Cài đặt hệ thống</span>
            </a>
        </nav>
    </div>

    <!-- THÔNG TIN ADMIN LẤY ĐỘNG TỪ SESSION -->
    <div class="sidebar-user" style="position: relative;">
        <!-- Avatar lấy động từ Session -->
        <img src="<?= $adminAvatarUrl ?>" alt="Avatar" class="user-avatar" style="object-fit: cover;">
        <div class="user-info">
            <div class="user-name"><?= $_SESSION['user']['display_name'] ?? 'Admin' ?></div>
            <div class="user-role"><?= $_SESSION['user']['role'] ?? 'ADMIN' ?></div>
        </div>

        <a href="<?= BASE_URL ?>/user/profile" class="stretched-link" title="Cài đặt tài khoản"></a>

        <a href="<?= BASE_URL ?>/auth/logout" class="user-menu-btn text-decoration-none d-flex align-items-center justify-content-center" title="Đăng xuất" style="color: #ff4d4f !important; z-index: 2; position: relative;">
            <i class="bi bi-box-arrow-right" style="font-size: 1.2rem;"></i>
        </a>
    </div>
</aside>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ===== TỰ ĐỘNG ĐỔI MÀU NÚT THEO TRANG HIỆN TẠI =====
        const currentUrl = window.location.href.split('?')[0].split('#')[0];
        const sidebarLinks = document.querySelectorAll('.app-sidebar .sidebar-link');
        let matchedAny = false;

        sidebarLinks.forEach(link => {
            // Xóa sạch trạng thái active được gán cứng ban đầu
            link.classList.remove('active');
            
            // So sánh URL sạch của thẻ với URL thực tế trên trình duyệt
            if (link.href && currentUrl === link.href.split('?')[0].split('#')[0]) {
                link.classList.add('active');
                matchedAny = true;
            }
        });

        // Nếu không trùng khớp bất kì URL nào, mặc định kích hoạt nút đầu tiên (Thống kê hệ thống)
        if (!matchedAny && sidebarLinks.length > 0) {
            const firstLink = document.querySelector('.app-sidebar .sidebar-nav a');
            if (firstLink) firstLink.classList.add('active');
        }

        // ===== XỬ LÝ THU PHÓNG VÀ KÉO GIÃN SIDEBAR =====
        const toggleBtn = document.querySelector(".sidebar-collapse");
        const sidebar = document.querySelector(".app-sidebar");
        const resizer = document.querySelector(".sidebar-resizer");

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener("click", function(e) {
                if (window.innerWidth > 992) {
                    e.preventDefault();
                    sidebar.classList.toggle("collapsed");
                    sidebar.style.width = '';
                }
            });
        }

        if (resizer && sidebar) {
            resizer.addEventListener("mousedown", function(e) {
                e.preventDefault();
                resizer.classList.add("active");

                document.addEventListener("mousemove", resize);
                document.addEventListener("mouseup", stopResize);
            });

            function resize(e) {
                let newWidth = e.clientX;
                if (newWidth >= 75 && newWidth <= 500) {
                    sidebar.style.width = newWidth + "px";
                    if (newWidth < 140) {
                        sidebar.classList.add("collapsed");
                    } else {
                        sidebar.classList.remove("collapsed");
                    }
                }
            }

            function stopResize() {
                resizer.classList.remove("active");
                document.removeEventListener("mousemove", resize);
                document.removeEventListener("mouseup", stopResize);
            }
        }
    });
</script>