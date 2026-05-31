<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'TaskSync' ?></title>
    <!--CSS : <link rel="stylesheet" href="/TaskSync/public/css/style.css"> -->
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 50px; }
        .container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <div class="container">
        <!-- NẠP GIAO DIỆN RUỘT VÀO ĐÂY -->
        <?php require_once '../app/views/' . $view . '.php'; ?>
    </div>

</body>
</html>