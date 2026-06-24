<?php
$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
$user = $_SESSION['user'] ?? null;
$displayName = $user['display_name'] ?? 'User';
?>
<header class="app-header">
    <div class="header-brand">
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

        <button class="app-btn" type="button" aria-label="Thông báo">
            <i class="bi bi-bell"></i>
        </button>

        <div class="user-chip">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($displayName) ?>&background=7c3aed&color=fff" alt="Avatar" class="avatar-sm">
        </div>

        <?php if ($isAdmin): ?>
            <button class="app-btn" type="button">
                <i class="bi bi-layout-split"></i>
                <span>Bảng Kanban/Member</span>
            </button>
        <?php endif; ?>
    </div>
</header>

