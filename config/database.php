<?php
/**
 * File cấu hình kết nối Cơ sở dữ liệu
 * Dự án: TaskSync
 */

// 1. Thông số kết nối (Mặc định cho XAMPP)
$host = 'localhost';        // Địa chỉ máy chủ database
$db   = 'task_sync';        // Tên database (Q2 sẽ tạo tên này)
$user = 'root';             // Tài khoản mặc định của XAMPP
$pass = '';                 // Mật khẩu mặc định của XAMPP là rỗng
$charset = 'utf8mb4';       // Bảng mã hỗ trợ tiếng Việt có dấu và Emoji

// 2. Cấu hình DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 3. Các tùy chọn thiết lập cho PDO
$options = [
    // Bật chế độ báo lỗi bằng Exception (Giúp dễ debug khi viết sai SQL)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Thiết lập kiểu dữ liệu trả về mặc định là Mảng kết hợp (Associative Array)
    // Ví dụ: $result['fullname'] thay vì $result[0]
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Tắt chế độ giả lập câu lệnh thực thi (Tăng tính bảo mật chống SQL Injection)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4. Tiến hành kết nối
try {
    // Khởi tạo đối tượng PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Nếu muốn kiểm tra kết nối khi mới setup, bạn có thể bỏ comment dòng dưới:
    // echo "Kết nối Database thành công!";

} catch (\PDOException $e) {
    // Nếu có lỗi (ví dụ sai tên DB hoặc MySQL chưa bật), nó sẽ nhảy vào đây
    // die() sẽ dừng toàn bộ chương trình và hiện thông báo lỗi
    die("Lỗi kết nối Cơ sở dữ liệu: " . $e->getMessage());
}

// Biến $pdo sẽ được dùng xuyên suốt toàn bộ dự án để truy vấn dữ liệu.