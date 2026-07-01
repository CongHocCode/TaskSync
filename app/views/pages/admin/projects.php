<div class="page-content" style="font-family: 'Inter', sans-serif; background-color: #f6f8fb; padding: 24px;">
    <!-- TIÊU ĐỀ TRANG -->
    <div class="page-header mb-4">
        <div>
            <h2 style="font-weight: 800; font-size: 1.75rem; color: #0f172a; margin-bottom: 4px;">Quản lý các dự án</h2>
            <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 0;">Giám sát các workspace, chuyển đổi quyền sở hữu hoặc xóa các dự án không hoạt động.</p>
        </div>
    </div>

    <!-- HIỂN THỊ THÔNG BÁO FLASH -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: rgba(57,192,141,0.12); color: var(--success); font-weight: 600; padding: 16px 20px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.15rem;"></i>
                <span><?= $_SESSION['flash_success'] ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 14px; right: 15px;"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: rgba(241,101,101,0.12); color: var(--danger); font-weight: 600; padding: 16px 20px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.15rem;"></i>
                <span><?= $_SESSION['flash_error'] ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 14px; right: 15px;"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- THANH TÌM KIẾM VÀ TRẠNG THÁI SỐ LƯỢNG -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
        <!-- Ô tìm kiếm dạng bo góc vừa -->
        <div style="position: relative; width: 100%; max-width: 360px;">
            <i class="bi bi-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.05rem;"></i>
            <input type="text" id="project-search-input" class="form-control" placeholder="Tìm tên dự án, key mã hóa, trưởng dự án..." style="border-radius: 12px; border: 1px solid #cbd5e1; padding: 12px 16px 12px 44px; font-size: 0.92rem; color: #1e293b; background: #ffffff; box-shadow: none;" />
        </div>
        <!-- Số lượng hiển thị -->
        <div style="color: #64748b; font-size: 0.95rem; font-weight: 500;">
            Hiển thị: <span id="display-count" style="font-weight: 700; color: #1e293b;"><?= count($data['projects']) ?></span> trên tổng số <span id="total-count" style="font-weight: 700; color: #1e293b;"><?= count($data['projects']) ?></span> dự án
        </div>
    </div>

    <!-- BẢNG DANH SÁCH DỰ ÁN -->
    <div class="app-card" style="padding: 0; border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
        <div class="table-responsive">
            <table class="table mb-0" style="width: 100%; border-collapse: collapse; text-align: left; vertical-align: middle;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                        <th style="padding: 18px 24px; font-weight: 700; color: #94a3b8; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase;">Tên dự án</th>
                        <th style="padding: 18px 24px; font-weight: 700; color: #94a3b8; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase;">Mã key</th>
                        <th style="padding: 18px 24px; font-weight: 700; color: #94a3b8; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase;">Trưởng dự án (Owner)</th>
                        <th style="padding: 18px 24px; font-weight: 700; color: #94a3b8; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase; text-align: center;">Số lượng thành viên</th>
                        <th style="padding: 18px 24px; font-weight: 700; color: #94a3b8; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase; text-align: center;">Tổng số tasks</th>
                        <th style="padding: 18px 24px; font-weight: 700; color: #94a3b8; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase;">Ngày thiết lập</th>
                        <th style="padding: 18px 24px; font-weight: 700; color: #94a3b8; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase; text-align: right;">Hành động quản lý</th>
                    </tr>
                </thead>
                <tbody id="project-table-body">
                    <?php if (empty($data['projects'])): ?>
                        <tr class="empty-row">
                            <td colspan="7" style="padding: 48px; text-align: center; color: #64748b; font-size: 0.95rem;">Chưa có dự án nào trên hệ thống.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['projects'] as $p):
                            $ownerName = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
                            if (empty($ownerName)) {
                                $ownerName = $p['username'] ?? 'Chưa có Owner';
                            }
                            $avatarUrl = (!empty($p['avatar_url']) && $p['avatar_url'] !== 'default-avatar.png')
                                ? BASE_URL . '/uploads/avatars/' . $p['avatar_url']
                                : "https://ui-avatars.com/api/?name=" . urlencode($ownerName) . "&background=7c3aed&color=fff";
                            $createdDate = date('Y-m-d', strtotime($p['created_at']));
                        ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;"
                                onmouseover="this.style.background='#f8fafc'"
                                onmouseout="this.style.background='transparent'"
                                data-project-name="<?= htmlspecialchars($p['name']) ?>"
                                data-project-key="<?= htmlspecialchars($p['key']) ?>"
                                data-project-owner="<?= htmlspecialchars($ownerName) ?>">

                                <!-- Tên dự án & Mô tả -->
                                <td style="padding: 20px 24px; max-width: 320px;">
                                    <div style="font-weight: 700; color: #0f172a; font-size: 0.98rem;"><?= htmlspecialchars($p['name']) ?></div>
                                    <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px; font-weight: 400; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($p['description'] ?? '') ?>">
                                        <?= htmlspecialchars($p['description'] ?? 'Không có mô tả dự án.') ?>
                                    </div>
                                </td>

                                <!-- Mã key badge -->
                                <td style="padding: 20px 24px;">
                                    <span class="badge" style="background: #f1f5f9; color: #475569; font-weight: 700; border-radius: 8px; border: 1px solid #e2e8f0; padding: 6px 12px; font-size: 0.82rem; letter-spacing: 0.02em;">
                                        <?= htmlspecialchars($p['key']) ?>
                                    </span>
                                </td>

                                <!-- Trưởng dự án (Owner) -->
                                <td style="padding: 20px 24px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img src="<?= $avatarUrl ?>" alt="<?= htmlspecialchars($ownerName) ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                                        <span style="font-weight: 600; color: #334155; font-size: 0.92rem;"><?= htmlspecialchars($ownerName) ?></span>
                                    </div>
                                </td>

                                <!-- Số lượng thành viên -->
                                <td style="padding: 20px 24px; text-align: center; color: #64748b; font-weight: 600; font-size: 0.95rem;">
                                    <?= htmlspecialchars($p['member_count']) ?>
                                </td>

                                <!-- Tổng số tasks -->
                                <td style="padding: 20px 24px; text-align: center; color: #4f46e5; font-weight: 600; font-size: 0.95rem;">
                                    <?= htmlspecialchars($p['task_count']) ?>
                                </td>

                                <!-- Ngày thiết lập -->
                                <td style="padding: 20px 24px; color: #94a3b8; font-size: 0.88rem; font-weight: 500;">
                                    <?= htmlspecialchars($createdDate) ?>
                                </td>

                                <!-- Hành động quản lý -->
                                <td style="padding: 20px 24px; text-align: right;">
                                    <div style="display: inline-flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                        <!-- Nút Cấu hình -->
                                        <a href="<?= BASE_URL ?>/project/settings/<?= $p['id'] ?>"
                                            class="btn btn-sm"
                                            style="font-size: 0.82rem; font-weight: 600; padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #0284c7; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class="bi bi-gear" style="font-size: 0.95rem; color: #0284c7;"></i>
                                            <span>Cấu hình</span>
                                        </a>

                                        <!-- Nút Đổi Owner -->
                                        <button type="button" class="btn btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#changeOwnerModal"
                                            data-project-id="<?= $p['id'] ?>"
                                            data-project-name="<?= htmlspecialchars($p['name']) ?>"
                                            data-owner-id="<?= $p['owner_id'] ?>"
                                            style="font-size: 0.82rem; font-weight: 600; padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class="bi bi-person-gear" style="font-size: 0.95rem; color: #64748b;"></i>
                                            <span>Đổi Owner</span>
                                        </button>

                                        <!-- Nút Xóa -->
                                        <a href="<?= BASE_URL ?>/admin/deleteProject/<?= $p['id'] ?>"
                                            class="btn btn-sm"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này? Thao tác này sẽ xóa toàn bộ công việc và thành viên thuộc dự án.')"
                                            style="font-size: 0.82rem; font-weight: 600; padding: 6px 12px; border-radius: 8px; border: 1px solid #fee2e2; background: #fef2f2; color: #ef4444; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class="bi bi-trash" style="font-size: 0.95rem;"></i>
                                            <span>Xóa</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Dòng rỗng ẩn dùng khi tìm kiếm không ra kết quả -->
                        <tr class="empty-row" style="display: none;">
                            <td colspan="7" style="padding: 48px; text-align: center; color: #64748b; font-size: 0.95rem;">Không tìm thấy dự án phù hợp với từ khóa tìm kiếm.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Đổi Chủ Sở Hữu (Change Owner) -->
