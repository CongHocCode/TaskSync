<?php
$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
$user = $_SESSION['user'] ?? null;

// Lấy ảnh đại diện và thông tin của user
$displayName = $userSession['display_name'] ?? ($userSession['username'] ?? 'User');
$avatarFile = $userSession['avatar_url'] ?? '';
// Nếu có file ảnh trên máy chủ, lấy ảnh thật. Ngược lại, lấy ảnh chữ tự động làm dự phòng
$sidebarAvatarUrl = (!empty($avatarFile) && $avatarFile !== 'default-avatar.png')
    ? BASE_URL . '/uploads/avatars/' . $avatarFile
    : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=7c3aed&color=fff";

// Nạp ProjectModel để lấy danh sách lời mời chờ duyệt cho quả chuông
$pendingInvites = [];
if (isset($_SESSION['user']['id'])) {
    require_once __DIR__ . '/../../models/ProjectModel.php';
    $notificationModel = new ProjectModel();
    $pendingInvites = $notificationModel->getPendingInvitations($_SESSION['user']['id']);
}
$inviteCount = count($pendingInvites);
?>
<header class="app-header">
    <div class="header-brand">
        <button class="app-btn app-btn-ghost d-lg-none me-2" id="mobile-sidebar-toggle" type="button" style="padding: 0.5rem; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(11, 18, 32, 0.1);" aria-label="Menu">
            <i class="bi bi-list" style="font-size: 1.35rem; color: inherit;"></i>
        </button>
        <?php if ($isAdmin): ?>
            <div class="header-session">
                <i class="bi bi-shield-lock"></i>
                <div>
                    <div class="session-label">Admin Session:</div>
                    <div class="session-user"><?= htmlspecialchars($displayName) ?></div>
                </div>
            </div>
        <?php else: ?>
            <!-- Cho member, đặt ô tìm kiếm ở bên trái -->
            <div class="search-wrapper">
                <input type="text" class="app-input" placeholder="Tìm kiếm mã hoặc từ khóa task..." aria-label="Tìm kiếm" />
                <button class="app-btn app-btn-ghost" type="button" aria-label="Tìm kiếm">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="header-actions">
        <?php if ($isAdmin): ?>
            <!-- Cho admin, ô tìm kiếm ở bên phải -->
            <div class="search-wrapper">
                <input type="text" class="app-input" placeholder="Tìm kiếm mã hoặc từ khóa task..." aria-label="Tìm kiếm" />
                <button class="app-btn app-btn-ghost" type="button" aria-label="Tìm kiếm">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        <?php endif; ?>

        <!--THÔNG BÁO -->
        <div class="dropdown me-3">
            <button class="btn btn-link text-white position-relative p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none;">
                <i class="bi bi-bell fs-5"></i>
                <!-- Nếu có lời mời, hiển thị chấm đỏ đếm số lượng thông báo -->
                <?php if ($inviteCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger p-1">
                        <span class="visually-hidden">Lời mời mới</span>
                    </span>
                <?php endif; ?>
            </button>

            <!-- Menu thông báo thả xuống -->
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow py-2 text-dark" style="width: 320px; border-radius: 12px;">
                <li class="px-3 py-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">Thông báo lời mời</h6>
                </li>

                <?php if ($inviteCount === 0): ?>
                    <li class="px-3 py-4 text-center text-muted small">
                        <i class="bi bi-bell-slash fs-4 d-block mb-1 opacity-50"></i> Không có lời mời dự án nào.
                    </li>
                <?php else: ?>
                    <div style="max-height: 250px; overflow-y: auto;">
                        <?php foreach ($pendingInvites as $invite):
                            $avatarColors = ['06b6d4', 'f59e0b', '8b5cf6', '10b981', 'ec4899', '3b82f6'];
                            $avatarBg = $invite['project_id'] ? $avatarColors[$invite['project_id'] % count($avatarColors)] : '64748b';
                            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($invite['sender_name']) . "&background=" . $avatarBg . "&color=fff";
                        ?>
                            <li class="px-3 py-3 border-bottom list-unstyled">
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-xs" width="30" height="30">
                                    <div>
                                        <small class="fw-bold text-dark d-block"><?= htmlspecialchars($invite['sender_name']) ?></small>
                                        <span class="text-secondary small" style="font-size: 0.82rem; line-height: 1.4; display: block;">
                                            Đã mời bạn vào dự án: <strong><?= htmlspecialchars($invite['project_name']) ?> (<?= $invite['project_key'] ?>)</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-1">
                                    <!-- Nút Từ chối -->
                                    <a href="<?= BASE_URL ?>/project/declineInvite/<?= $invite['project_id'] ?>" class="btn btn-xs btn-outline-danger py-1 px-2.5 rounded-pill small" style="font-size: 0.72rem; font-weight: bold;">
                                        <i class="bi bi-x-lg"></i> Từ chối
                                    </a>
                                    <!-- Nút Đồng ý -->
                                    <a href="<?= BASE_URL ?>/project/acceptInvite/<?= $invite['project_id'] ?>" class="btn btn-xs btn-success py-1 px-2.5 rounded-pill small text-white" style="font-size: 0.72rem; font-weight: bold;">
                                        <i class="bi bi-check-lg"></i> Chấp nhận
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </ul>
        </div>

        <div class="user-chip">
            <img src="<?= $sidebarAvatarUrl ?>" alt="Avatar" class="user-avatar" style="object-fit: cover;">
        </div>

        <!-- Nút chuyển chế độ cho admin-->
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'):
            $normalizedView = isset($view) ? str_replace('\\', '/', $view) : '';
            // Thỏa mãn cả trường hợp nằm trong thư mục admin/ hoặc file đặt tại dashboard/admin
            $isAdminPage = (strpos($normalizedView, 'admin/') !== false || strpos($normalizedView, 'dashboard/admin') !== false);
        ?>
            <?php if ($isAdminPage): ?>
                <!-- ĐANG Ở ADMIN: Bấm nút này sẽ trỏ về URL: /workspace (Gọi Workspace Controller -> index) -->
                <a href="<?= BASE_URL ?>/workspace"
                    class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 shadow-sm"
                    style="font-size: 0.8rem; border-color: rgba(255,255,255,0.35);">
                    <i class="bi bi-person-workspace text-warning"></i> Không gian làm việc
                </a>
            <?php else: ?>
                <!-- ĐANG Ở WORKSPACE: Bấm nút này sẽ trỏ về URL: /admin/dashboard (Gọi Admin Controller -> dashboard) -->
                <a href="<?= BASE_URL ?>/admin/dashboard"
                    class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 shadow-sm"
                    style="font-size: 0.8rem; border-color: rgba(255,255,255,0.35);">
                    <i class="bi bi-shield-lock-fill text-warning"></i> Hệ thống Admin
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</header>