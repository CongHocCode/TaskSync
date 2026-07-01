<?php
// Khởi tạo Model để tính toán dự án kích hoạt
require_once __DIR__ . '/../../models/ProjectModel.php';
$projectModel = new ProjectModel();
$userSession = $_SESSION['user'] ?? [];
$userId = $userSession['id'] ?? null;

// Lấy ảnh đại diện và thông tin của user
$displayName = $userSession['display_name'] ?? ($userSession['username'] ?? 'User');
$avatarFile = $userSession['avatar_url'] ?? '';
// Nếu có file ảnh trên máy chủ, lấy ảnh thật. Ngược lại, lấy ảnh chữ tự động làm dự phòng
$sidebarAvatarUrl = (!empty($avatarFile) && $avatarFile !== 'default-avatar.png')
    ? BASE_URL . '/uploads/avatars/' . $avatarFile
    : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=7c3aed&color=fff";

//Lấy toàn bộ project của user (Chỉ chạy đúng 1 lần)
$userProjects = $userId ? $projectModel->getProjectsOrderedForSidebar($userId) : [];

$activeProject = null;
$isManager = false;
$userProjectRole = null;
$isAdmin = ($userSession['role'] ?? '') === 'admin';
$canCreateTask = $isAdmin; // Admin luôn có quyền tạo
$otherProjects = [];

// Xác định dự án đang được hiển thị chính trên sidebar
if (isset($data['project']) && !empty($data['project'])) {
    // Trường hợp 1: Nếu đang đứng ở trang dự án cụ thể, lấy chính nó làm active
    $activeProject = $data['project'];
} elseif (!empty($userProjects)) {
    // Trường hợp 2: Nếu đang đứng ở trang ngoài (Dashboard, Profile), lấy dự án đầu tiên làm mặc định
    $activeProject = $userProjects[0];
}

