<?php
// Kiểm tra quyền quản trị/PM từ dữ liệu truyền xuống của Controller [179]
$canManage = $data['is_authorized_to_delete'] ?? false;
?>

<div class="container-fluid py-4">
    <!-- Tiêu đề trang -->
    <div class="mb-4">
        <h1 class="h3 mb-2 fw-bold text-dark">
            <i class="bi bi-people-fill text-primary me-2"></i>Thành viên & Điều phối dự án: <span class="text-primary"><?= htmlspecialchars($data['project']['name']) ?></span>
        </h1>
        <p class="text-muted" style="font-size: 0.95rem;">Quản lý nhân sự, phân quyền vai trò và theo dõi hiệu suất làm việc của dự án.</p>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: DANH SÁCH THÀNH VIÊN HIỆN TẠI (Tự co giãn động 8 hoặc 12 cột) -->
        <div class="col-12 <?= $canManage ? 'col-xl-8' : 'col-xl-12' ?>">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                <h5 class="fw-bold mb-3 text-dark">Danh sách thành viên hiện tại (<?= count($data['members']) ?>)</h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr style="font-size: 0.85rem;" class="text-secondary text-uppercase fw-bold">
                                <th>Họ và Tên</th>
                                <th>Email</th>
                                <th>Vai trò dự án</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($data['members'] ?? []) as $member):
                                $fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
                                $displayName = !empty($fullName) ? $fullName : $member['username'];
                                $isPending = ($member['status'] === 'pending'); // Kiểm tra trạng thái chờ

                                $avatarColors = ['06b6d4', 'f59e0b', '8b5cf6', '10b981', 'ec4899', '3b82f6'];
                                $avatarBg = $member['id'] ? $avatarColors[$member['id'] % count($avatarColors)] : '64748b';
                                $avatarUrl = !empty($member['avatar_url']) && $member['avatar_url'] !== 'default-avatar.png'
                                    ? BASE_URL . '/uploads/avatars/' . $member['avatar_url']
                                    : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=" . $avatarBg . "&color=fff";
                            ?>
                                <tr class="<?= $isPending ? 'opacity-75' : '' ?>">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-sm" width="40" height="40" style="<?= $isPending ? 'filter: grayscale(1);' : '' ?>">
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($displayName) ?></div>
                                                <small class="text-muted">@<?= htmlspecialchars($member['username']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($member['email'] ?? 'Chưa cập nhật') ?></td>
                                    <td>
                                        <!-- Badge Vai trò -->
                                        <span class="badge rounded px-3 py-1.5 <?= $member['role'] === 'manager' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' ?> text-capitalize small">
                                            <?= htmlspecialchars($member['role']) ?>
                                        </span>

                                        <!-- Badge Trạng thái Lời mời -->
                                        <?php if ($isPending): ?>
                                            <span class="badge bg-warning-subtle text-warning rounded px-2.5 py-1.5 small ms-1">
                                                <i class="bi bi-clock-history me-1"></i>Pending
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success rounded px-2.5 py-1.5 small ms-1">
                                                <i class="bi bi-check-circle me-1"></i>Active
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- Xem hồ sơ nhân sự -->
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="viewStaffDetails(<?= $member['id'] ?>, '<?= htmlspecialchars($displayName) ?>')">
                                                <i class="bi bi-eye-fill me-1"></i> Xem hồ sơ
                                            </button>

                                            <!-- CHỈ hiển thị nút Bổ nhiệm/Bãi nhiệm nếu thành viên đã ACTIVE -->
                                            <?php if (!$isPending && ($data['is_authorized_to_delete'] ?? false) && $data['project']['owner_id'] != $member['id'] && $_SESSION['user']['id'] != $member['id']): ?>
                                                <form action="<?= BASE_URL ?>/project/changeRole" method="POST" class="d-inline">
                                                    <input type="hidden" name="project_id" value="<?= $data['project']['id'] ?>">
                                                    <input type="hidden" name="user_id" value="<?= $member['id'] ?>">

                                                    <?php if ($member['role'] === 'manager'): ?>
                                                        <input type="hidden" name="role" value="member">
                                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn bãi nhiệm chức vụ Quản lý của thành viên này?');">
                                                            <i class="bi bi-person-down"></i> Bãi nhiệm
                                                        </button>
                                                    <?php else: ?>
                                                        <input type="hidden" name="role" value="manager">
                                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn bổ nhiệm thành viên này làm Quản lý dự án?');">
                                                            <i class="bi bi-person-up"></i> Bổ nhiệm
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            <?php endif; ?>

                                            <!-- Xóa thành viên / Hủy lời mời -->
                                            <?php if (($data['is_authorized_to_delete'] ?? false) && $data['project']['owner_id'] != $member['id'] && $_SESSION['user']['id'] != $member['id'] && $member['role'] !== 'manager'): ?>
                                                <a href="<?= BASE_URL ?>/project/removeMember/<?= $data['project']['id'] ?>/<?= $member['id'] ?>"
                                                    class="btn btn-sm <?= $isPending ? 'btn-outline-secondary' : 'btn-outline-danger' ?> rounded-pill px-3"
                                                    onclick="return confirm('<?= $isPending ? 'Bạn có chắc chắn muốn hủy bỏ lời mời tham gia dự án này?' : 'Bạn có chắc chắn muốn mời thành viên này ra khỏi dự án?' ?>');">
                                                    <i class="<?= $isPending ? 'bi bi-x-circle' : 'bi bi-person-x-fill' ?>"></i>
                                                    <?= $isPending ? 'Hủy lời mời' : 'Xóa' ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 🚀 CỘT PHẢI (35%): CHỈ HIỂN THỊ CHO TÀI KHOẢN CÓ QUYỀN HẠN PHÙ HỢP (MANAGER/ADMIN) -->
        <?php if ($canManage): ?>
            <div class="col-12 col-xl-4">
                <div class="d-flex flex-column gap-4">

                    <!-- KHỐI 1: NÚT KÍCH HOẠT MODAL THÊM THÀNH VIÊN ĐỘNG -->
                    <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-plus-fill text-success me-2"></i>Mời thành viên</h5>
                        <p class="text-muted small">Mời thêm nhân sự mới tham gia thực thi công việc của dự án này.</p>

                        <!-- Nút mở Modal tìm kiếm và chọn nhân sự chuyên nghiệp -->
                        <button class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 rounded-pill"
                            data-bs-toggle="modal"
                            data-bs-target="#addMemberModal">
                            <i class="bi bi-person-plus-fill fs-5"></i> Thêm thành viên mới
                        </button>
                    </div>

                    <!-- KHỐI 2: SỨC KHỎE DỰ ÁN -->
                    <?php
                    $total = $data['stats']['total_tasks'] ?? 0;
                    $completed = $data['stats']['completed_tasks'] ?? 0;
                    $percent = $total ? round(($completed / $total) * 100) : 0;
                    ?>
                    <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-activity text-danger me-2"></i>Sức khỏe dự án</h5>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary small fw-medium">Tiến độ hoàn thành:</span>
                            <span class="text-dark fw-bold"><?= $percent ?>%</span>
                        </div>
                        <div class="progress mb-3" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%; border-radius: 10px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="row g-2 text-center mt-2">
                            <div class="col-6 py-2 bg-light rounded shadow-xs border-bottom border-3 border-primary">
                                <small class="text-secondary d-block">Tổng Task</small>
                                <span class="h4 fw-bold text-dark"><?= $total ?></span>
                            </div>
                            <div class="col-6 py-2 bg-light rounded shadow-xs border-bottom border-3 border-success">
                                <small class="text-secondary d-block">Đã xong</small>
                                <span class="h4 fw-bold text-dark"><?= $completed ?></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==============================================================
     MODAL 1: THÊM THÀNH VIÊN ĐỘNG BẰNG TÌM KIẾM AJAX
     ============================================================== -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill text-success me-2"></i>Tìm kiếm & Thêm thành viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= BASE_URL ?>/project/addMember" method="POST">
                <input type="hidden" name="project_id" value="<?= $data['project']['id'] ?>">
                <!-- ID người dùng được chọn (Gán ngầm bằng JavaScript) -->
                <input type="hidden" name="user_id" id="selectedUserId" required>

                <div class="modal-body py-2 px-4 text-dark">
                    <!-- Ô gõ tìm kiếm -->
                    <div class="mb-3 position-relative">
                        <label class="form-label small fw-bold text-secondary">Tìm kiếm theo Tên đăng nhập hoặc Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-secondary-subtle text-dark" id="memberSearchInput" autocomplete="off" placeholder="Gõ từ 2 ký tự để tìm kiếm nhân sự..." required>
                        </div>
                    </div>

                    <!-- Khu vực danh sách kết quả tìm kiếm đổ động bằng AJAX -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Kết quả tìm kiếm:</label>
                        <div class="list-group overflow-y-auto" id="searchResultList" style="max-height: 200px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 5px; background-color: #f8fafc;">
                            <div class="text-muted text-center py-3 small">Vui lòng nhập thông tin để tìm nhân sự ngoài dự án...</div>
                        </div>
                    </div>

                    <!-- Chọn vai trò cho thành viên mới -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Vai trò dự án</label>
                        <select class="form-select border-secondary-subtle" name="role" required>
                            <option value="member" selected>Member (Thành viên)</option>
                            <option value="manager">Manager (Quản lý)</option>
                            <option value="viewer">Viewer (Người xem)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-top-0 py-3 px-4 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Hủy</button>
                    <!-- Khóa nút submit cho đến khi JS đã gán ID người dùng chọn hợp lệ -->
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" id="submitAddMemberBtn" disabled>
                        <i class="bi bi-check-lg me-1"></i>Xác nhận thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL 2: CHI TIẾT NHÂN SỰ ĐỘNG
     ============================================================== -->
<div class="modal fade" id="staffDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-badge-fill text-primary me-2"></i>Hồ sơ năng lực thành viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3 px-4 text-dark">
                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-1 text-dark" id="modalStaffName">N/A</h4>
                    <span class="text-muted small">Các dự án tham gia & Hiệu suất xử lý Task của nhân viên</span>
                </div>

                <!-- Danh sách các Dự án tham gia -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-2 text-secondary"><i class="bi bi-folder-check me-2 text-primary"></i>Các dự án đang tham gia:</h6>
                    <ul class="list-group" id="modalStaffProjects" style="max-height: 150px; overflow-y: auto;">
                        <!-- JS nạp danh sách dự án bằng Fetch API -->
                    </ul>
                </div>

                <!-- Thống kê trạng thái Task được giao -->
                <div>
                    <h6 class="fw-bold mb-2 text-secondary"><i class="bi bi-check2-all me-2 text-success"></i>Tiến độ công việc trong dự án này:</h6>
                    <div class="row g-2 text-center" id="modalStaffTasks">
                        <!-- JS nạp số liệu đếm bằng Fetch API -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Khai báo biến đường dẫn gốc của PHP cho JS sử dụng
    const baseUrl = "<?= BASE_URL ?>";

    //TÍCH HỢP TÌM KIẾM THÀNH VIÊN ĐỘNG BẰNG JS DEBOUNCE (MẤT 30 GIÂY ĐỂ HOẠT ĐỘNG)
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('memberSearchInput');
        const resultList = document.getElementById('searchResultList');
        const selectedUserIdInput = document.getElementById('selectedUserId');
        const submitBtn = document.getElementById('submitAddMemberBtn');

        let searchTimeout = null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = searchInput.value.trim();

                // Nếu ô nhập ít hơn 2 ký tự, reset danh sách kết quả về rỗng
                if (query.length < 2) {
                    resultList.innerHTML = '<div class="text-muted text-center py-3 small">Vui lòng gõ từ 2 ký tự trở lên để quét CSDL...</div>';
                    if (submitBtn) submitBtn.setAttribute('disabled', 'true');
                    return;
                }

                // Kỹ thuật Debounce (chống dội) 300 mili-giây: Chỉ gọi máy chủ sau khi người dùng ngừng gõ phím
                searchTimeout = setTimeout(() => {
                    resultList.innerHTML = '<div class="text-muted text-center py-3 small"><div class="spinner-border spinner-border-sm text-secondary me-2"></div>Đang tìm kiếm...</div>';

                    // Gửi Fetch API tìm kiếm động các nhân sự CHƯA có trong dự án
                    fetch(`${baseUrl}/project/searchNonMembers/<?= $data['project']['id'] ?>?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(users => {
                            if (users.length === 0) {
                                resultList.innerHTML = '<div class="text-danger text-center py-3 small"><i class="bi bi-x-circle me-1"></i>Không tìm thấy nhân sự phù hợp ngoài dự án!</div>';
                                if (submitBtn) submitBtn.setAttribute('disabled', 'true');
                                return;
                            }

                            let html = '';
                            users.forEach(user => {
                                const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim();
                                const displayName = fullName || user.username;

                                // Tạo thẻ button cho phép bấm chọn nhanh
                                html += `
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2.5" onclick="selectUserToInvite(${user.id}, '${displayName}')">
                                    <div>
                                        <div class="fw-bold text-dark text-start" style="font-size: 0.9rem;">${displayName}</div>
                                        <small class="text-muted">@${user.username} - ${user.email}</small>
                                    </div>
                                    <i class="bi bi-plus-circle-fill text-success fs-5"></i>
                                </button>
                            `;
                            });
                            resultList.innerHTML = html;
                        })
                        .catch(err => {
                            console.error('Lỗi tìm kiếm API:', err);
                            resultList.innerHTML = '<div class="text-danger text-center py-3 small">Lỗi kết nối máy chủ</div>';
                        });
                }, 300);
            });
        }

        // Hàm toàn cục khi click chọn 1 user từ kết quả tìm kiếm
        window.selectUserToInvite = function(userId, displayName) {
            selectedUserIdInput.value = userId;
            searchInput.value = displayName; // Đưa tên hiển thị lên ô tìm kiếm

            // Hiển thị thông báo đã chọn thành công để người dùng dễ nhận biết
            resultList.innerHTML = `
            <div class="alert alert-success py-2.5 mb-0 small text-center" style="border: none;">
                <i class="bi bi-check-circle-fill me-2 fs-6"></i>Đã chọn thành công: <strong>${displayName}</strong>
            </div>`;

            // Kích hoạt mở khóa nút Xác nhận thêm của Form
            if (submitBtn) submitBtn.removeAttribute('disabled');
        };
    });

    // Hàm Fetch API lấy chi tiết năng lực nhân sự động
    function viewStaffDetails(userId, displayName) {
        const modal = new bootstrap.Modal(document.getElementById('staffDetailModal'));
        document.getElementById('modalStaffName').textContent = displayName;

        const projectsList = document.getElementById('modalStaffProjects');
        const tasksContainer = document.getElementById('modalStaffTasks');

        // Hiện trạng thái đang tải...
        projectsList.innerHTML = '<li class="list-group-item text-muted text-center py-3">Đang tải danh sách...</li>';
        tasksContainer.innerHTML = '<div class="col-12 text-muted text-center py-3">Đang thống kê...</div>';

        modal.show();

        // Gọi API động lấy chi tiết nhân sự có lọc theo dự án hiện tại
        fetch(`${baseUrl}/project/memberStats/${userId}?project_id=<?= $data['project']['id'] ?>`)
            .then(response => response.json())
            .then(data => {
                // 1. Đổ danh sách các dự án tham gia
                let projHtml = '';
                if (data.projects.length === 0) {
                    projHtml = '<li class="list-group-item text-muted text-center py-3">Chưa tham gia dự án nào</li>';
                } else {
                    data.projects.forEach(p => {
                        projHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2.5">
                            <div>
                                <span class="fw-bold text-dark">${p.key}</span> - ${p.name}
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary small text-capitalize">${p.role}</span>
                        </li>`;
                    });
                }
                projectsList.innerHTML = projHtml;

                // 2. Đổ dữ liệu thống kê trạng thái Task được giao trong dự án này
                const statuses = {
                    todo: {
                        label: 'To Do',
                        color: 'border-secondary'
                    },
                    in_progress: {
                        label: 'In Progress',
                        color: 'border-primary'
                    },
                    in_review: {
                        label: 'In Review',
                        color: 'border-warning'
                    },
                    done: {
                        label: 'Done',
                        color: 'border-success'
                    }
                };

                let taskHtml = '';
                const taskMap = {};

                data.tasks.forEach(t => {
                    taskMap[t.status] = parseInt(t.count) || 0;
                });

                for (const [key, info] of Object.entries(statuses)) {
                    const count = taskMap[key] || 0;
                    taskHtml += `
                    <div class="col-6 col-md-3">
                        <div class="p-2 border-bottom border-3 ${info.color} bg-light rounded">
                            <small class="text-secondary d-block" style="font-size: 0.72rem;">${info.label}</small>
                            <span class="fw-bold text-dark h5">${count}</span>
                        </div>
                    </div>`;
                }

                tasksContainer.innerHTML = taskHtml;
            })
            .catch(error => {
                console.error('Lỗi lấy dữ liệu chi tiết nhân sự:', error);
                projectsList.innerHTML = '<li class="list-group-item text-danger text-center">Không thể lấy dữ liệu</li>';
                tasksContainer.innerHTML = '<div class="col-12 text-danger text-center">Lỗi tải dữ liệu</div>';
            });
    }
</script>