<div class="modal fade text-dark" id="changeOwnerModal" tabindex="-1" aria-labelledby="changeOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.12); overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="changeOwnerModalLabel">
                    <i class="bi bi-person-gear text-primary me-2"></i>Thay đổi trưởng dự án (Owner)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="box-shadow: none;"></button>
            </div>
            <form action="<?= BASE_URL ?>/admin/changeProjectOwner" method="POST">
                <input type="hidden" name="project_id" id="modal-project-id" value="">
                <div class="modal-body px-4 py-3">
                    <p class="text-secondary small mb-3">Bạn đang thay đổi trưởng dự án cho dự án <strong id="modal-project-name" class="text-dark"></strong>. Người sở hữu mới phải là thành viên hiện tại của dự án.</p>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Chọn trưởng dự án mới</label>
                        <select class="form-select border-secondary-subtle" name="new_owner_id" required style="border-radius: 8px; padding: 10px; font-size: 0.92rem; box-shadow: none;">
                            <!-- Thành viên dự án sẽ được nạp động từ JS -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.9rem; font-weight: 600;">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-size: 0.9rem; font-weight: 600; background: var(--primary); border: none;">Xác nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS XỬ LÝ TÌM KIẾM VÀ POPULATE MODAL -->
<script>
    // Dữ liệu thành viên hoạt động của từng dự án nạp từ PHP
    const projectMembers = <?= json_encode($data['project_members'] ?? []) ?>;

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Live Search phía Client
        const searchInput = document.getElementById('project-search-input');
        const tableRows = document.querySelectorAll('#project-table-body tr:not(.empty-row)');
        const displayCountSpan = document.getElementById('display-count');
        const emptyRows = document.querySelectorAll('#project-table-body .empty-row');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const name = row.getAttribute('data-project-name').toLowerCase();
                    const key = row.getAttribute('data-project-key').toLowerCase();
                    const owner = row.getAttribute('data-project-owner').toLowerCase();

                    if (name.includes(query) || key.includes(query) || owner.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Cập nhật số lượng đếm hiển thị
                if (displayCountSpan) {
                    displayCountSpan.textContent = visibleCount;
                }

                // Xử lý dòng rỗng khi không có kết quả
                if (emptyRows.length > 0) {
                    if (visibleCount === 0 && tableRows.length > 0) {
                        // Nếu không có dự án nào khớp từ khóa, hiện thông báo "Không tìm thấy"
                        emptyRows.forEach(el => {
                            if (el.innerText.includes('Không tìm thấy')) {
                                el.style.display = '';
                            } else {
                                el.style.display = 'none';
                            }
                        });
                    } else if (tableRows.length === 0) {
                        // Nếu hệ thống thực sự rỗng
                        emptyRows.forEach(el => {
                            if (el.innerText.includes('Chưa có dự án')) {
                                el.style.display = '';
                            } else {
                                el.style.display = 'none';
                            }
                        });
                    } else {
                        emptyRows.forEach(el => el.style.display = 'none');
                    }
                }
            });
        }

        // 2. Gán dữ liệu động vào Modal đổi Owner
        const changeOwnerModal = document.getElementById('changeOwnerModal');
        if (changeOwnerModal) {
            changeOwnerModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const projectId = button.getAttribute('data-project-id');
                const projectName = button.getAttribute('data-project-name');
                const currentOwnerId = button.getAttribute('data-owner-id');

                const modalProjectIdInput = changeOwnerModal.querySelector('#modal-project-id');
                const modalProjectNameSpan = changeOwnerModal.querySelector('#modal-project-name');
                const selectElement = changeOwnerModal.querySelector('select[name="new_owner_id"]');

                modalProjectIdInput.value = projectId;
                modalProjectNameSpan.textContent = projectName;

                // Nạp động các thành viên của dự án này vào dropdown
                if (selectElement) {
                    selectElement.innerHTML = '';
                    const members = projectMembers[projectId] || [];

                    if (members.length === 0) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'Không có thành viên nào hoạt động';
                        opt.disabled = true;
                        selectElement.appendChild(opt);
                    } else {
                        members.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.user_id;

                            let displayName = ((m.first_name || '') + ' ' + (m.last_name || '')).trim();
                            if (!displayName) {
                                displayName = m.username;
                            }

                            opt.textContent = displayName + ' (@' + m.username + ')';

                            if (m.user_id == currentOwnerId) {
                                opt.selected = true;
                            }

                            selectElement.appendChild(opt);
                        });
                    }
                }
            });
        }
    });
</script>