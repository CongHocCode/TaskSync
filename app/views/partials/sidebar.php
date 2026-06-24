<?php
// Khởi tạo Model để tính toán dự án kích hoạt
$projectModel = new ProjectModel();
$userId = $_SESSION['user']['id'] ?? null;

// Lấy danh sách dự án sắp xếp theo số task và ngày tạo
$userProjects = $userId ? $projectModel->getProjectsOrderedForSidebar($userId) : [];

$activeProject = null;
$otherProjects = [];

// Xác định dự án đang được mở
if (isset($data['project']) && !empty($data['project'])) {
    // Trường hợp 1: Nếu đang đứng ở trang dự án cụ thể, chọn chính nó làm active
    $activeProject = $data['project'];
    // Lọc các dự án còn lại để đưa vào dropdown
    $otherProjects = array_filter($userProjects, function ($p) use ($activeProject) {
        return $p['id'] != $activeProject['id'];
    });
} else {
    // Trường hợp 2: Nếu đang đứng ở trang ngoài (Dashboard cá nhân, Profile)
    if (!empty($userProjects)) {
        // Chọn dự án đầu tiên trong danh sách đã sắp xếp thông minh làm mặc định
        $activeProject = $userProjects[0];
        // Các dự án còn lại đưa vào dropdown
        $otherProjects = array_slice($userProjects, 1);
    }
}

// Xử lý khi user không có dự án
if ($activeProject) {
    // Nếu có dự án, các liên kết trỏ về dự án đó
    $kanbanUrl = BASE_URL . "/project/kanban/" . $activeProject['id'];
    $listUrl   = BASE_URL . "/project/list/" . $activeProject['id'];
    $membersUrl = BASE_URL . "/project/members/" . $activeProject['id'];
    $settingsUrl = BASE_URL . "/project/settings/" . $activeProject['id'];
    $projectNameDisplay = htmlspecialchars($activeProject['key'] . ' - ' . $activeProject['name']);
} else {
    // Nếu chưa có bất kỳ dự án nào, tất cả liên kết dẫn đến trang tạo mới dự án
    $kanbanUrl = $listUrl = $membersUrl = $settingsUrl = BASE_URL . "/project/create";
    $projectNameDisplay = "Chưa có dự án";
}
?>
<style>
    @media (min-width: 992px) {
        /* --- TRẠNG THÁI MẶC ĐỊNH (MỞ RỘNG) --- */
        .app-sidebar {
            position: relative;
            /* Bắt buộc để định vị thanh kéo */
            width: 380px;
            /* Chiều rộng mặc định */
            min-width: 75px;
            /* Giới hạn nhỏ nhất khi kéo */
            max-width: 500px;
            /* Giới hạn lớn nhất khi kéo */
            transition: width 0.05s ease;
            /* Giảm thời gian để khi kéo chuột mượt hơn, không bị trễ */
            overflow-x: hidden;
        }

        /* Thanh handle ẩn ở mép phải để rê chuột vào kéo */
        .sidebar-resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 100%;
            cursor: col-resize;
            /* Đổi icon chuột thành dạng kéo cột */
            background: transparent;
            z-index: 10;
            transition: background 0.2s;
        }

        /* Sáng nhẹ lên khi người dùng rê chuột hoặc đang kéo */
        .sidebar-resizer:hover,
        .sidebar-resizer.active {
            background: rgba(255, 255, 255, 0.15);
        }

        /* --- TRẠNG THÁI KHI THU GỌN (.collapsed) --- */
        .app-sidebar.collapsed {
            width: 75px !important;
            /* Fix cứng kích thước khi đóng hẳn */
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
        min-width: 0;
        /* Thuộc tính quan trọng để kích hoạt text-overflow trong Flexbox */
    }

    /* Định dạng chung cho các thẻ chứa chữ */
    .sidebar-link span,
    .sidebar-project-toggle span,
    .user-info .user-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        /* Tự động biến phần chữ thừa thành dấu "..." */
        white-space: nowrap;
        text-align: left;
    }

    /* Giữ khoảng cách cố định cho icon để không bị bóp méo khi thu nhỏ */
    .app-sidebar i {
        min-width: 20px;
    }