if ($activeProject) {
    $currentProjId = $activeProject['id'];

    // Gọi CSDL lấy vai trò thực tế của user trong dự án
    $userProjectRole = $projectModel->getProjectUserRole($currentProjId, $userId);
    $isManager = ($userProjectRole === 'manager');
    $canCreateTask = $isAdmin || ($userProjectRole === 'manager' || $userProjectRole === 'member');

    // Lọc các dự án còn lại để đưa vào dropdown chuyển dự án
    $otherProjects = array_filter($userProjects, function ($p) use ($currentProjId) {
        return $p['id'] != $currentProjId;
    });

    // Thiết lập các liên kết động trỏ về dự án đó
    $kanbanUrl = BASE_URL . "/project/kanban/" . $currentProjId;
    $listUrl = BASE_URL . "/project/list/" . $currentProjId;
    $membersUrl = BASE_URL . "/project/members/" . $currentProjId;
    $settingsUrl = BASE_URL . "/project/settings/" . $currentProjId;
    $projectNameDisplay = htmlspecialchars($activeProject['key'] . ' - ' . $activeProject['name']);
} else {
    // Trường hợp đặc biệt: User chưa tham gia bất kỳ dự án nào (Empty State)
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

    #createIssueModal input {
        color: #1e293b !important;
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
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
    $showCreateBtn = !$activeProject || $canCreateTask;
    if ($showCreateBtn):
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
    <?php endif; ?>

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
                <!-- Chỉ hiển thị nút Cấu hình nếu là Manager -->
                <?php if ($isManager || $isAdmin): ?>
                    <a href="<?= $settingsUrl ?>" class="sidebar-link">
                        <i class="bi bi-gear-fill"></i>
                        <span>Cấu hình dự án</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <div class="sidebar-user" style="position: relative;">
        <!-- Avatar lấy động từ Session -->
        <img src="<?= $sidebarAvatarUrl ?>" alt="Avatar" class="user-avatar" style="object-fit: cover;">
        <div class="user-info">
            <!-- Tên User thực tế đang đăng nhập -->
            <div class="user-name"><?= htmlspecialchars($_SESSION['user']['display_name'] ?? 'User') ?></div>
            <div class="user-role"><?= strtoupper(htmlspecialchars($_SESSION['user']['role'] ?? 'MEMBER')) ?></div>
        </div>

        <!-- Lớp phủ bây giờ sẽ bị khóa chặt bên trong khung .sidebar-user nhờ thuộc tính relative ở trên -->
        <a href="<?= BASE_URL ?>/user/profile" class="stretched-link" title="Cài đặt tài khoản"></a>

        <a href="<?= BASE_URL ?>/auth/logout" class="user-menu-btn text-decoration-none d-flex align-items-center justify-content-center" title="Đăng xuất" style="color: #ff4d4f !important; z-index: 2; position: relative;">
            <i class="bi bi-box-arrow-right" style="font-size: 1.2rem;"></i>
        </a>
    </div>
</aside>

<!-- MODAL TẠO ISSUE MỚI -->
<?php 
    $defaultProjectId = $activeProject['id'] ?? '';
    $defaultProjectName = $activeProject['name'] ?? '';
    $defaultMembers = !empty($defaultProjectId) && isset($projectModel) ? $projectModel->getProjectMembers($defaultProjectId) : [];
?>
    <div class="modal fade" id="createIssueModal" tabindex="-1" aria-labelledby="createIssueModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content text-dark" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <!-- Modal Header -->
                <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="createIssueModalLabel">
                        <i class="bi bi-plus-circle-fill text-primary"></i> <span id="createIssueModalTitleText">Tạo Issue mới cho dự án: <span class="text-primary"><?= htmlspecialchars($defaultProjectName) ?></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Form -->
                <form action="<?= BASE_URL ?>/task/create" method="POST" id="createIssueFormObj">
                    <!-- ID Dự án ẩn (Tự động điền) -->
                    <input type="hidden" name="project_id" id="createIssueProjectId" value="<?= $defaultProjectId ?>">
                    
                    <!-- ID Parent Task ẩn (Tự động điền nếu là subtask) -->
                    <input type="hidden" name="parent_issue_id" id="parentIssueIdInput" value="">

                    <div class="modal-body py-3 px-4">
                        <div class="row g-3">
                            <!-- Hiển thị Mother Task (Nếu có) -->
                            <div class="col-12 d-none" id="motherTaskInfo">
                                <label class="form-label small fw-bold text-secondary">Mother Task</label>
                                <div class="alert alert-secondary py-2 mb-0 d-flex align-items-center" id="motherTaskName"></div>
                            </div>

                            <!-- Loại hình công việc & Độ ưu tiên -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Loại hình công việc</label>
                                <select class="form-select border-secondary-subtle" name="type" required>
                                    <option value="task" selected>Task (Công việc thường)</option>
                                    <option value="bug">Bug (Sửa lỗi)</option>
                                    <?php if ($isAdmin || $userProjectRole === 'manager'): ?>
                                        <option value="story">Story (Nghiệp vụ)</option>
                                        <option value="epic">Epic (Tính năng lớn)</option>
                                    <?php endif; ?>
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
                                <select class="form-select border-secondary-subtle" name="assignee_id" id="createIssueAssigneeSelect">
                                    <option value="" selected>Chưa phân công (Unassigned)</option>
                                    <?php foreach ($defaultMembers as $member):
                                        $fullName = trim($member['first_name'] . ' ' . $member['last_name']);
                                        $displayName = !empty($fullName) ? $fullName : $member['username'];
                                    ?>
                                        <option value="<?= $member['id'] ?>">
                                            <?= htmlspecialchars($displayName) ?> (<?= htmlspecialchars($member['role']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Hạn thực hiện (Due Date) -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary" for="due_date_input">
                                    Hạn thực hiện <span class="text-muted fw-normal">(Tùy chọn)</span>
                                </label>
                                <input type="datetime-local" class="form-control border-secondary-subtle" id="due_date_input" name="due_date">
                                <div class="invalid-feedback" id="due_date_error">
                                    Hạn thực hiện phải lớn hơn thời điểm hiện tại.
                                </div>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ===== VALIDATE DUE DATE =====
        const createIssueModal = document.getElementById('createIssueModal');
        if (createIssueModal) {
            const dueDateInput = document.getElementById('due_date_input');
            const createIssueForm = createIssueModal.querySelector('form');

            // Khi mở modal, set giá trị min là thời điểm hiện tại
            createIssueModal.addEventListener('show.bs.modal', function(e) {
                if (dueDateInput) {
                    const now = new Date();
                    // Format: YYYY-MM-DDTHH:MM (datetime-local format)
                    const pad = n => String(n).padStart(2, '0');
                    const minVal = now.getFullYear() + '-' +
                        pad(now.getMonth() + 1) + '-' +
                        pad(now.getDate()) + 'T' +
                        pad(now.getHours()) + ':' +
                        pad(now.getMinutes());
                    dueDateInput.setAttribute('min', minVal);
                    dueDateInput.value = '';
                    dueDateInput.classList.remove('is-invalid');
                }
            });

            // Reset trạng thái subtask khi đóng modal
            createIssueModal.addEventListener('hidden.bs.modal', function() {
                const parentInput = document.getElementById('parentIssueIdInput');
                const motherTaskInfo = document.getElementById('motherTaskInfo');
                if (parentInput) parentInput.value = '';
                if (motherTaskInfo) motherTaskInfo.classList.add('d-none');
            });

            // Validate trước khi submit
            if (createIssueForm) {
                createIssueForm.addEventListener('submit', function(e) {
                    if (dueDateInput && dueDateInput.value) {
                        const selectedDate = new Date(dueDateInput.value);
                        const now = new Date();
                        if (selectedDate <= now) {
                            e.preventDefault();
                            dueDateInput.classList.add('is-invalid');
                            dueDateInput.focus();
                            return false;
                        } else {
                            dueDateInput.classList.remove('is-invalid');
                        }
                    }
                });

                // Xóa lỗi khi user thay đổi giá trị
                if (dueDateInput) {
                    dueDateInput.addEventListener('change', function() {
                        dueDateInput.classList.remove('is-invalid');
                    });
                }
            }
        }

        // ===== SIDEBAR CONTROLS =====
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