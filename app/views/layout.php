<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'TaskSync' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (Dùng cho Sidebar/Header) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- CSS Custom của Thành phụ trách [4] -->
    <link rel="stylesheet" href="/TaskSync/public/css/style.css">
    
    <style>
        /* Tối ưu hóa layout để chứa Sidebar cố định */
        body { font-family: 'Inter', Arial, sans-serif; background: #f4f5f7; }
        .wrapper { display: flex; min-height: 100vh; }
        .main-content { flex: 1; display: flex; flex-direction: column; transition: all 0.3s; }
        .content-body { padding: 30px; flex: 1; }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- 1. NẠP SIDEBAR (Thanh điều hướng bên trái) [2] -->
        <?php require_once '../app/views/partials/sidebar.php'; ?>

        <div class="main-content">
            <!-- 2. NẠP HEADER (Thanh điều hướng trên) [2] -->
            <?php require_once '../app/views/partials/header.php'; ?>

            <!-- 3. NẠP GIAO DIỆN RUỘT (Main content) -->
            <div class="content-body">
                <div class="container-fluid bg-white p-4 rounded shadow-sm">
                    <?php 
                        if (isset($view) && file_exists('../app/views/' . $view . '.php')) {
                            require_once '../app/views/' . $view . '.php'; 
                        } else {
                            echo "<h3>Nội dung đang được cập nhật...</h3>";
                        }
                    ?>
                </div>
            </div>

            <!-- 4. NẠP FOOTER (Chân trang) [2] -->
            <?php require_once '../app/views/partials/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS Custom của Thành xử lý DOM/Kanban [5] -->
    <script src="/TaskSync/public/js/script.js"></script>
</body>
</html>
