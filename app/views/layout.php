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
    <link rel="stylesheet" href="/TaskSync/public/css/style.css">
</head>
<body>
    <div class="app-shell">
        <!-- Sidebar chung -->
        <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

        <div class="app-main">
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
    <script src="/TaskSync/public/js/script.js"></script>
</body>
</html>
