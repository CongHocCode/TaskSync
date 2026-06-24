<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'TaskSync' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- CSS hệ thống -->
    <?php
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $assetBase = ($base === '/' ? '' : $base) . '/css';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $assetBase ?>/style.css">
</head>

<body>
    <div class="app-shell">
        <!-- Overlay cho Sidebar di động -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar chung -->
        <?php 
        // Nếu là Admin thì nạp menu Admin, ngược lại nạp menu làm việc thường
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
            require_once __DIR__ . '/partials/sidebar_admin.php';
        } else {
            require_once __DIR__ . '/partials/sidebar.php';
        } 
        ?>

        <!-- Main area uses a light theme to match screenshot */ -->
        <div class="app-main light">
            <!-- Header chung -->
            <?php require_once __DIR__ . '/partials/header.php'; ?>

            <!-- Nội dung động -->
            <main class="app-content">
                <div class="content-wrapper">
                    <?php
                    if (isset($view) && file_exists(__DIR__ . '/' . $view . '.php')) {
                        require_once __DIR__ . '/' . $view . '.php';
                    } else {
                        echo '<section class="app-card app-alert danger"><h3>Nội dung đang được cập nhật...</h3><p>Vui lòng kiểm tra lại đường dẫn hoặc chờ cập nhật nội dung.</p></section>';
                    }
                    ?>
                </div>
            </main>

            <!-- Footer chung -->
            <?php require_once __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $assetBase ?>/../js/script.js"></script>
</body>

</html>