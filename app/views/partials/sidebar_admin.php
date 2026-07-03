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
    /* Khai báo container cho sidebar */
    .app-sidebar {
        container-type: inline-size;
        container-name: sidebar;
    }

    @media (min-width: 992px) {
        .app-sidebar {
            position: relative;
            width: 280px;
            min-width: 75px;
            max-width: 500px;
            transition: width 0.05s ease;
            overflow-x: hidden;
            z-index: 100 !important;
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

        /* Nút thu gọn dạng lơ lửng trên viền (Jira-style) */
        .sidebar-collapse {
            position: absolute !important;
            top: 36px !important;
            right: -12px !important;
            width: 24px !important;
            height: 24px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 50% !important;
            color: #475569 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            z-index: 12 !important;
            transition: all 0.2s ease-in-out !important;
        }

        .sidebar-collapse:hover {
            background: #4f46e5 !important;
            color: #ffffff !important;
            border-color: #4f46e5 !important;
            transform: scale(1.1) !important;
        }
    }

    /* --- TRẠNG THÁI THU NHỎ QUA CONTAINER QUERY (ĐỐI PHÓ VỚI MỌI ĐỘ RỘNG HẸP) --- */
    @container sidebar (max-width: 220px) {
        .app-sidebar {
            padding: 24px 13px !important;
            gap: 1.25rem !important;
        }

        /* Căn giữa logo */
        .app-sidebar .sidebar-brand {
            justify-content: center !important;
            padding: 0 !important;
        }

        /* Thu nhỏ logo khi collapsed để tránh bị rìa cắt */
        .app-sidebar .sidebar-logo {
            width: 38px !important;
            height: 38px !important;
            font-size: 0.95rem !important;
            border-radius: 10px !important;
        }

        /* Ẩn toàn bộ các phần chữ, nhãn tiêu đề khi thu nhỏ */
        .app-sidebar h1,
        .app-sidebar .brand-text,
        .app-sidebar .sidebar-section-label,
        .app-sidebar .sidebar-link span,
        .app-sidebar .sidebar-link .badge,
        .app-sidebar .sidebar-project-toggle span,
        .app-sidebar .sidebar-project-toggle .bi-chevron-down,
        .app-sidebar .user-info,
        .app-sidebar .user-menu-btn {
            display: none !important;
        }

        /* Biến nút Tạo Issue thành hình tròn */
        .app-sidebar .app-btn-create-issue {
            width: 44px !important;
            height: 44px !important;
            border-radius: 50% !important;
            margin: 0.5rem auto !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0 !important;
        }

        .app-sidebar .app-btn-create-issue i {
            font-size: 1.5rem !important;
            margin: 0 !important;
        }

        /* Căn giữa và thu gọn các link menu thành hình tròn icon */
        .app-sidebar .sidebar-link {
            justify-content: center !important;
            padding: 0 !important;
            border-radius: 50% !important;
            width: 44px !important;
            height: 44px !important;
            margin: 0 auto !important;
            gap: 0 !important;
        }

        .app-sidebar .sidebar-link i {
            font-size: 1.25rem !important;
            margin: 0 !important;
        }

        /* Phần thông tin user khi thu nhỏ */
        .app-sidebar .sidebar-user {
            justify-content: center !important;
            padding: 10px 0 !important;
        }

        /* Định vị nút lơ lửng ở vị trí giữa theo chiều dọc khi thu gọn */
        .app-sidebar .sidebar-collapse {
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        /* Xoay ngược mũi tên khi đóng */
        .app-sidebar .sidebar-collapse i {
            transform: rotate(180deg) !important;
            display: inline-block !important;
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

    /* Ngăn chặn các phần tử quan trọng bị co cụm hoặc méo mó hình dạng */
    .sidebar-logo,
    .app-btn-create-issue,
    .sidebar-link,
    .sidebar-project-toggle,
    .sidebar-user img,
    .user-avatar {
        flex-shrink: 0 !important;
    }

    /* Ẩn hoàn toàn nút mũi tên thu gọn theo yêu cầu người dùng */
    .sidebar-collapse {
        display: none !important;
    }

    .app-sidebar i { min-width: 20px; }
</style>

<aside class="app-sidebar">
    <div class="sidebar-resizer"></div>

    <div class="sidebar-brand">
        <!-- Logo của Admin:chữ A (Admin) -->
        <div class="sidebar-logo" style="background: rgba(241, 101, 101, 0.15); color: var(--danger);">A</div>
        <div class="brand-text">
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
                    if (newWidth < 180) {
                        sidebar.classList.add("collapsed");
                        sidebar.style.width = "75px";
                    } else {
                        sidebar.classList.remove("collapsed");
                        sidebar.style.width = newWidth + "px";
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