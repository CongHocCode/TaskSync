-- 1. Tạo cơ sở dữ liệu nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS `task_sync` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- 2. Chỉ định sử dụng cơ sở dữ liệu vừa tạo cho các câu lệnh phía sau
USE `task_sync`;