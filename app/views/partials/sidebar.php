<style>
    /* --- TRẠNG THÁI MẶC ĐỊNH (MỞ RỘNG) --- */
    .app-sidebar {
        position: relative; /* Bắt buộc để định vị thanh kéo */
        width: 280px; /* Chiều rộng mặc định */
        min-width: 75px; /* Giới hạn nhỏ nhất khi kéo */
        max-width: 500px; /* Giới hạn lớn nhất khi kéo */
        transition: width 0.05s ease; /* Giảm thời gian để khi kéo chuột mượt hơn, không bị trễ */
        overflow-x: hidden;
    }

    /* Thanh handle ẩn ở mép phải để rê chuột vào kéo */
    .sidebar-resizer {
        position: absolute;
        top: 0;
        right: 0;
        width: 8px;
        height: 100%;
        cursor: col-resize; /* Đổi icon chuột thành dạng kéo cột */
        background: transparent;
        z-index: 10;
        transition: background 0.2s;
    }
    /* Sáng nhẹ lên khi người dùng rê chuột hoặc đang kéo */
    .sidebar-resizer:hover, .sidebar-resizer.active {
        background: rgba(255, 255, 255, 0.15);
    }

    /* SỬA LỖI TRÀN CHỮ: Cấu hình ép chữ hiện dấu ... khi sidebar quá hẹp */
    .sidebar-link, 
    .sidebar-project-toggle,
    .sidebar-brand,
    .sidebar-user {
        display: flex;
        align-items: center;
        white-space: nowrap;
        width: 100%;
        min-width: 0; /* Thuộc tính quan trọng để kích hoạt text-overflow trong Flexbox */
    }

    /* Định dạng chung cho các thẻ chứa chữ */
    .sidebar-link span, 
    .sidebar-project-toggle span,
    .user-info .user-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis; /* Tự động biến phần chữ thừa thành dấu "..." */
        white-space: nowrap;
        text-align: left;
    }

    /* Giữ khoảng cách cố định cho icon để không bị bóp méo khi thu nhỏ */
    .app-sidebar i {
        min-width: 20px;
    }

    /* --- TRẠNG THÁI KHI THU GỌN (.collapsed) --- */
    .app-sidebar.collapsed {
        width: 75px !important; /* Fix cứng kích thước khi đóng hẳn */
    }

    /* Ẩn toàn bộ các phần chữ, nhãn tiêu đề khi thu nhỏ */
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

    /* Xử lý nút "Tạo Issue mới" khi thu nhỏ */
    .app-sidebar.collapsed .app-btn-create-issue {
        font-size: 0 !important;
        padding: 10px 0;
        justify-content: center;
    }
    .app-sidebar.collapsed .app-btn-create-issue i {
        font-size: 1.5rem !important;
        margin: 0;
    }

    /* Xoay ngược mũi tên khi đóng */
    .app-sidebar.collapsed .sidebar-collapse i {
        transform: rotate(180deg);
        display: inline-block;
    }
</style>

<aside class="app-sidebar">
    <div class="sidebar-resizer"></div>

    <div class="sidebar-brand">
        <div class="sidebar-logo">M</div>
        <div>
            <h1>Workspace</h1>
        </div>
        <button class="sidebar-collapse" aria-label="Thu gọn sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <a href="/TaskSync/task/create" class="app-btn app-btn-create-issue">
        <i class="bi bi-plus"></i> Tạo Issue mới
    </a>

    <div class="sidebar-section">
        <div class="sidebar-section-label">CÁ NHÂN</div>
        <nav class="sidebar-nav">
            <a href="/TaskSync/workspace" class="sidebar-link active">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard tổng hợp</span>
            </a>
            <a href="/TaskSync/task/myTasks" class="sidebar-link">
                <i class="bi bi-check-circle"></i>
                <span>Task của tôi</span>
                <span class="badge">2</span>
            </a>
            <a href="/TaskSync/project/myProjects" class="sidebar-link">
                <i class="bi bi-folder"></i>
                <span>Dự án của tôi</span>
                <span class="badge">3</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">DỰ ÁN HIỆN HÀNH</div>
        
        <div class="sidebar-project">
            <button class="sidebar-project-toggle" type="button" aria-expanded="true">
                <i class="bi bi-chevron-down"></i>
                <span>WEB - Frontend Reconstruction</span>
            </button>
            <nav class="sidebar-nav sidebar-project-nav">
                <a href="/TaskSync/project/kanban/web" class="sidebar-link">
                    <i class="bi bi-kanban-fill"></i>
                    <span>Bảng Kanban</span>
                </a>
                <a href="/TaskSync/project/list/web" class="sidebar-link">
                    <i class="bi bi-list-ul"></i>
                    <span>Danh sách</span>
                </a>
                <a href="/TaskSync/project/members/web" class="sidebar-link">
                    <i class="bi bi-people-fill"></i>
                    <span>Thành viên Dự án</span>
                </a>
                <a href="/TaskSync/project/settings/web" class="sidebar-link">
                    <i class="bi bi-gear-fill"></i>
                    <span>Cấu hình dự án</span>
                </a>
            </nav>
        </div>
    </div>

    <div class="sidebar-user">
        <img src="https://ui-avatars.com/api/?name=QG&background=7c3aed&color=fff" alt="Avatar" class="user-avatar">
        <div class="user-info">
            <div class="user-name">Quyen Gia</div>
            <div class="user-role">ADMIN</div>
        </div>
        <button class="user-menu-btn" type="button" aria-label="Menu người dùng">
            <i class="bi bi-arrow-repeat"></i>
        </button>
    </div>
</aside>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.querySelector(".sidebar-collapse");
    const sidebar = document.querySelector(".app-sidebar");
    const resizer = document.querySelector(".sidebar-resizer");

    // 1. CHỨC NĂNG CLICK NÚT MŨI TÊN ĐỂ THU PHÓNG NHANH
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function(e) {
            e.preventDefault();
            sidebar.classList.toggle("collapsed");
            // Reset lại inline-width để CSS class tự quyết định kích thước cố định
            sidebar.style.width = ''; 
        });
    }

    // 2. CHỨC NĂNG DÙNG CHUỘT NẮM KÉO MÉP ĐỂ TÙY CHỈNH ĐỘ RỘNG
    if (resizer && sidebar) {
        resizer.addEventListener("mousedown", function(e) {
            e.preventDefault();
            resizer.classList.add("active");
            
            document.addEventListener("mousemove", resize);
            document.addEventListener("mouseup", stopResize);
        });

        function resize(e) {
            // Tính toán độ rộng mới dựa trên vị trí con trỏ chuột X
            let newWidth = e.clientX; 
            
            // Giới hạn khoảng kéo từ 75px đến 500px
            if (newWidth >= 75 && newWidth <= 500) {
                sidebar.style.width = newWidth + "px";
                
                // Nếu người dùng kéo kích thước quá nhỏ (< 140px), tự động chuyển sang layout thu gọn
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