</style>

<aside class="app-sidebar">
    <div class="sidebar-resizer"></div>

    <div class="sidebar-brand">
        <div class="sidebar-logo">M</div>
        <div>
            <h1>Workspace</h1>
            <p style="font-size: 0.65rem; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted);">TaskSync</p>
        </div>
        <button class="sidebar-collapse" aria-label="Thu gọn sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <!-- Nút tạo issue mới -->
    <?php
    // Nếu có dự án hiện hành, mở modal. Nếu chưa có, đẩy về trang tạo dự án
    if ($activeProject) {
        $issueTriggerAttr = 'data-bs-toggle="modal" data-bs-target="#createIssueModal" href="#"';
    } else {
        $issueTriggerAttr = 'href="' . BASE_URL . '/project/create"';
    }
    ?>

    <a <?= $issueTriggerAttr ?> class="app-btn app-btn-create-issue d-flex align-items-center justify-content-center gap-1 text-decoration-none">
        <i class="bi bi-plus-lg"></i> Tạo Issue mới
    </a>

    <div class="sidebar-section">
        <div class="sidebar-section-label">CÁ NHÂN</div>
        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>/workspace" class="sidebar-link active">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard tổng hợp</span>
            </a>
            <a href="<?= BASE_URL ?>/task/myTasks" class="sidebar-link">
                <i class="bi bi-check-circle"></i>
                <span>Task của tôi</span>
                <span class="badge">2</span>
            </a>
            <a href="<?= BASE_URL ?>/project/myProjects" class="sidebar-link">
                <i class="bi bi-folder"></i>
                <span>Dự án của tôi</span>
                <span class="badge">3</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">DỰ ÁN HIỆN HÀNH</div>

        <div class="sidebar-project dropdown">
            <!-- Nút hiển thị Dự án đang kích hoạt / Hoặc trạng thái rỗng -->
            <button class="sidebar-project-toggle w-100 text-start d-flex align-items-center justify-content-between border-0 dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                style="background: transparent;">
                <span><?= $projectNameDisplay ?></span>
            </button>

            <!-- Menu danh sách chọn dự án khác -->
            <ul class="dropdown-menu dropdown-menu-dark border-0 shadow w-100" style="background-color: #141933;">
                <?php if (empty($userProjects)): ?>
                    <!-- Trạng thái trống hoàn toàn -->
                    <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>/project/create"><i class="bi bi-plus-circle me-2 text-success"></i>Tạo dự án đầu tiên</a></li>
                <?php else: ?>
                    <?php if (empty($otherProjects)): ?>
                        <li><a class="dropdown-item disabled small" href="#">Không có dự án khác</a></li>
                    <?php else: ?>
                        <?php foreach ($otherProjects as $p): ?>
                            <li>
                                <a class="dropdown-item py-2" href="<?= BASE_URL ?>/project/kanban/<?= $p['id'] ?>">
                                    <i class="bi bi-folder-fill text-primary me-2"></i>
                                    <?= htmlspecialchars($p['key'] . ' - ' . $p['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <!-- DANH SÁCH MENU CON - LUÔN GIỮ KHUNG GIAO DIỆN -->
            <nav class="sidebar-nav sidebar-project-nav mt-2">
                <a href="<?= $kanbanUrl ?>" class="sidebar-link">
                    <i class="bi bi-kanban-fill"></i>
                    <span>Bảng Kanban</span>
                </a>
                <a href="<?= $listUrl ?>" class="sidebar-link">
                    <i class="bi bi-list-ul"></i>
                    <span>Danh sách</span>
                </a>
                <a href="<?= $membersUrl ?>" class="sidebar-link">
                    <i class="bi bi-people-fill"></i>
                    <span>Thành viên Dự án</span>
                </a>
                <a href="<?= $settingsUrl ?>" class="sidebar-link">
                    <i class="bi bi-gear-fill"></i>
                    <span>Cấu hình dự án</span>
                </a>
            </nav>
        </div>
    </div>

    <div class="sidebar-user">
        <!-- Avatar lấy động từ Session -->
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['display_name'] ?? 'User') ?>&background=7c3aed&color=fff" alt="Avatar" class="user-avatar">
        <div class="user-info">
            <!-- Tên User thực tế đang đăng nhập -->
            <div class="user-name"><?= htmlspecialchars($_SESSION['user']['display_name'] ?? 'User') ?></div>
            <div class="user-role"><?= strtoupper(htmlspecialchars($_SESSION['user']['role'] ?? 'MEMBER')) ?></div>
        </div>

        <a href="<?= BASE_URL ?>/auth/logout" class="user-menu-btn text-decoration-none d-flex align-items-center justify-content-center" title="Đăng xuất" style="color: #ff4d4f !important;">
            <i class="bi bi-box-arrow-right" style="font-size: 1.2rem;"></i>
        </a>
    </div>
</aside>

<!-- MODAL TẠO ISSUE MỚI -->
<?php if ($activeProject):
    // Lấy danh sách thành viên thực tế của dự án hiện tại để làm danh sách Assignee
    $projectMembers = $projectModel->getProjectMembers($activeProject['id']);
?>
    <div class="modal fade" id="createIssueModal" tabindex="-1" aria-labelledby="createIssueModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content text-dark" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <!-- Modal Header -->
                <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="createIssueModalLabel">
                        <i class="bi bi-plus-circle-fill text-primary"></i> Tạo Issue mới cho dự án: <span class="text-primary"><?= htmlspecialchars($activeProject['name']) ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Form -->
                <form action="<?= BASE_URL ?>/task/create" method="POST">
                    <!-- ID Dự án ẩn (Tự động điền) -->
                    <input type="hidden" name="project_id" value="<?= $activeProject['id'] ?>">

                    <div class="modal-body py-3 px-4">
                        <div class="row g-3">
                            <!-- Loại hình công việc & Độ ưu tiên -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Loại hình công việc</label>
                                <select class="form-select border-secondary-subtle" name="type" required>
                                    <option value="task" selected>Task (Công việc thường)</option>
                                    <option value="bug">Bug (Sửa lỗi)</option>
                                    <option value="story">Story (Nghiệp vụ)</option>
                                    <option value="epic">Epic (Tính năng lớn)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Độ ưu tiên</label>
                                <select class="form-select border-secondary-subtle" name="priority" required>
                                    <option value="HIGHEST">Highest</option>
                                    <option value="HIGH">High</option>
                                    <option value="MEDIUM" selected>Medium</option>
                                    <option value="LOW">Low</option>
                                </select>
                            </div>

                            <!-- Tiêu đề công việc -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Tiêu đề Issue <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-secondary-subtle" name="title" placeholder="Nhập tên ngắn gọn cho công việc..." required>
                            </div>

                            <!-- Người thực hiện (Assignee) -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Người được phân công (Assignee)</label>
                                <select class="form-select border-secondary-subtle" name="assignee_id">
                                    <option value="" selected>Chưa phân công (Unassigned)</option>
                                    <?php foreach ($projectMembers as $member):
                                        $fullName = trim($member['first_name'] . ' ' . $member['last_name']);
                                        $displayName = !empty($fullName) ? $fullName : $member['username'];
                                    ?>
                                        <option value="<?= $member['id'] ?>">
                                            <?= htmlspecialchars($displayName) ?> (<?= htmlspecialchars($member['role']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Mô tả chi tiết -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Mô tả chi tiết công việc</label>
                                <textarea class="form-control border-secondary-subtle" name="description" rows="4" placeholder="Nhập mô tả các bước thực hiện hoặc yêu cầu công việc..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light border-top-0 py-3 px-4">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-check-lg me-1"></i> Tạo công việc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.querySelector(".sidebar-collapse");
        const sidebar = document.querySelector(".app-sidebar");
        const resizer = document.querySelector(".sidebar-resizer");

        // 1. CHỨC NĂNG CLICK NÚT MŨI TÊN ĐỂ THU PHÓNG NHANH
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener("click", function(e) {
                if (window.innerWidth > 992) {
                    e.preventDefault();
                    sidebar.classList.toggle("collapsed");
                    sidebar.style.width = '';
                }
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