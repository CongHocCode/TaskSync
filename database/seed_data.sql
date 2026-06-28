-- ==========================================
-- 2. NẠP DỮ LIỆU MẪU CHẠY THỬ (SEED DATA)
-- ==========================================

-- 1. Tạo danh sách Người dùng mẫu (Mật khẩu chung: 123456)
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`, `first_name`, `last_name`) VALUES
(1, 'admin', 'admin@tasksync.vn', '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i', 'admin', 'active', 'Nguyễn', 'Át Min'),
(2, 'member1', 'member1@tasksync.vn', '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i', 'user', 'active', 'Văn C', 'Nguyễn'),
(3, 'alex', 'alex@tasksync.vn', '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i', 'user', 'active', 'Alex', 'Nguyen'),
(4, 'sarah', 'sarah@tasksync.vn', '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i', 'user', 'active', 'Sarah', 'Tran');

-- 2. Tạo danh sách Dự án mẫu
INSERT INTO `projects` (`id`, `name`, `key`, `description`, `owner_id`, `issue_counter`) VALUES
(1, 'TASKSYNC', 'TS', 'Hệ thống quản lý công việc nội bộ dạng Jira Clone', 1, 5),
(2, 'Frontend Reconstruction', 'WEB', 'Dự án tái cấu trúc giao diện responsive chuẩn hiện đại', 1, 3);

-- 3. Thiết lập mối quan hệ dự án (Chấp nhận và Phân vai)
INSERT INTO `project_members` (`project_id`, `user_id`, `role`, `status`, `invited_by`) VALUES
-- Dự án TASKSYNC (ID = 1)
(1, 1, 'manager', 'active', 1), -- Admin làm manager
(1, 2, 'member', 'active', 1),  -- Nguyễn Văn C tham gia hoạt động
(1, 3, 'member', 'active', 1),  -- Alex tham gia hoạt động
(1, 4, 'member', 'pending', 1), -- Sarah nhận lời mời chờ duyệt (Test quả chuông thông báo)

-- Dự án Frontend Reconstruction (ID = 2)
(2, 1, 'manager', 'active', 1),
(2, 2, 'member', 'active', 1),
(2, 4, 'member', 'active', 1);

-- 4. Tạo danh sách Công việc mẫu (Issues)
INSERT INTO `issues` (`id`, `project_id`, `parent_issue_id`, `issue_key`, `title`, `description`, `type`, `status`, `priority`, `reporter_id`, `assignee_id`) VALUES
-- Dự án 1: TASKSYNC
(1, 1, NULL, 'TS-1', 'Thiết lập khung lõi Router MVC', 'Hoàn thiện lớp App.php phân tích URL và Controller.php nạp Model/View', 'task', 'done', 'highest', 1, 1),
(2, 1, NULL, 'TS-2', 'Kết nối cơ sở dữ liệu PDO', 'Viết lớp kết nối Singleton Database.php chống SQL Injection', 'task', 'done', 'high', 1, 1),
(3, 1, NULL, 'TS-3', 'Đổ dữ liệu động lên bảng Kanban', 'Đồng bộ API và viết foreach lặp thẻ Card động từ Database', 'task', 'in_progress', 'medium', 1, 2),
(4, 1, NULL, 'TS-4', 'Bảo mật quyền truy cập chéo', 'Chặn đứng lỗ hổng bảo mật IDOR không cho phép xem dự án lén', 'bug', 'todo', 'highest', 1, 2),

-- THIẾT LẬP SUBTASK ĐỂ QUYỀN TEST NGAY LẬP TỨC:
-- Task TS-5 là Task cha, Task TS-6 có parent_issue_id trỏ về TS-5 làm Task con
(5, 1, NULL, 'TS-5', 'Xây dựng Module Bình luận (Comments)', 'Cho phép thành viên viết thảo luận trực tiếp trong Modal chi tiết công việc', 'story', 'in_progress', 'high', 1, 2),
(6, 1, 5, 'TS-6', 'Viết API lưu bình luận mới', 'Công việc con: Tiếp nhận chuỗi JSON và chạy INSERT lưu bình luận', 'task', 'todo', 'medium', 1, 2);

-- 5. Tạo danh sách Bình luận mẫu (Comments)
INSERT INTO `comments` (`id`, `issue_id`, `user_id`, `content`) VALUES
(1, 3, 1, 'Nguyễn Văn C ơi, kiểm tra xem bộ lọc tên đăng nhập đã so khớp đúng chưa nhé!'),
(2, 3, 2, 'Dạ em đã cập nhật đồng bộ so khớp theo Username rồi anh Át Min ơi, mượt lắm ạ!');