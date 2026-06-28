<div class="container-fluid py-4">
    <!-- Tiêu đề trang -->
    <div class="mb-4">
        <h1 class="h3 mb-2 fw-bold text-dark">
            <i class="bi bi-people-fill text-primary me-2"></i>Thành viên dự án: <span class="text-primary"><?= htmlspecialchars($data['project']['name']) ?></span>
        </h1>
        <p class="text-muted" style="font-size: 0.95rem;">Quản lý nhân sự, phân quyền vai trò và theo dõi hiệu suất làm việc của dự án.</p>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: DANH SÁCH THÀNH VIÊN HIỆN TẠI -->
        <div class="col-12 col-xl-8">
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

                                $avatarColors = ['06b6d4', 'f59e0b', '8b5cf6', '10b981', 'ec4899', '3b82f6'];
                                $avatarBg = $member['id'] ? $avatarColors[$member['id'] % count($avatarColors)] : '64748b';
                                $avatarUrl = !empty($member['avatar_url']) && $member['avatar_url'] !== 'default-avatar.png'
                                    ? BASE_URL . '/uploads/avatars/' . $member['avatar_url']
                                    : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=" . $avatarBg . "&color=fff";
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-sm" width="40" height="40">
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($displayName) ?></div>
                                                <small class="text-muted">@<?= htmlspecialchars($member['username']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($member['email'] ?? 'Chưa cập nhật') ?></td>
                                    <td>
                                        <span class="badge rounded px-3 py-1.5 <?= $member['role'] === 'manager' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' ?> text-capitalize small">
                                            <?= htmlspecialchars($member['role']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Gọi hàm JS mở Hồ sơ động kèm ID của User -->
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="viewStaffDetails(<?= $member['id'] ?>, '<?= htmlspecialchars($displayName) ?>')">
                                            <i class="bi bi-eye-fill me-1"></i> Xem hồ sơ
                                        </button>
                                        <!-- Chỉ hiển thị nút xóa nếu có role là manager và không được xóa manager khác -->
                                        <?php if ($data['project']['owner_id'] != $member['id'] && $_SESSION['user']['id'] != $member['id']  && $member['role'] !== 'manager'): ?>
                                            <a href="<?= BASE_URL ?>/project/removeMember/<?= $data['project']['id'] ?>/<?= $member['id'] ?>"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                onclick="return confirm('Bạn có chắc chắn muốn mời thành viên này ra khỏi dự án?');">
                                                <i class="bi bi-person-x-fill"></i> Xóa
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: THÊM THÀNH VIÊN MỚI -->
        <div class="col-12 col-xl-4">
            <div class="d-flex flex-column gap-4">

                <!-- KHỐI 1: THÊM THÀNH VIÊN MỚI -->
                <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-plus-fill text-success me-2"></i>Thêm thành viên</h5>

                    <?php if (empty($data['non_members'])): ?>
                        <div class="alert alert-info py-2 small mb-0">Tất cả nhân sự trong hệ thống đều đã tham gia dự án này.</div>
                    <?php else: ?>
                        <form action="<?= BASE_URL ?>/project/addMember" method="POST">
                            <input type="hidden" name="project_id" value="<?= $data['project']['id'] ?>">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Chọn nhân sự</label>
                                <select class="form-select border-secondary-subtle" name="user_id" required>
                                    <option value="" disabled selected>Chọn nhân viên...</option>
                                    <?php foreach ($data['non_members'] as $user):
                                        $uName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                        $displayUName = !empty($uName) ? $uName : $user['username'];
                                    ?>
                                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($displayUName) ?> (@<?= htmlspecialchars($user['username']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Vai trò dự án</label>
                                <select class="form-select border-secondary-subtle" name="role" required>
                                    <option value="member" selected>Member (Thực thi)</option>
                                    <option value="manager">Manager (Quản lý)</option>
                                    <option value="viewer">Viewer (Chỉ xem)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><i class="bi bi-check-lg me-1"></i>Thêm vào dự án</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal chi tiết nhân sự -->
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
    const baseUrl = "<?= BASE_URL ?>";
    // Hàm gọi Fetch API lấy chi tiết số liệu nhân sự động theo thời gian thực
    function viewStaffDetails(userId, displayName) {
        const modal = new bootstrap.Modal(document.getElementById('staffDetailModal'));
        document.getElementById('modalStaffName').textContent = displayName;

        const projectsList = document.getElementById('modalStaffProjects');
        const tasksContainer = document.getElementById('modalStaffTasks');

        // Hiển thị trạng thái đang nạp...
        projectsList.innerHTML = '<li class="list-group-item text-muted text-center py-3">Đang tải danh sách...</li>';
        tasksContainer.innerHTML = '<div class="col-12 text-muted text-center py-3">Đang thống kê...</div>';

        modal.show();


        fetch(`${baseUrl}/project/memberStats/${userId}?project_id=<?= $data['project']['id'] ?>`)
            .then(response => response.json())
            .then(data => {
                // Đổ danh sách các dự án tham gia
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

                // Chuyển mảng kết quả đếm về dạng Map key-value
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