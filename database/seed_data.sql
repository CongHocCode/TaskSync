-- ==============================================================
-- FILE 2: NẠP DỮ LIỆU MẪU CHẠY THỬ (task_sync_seed.sql)
-- ==============================================================
-- 1. NẠP DỮ LIỆU TÀI KHOẢN NGƯỜI DÙNG (Mật khẩu chung giải mã là: 123456)
INSERT INTO
    users (
        id,
        username,
        email,
        password_hash,
        role,
        status,
        first_name,
        last_name
    )
VALUES
    (
        1,
        'admin',
        'asdvbn666999@gmail.com',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'admin',
        'active',
        'Nguyễn',
        'Át Min'
    ),
    (
        2,
        'member1',
        'cuong.nguyen@tasksync.vn',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'user',
        'active',
        'Nguyễn',
        'Văn Cường'
    ),
    (
        3,
        'alex',
        'alex.nguyen@tasksync.vn',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'user',
        'active',
        'Alex',
        'Nguyễn'
    ),
    (
        4,
        'sarah',
        'sarah.tran@tasksync.vn',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'user',
        'active',
        'Sarah',
        'Trần'
    ),
    (
        5,
        'hung_le',
        'hung.le@tasksync.vn',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'user',
        'active',
        'Lê',
        'Mạnh Hùng'
    ),
    (
        6,
        'thanh_tran',
        'thanh.tran@tasksync.vn',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'user',
        'active',
        'Trần',
        'Hữu Thành'
    ),
    (
        7,
        'quyen_pham',
        'quyen.pham@tasksync.vn',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'user',
        'active',
        'Phạm',
        'Gia Quyền'
    ),
    (
        8,
        'trang_dang',
        'trang.dang@tasksync.vn',
        '$2y$10$F3oF9BQkQhKrshFXx2F8uu/ZqLgtCJ.zRn8q3t0OdVd8aG1fL0J9i',
        'user',
        'active',
        'Đặng',
        'Thu Trang'
    );

-- 2. NẠP DANH SÁCH CÁC DỰ ÁN MẪU TRÊN HỆ THỐNG
INSERT INTO
    projects (
        id,
        name,
        `key`,
        description,
        owner_id,
        issue_counter,
        github_repo_url
    )
VALUES
    (
        1,
        'TASKSYNC',
        'TS',
        'Hệ thống quản lý công việc nội bộ dạng Jira Clone',
        1,
        6,
        -- Thư viện Bootstrap 5 làm mốc liên kết dự án TASKSYNC [2]
        'https://github.com/CongHocCode/TaskSync'
    ),
    (
        2,
        'Frontend Reconstruction',
        'WEB',
        'Dự án tái cấu trúc giao diện responsive chuẩn hiện đại',
        5,
        4,
        -- Thư viện Tailwind CSS làm mốc liên kết cho WEB [38]
        'https://github.com/tailwindlabs/tailwindcss'
    ),
    (
        3,
        'API Gateway Infrastructure',
        'API',
        'Xây dựng cổng kết nối dịch vụ và phân luồng tải hệ thống',
        1,
        0,
        -- Thư viện ExpressJS nổi tiếng làm mốc liên kết cho API
        'https://github.com/expressjs/express'
    );

-- 3. PHÂN CHIA VAI TRÒ VÀ TRẠNG THÁI THÀNH VIÊN TRONG CÁC DỰ ÁN (Project Members)
INSERT INTO
    project_members (project_id, user_id, role, status, invited_by)
VALUES
    -- Dự án TASKSYNC (ID = 1)
    (1, 1, 'manager', 'active', 1), -- Admin là Manager
    (1, 2, 'member', 'active', 1), -- Nguyễn Văn Cường (Active)
    (1, 3, 'member', 'active', 1), -- Alex Nguyễn (Active)
    (1, 7, 'member', 'active', 1), -- Phạm Gia Quyền (Active)
    (1, 4, 'member', 'pending', 1), -- Sarah Trần đang nhận lời mời (Chờ duyệt ở quả chuông)
    -- Dự án Frontend Reconstruction (ID = 2)
    (2, 5, 'manager', 'active', 5), -- Lê Mạnh Hùng là Manager gốc (Owner)
    (2, 1, 'manager', 'active', 5), -- Admin tham gia hỗ trợ quản lý
    (2, 6, 'member', 'active', 5), -- Trần Hữu Thành tham gia phát triển UI
    (2, 8, 'member', 'active', 5), -- Đặng Thu Trang tham gia kiểm thử (Tester)
    (2, 3, 'member', 'pending', 5), -- Alex Nguyễn đang nhận lời mời chờ duyệt
    -- Dự án API Gateway Infrastructure (ID = 3)
    (3, 1, 'manager', 'active', 1),
    (3, 7, 'member', 'active', 1);

-- 4. DANH SÁCH CÔNG VIỆC MẪU (Issues & Subtasks)
INSERT INTO
    issues (
        id,
        project_id,
        parent_issue_id,
        issue_key,
        title,
        description,
        type,
        status,
        priority,
        reporter_id,
        assignee_id,
        due_date
    )
VALUES
    -- Dự án 1: TASKSYNC (TS)
    (
        1,
        1,
        NULL,
        'TS-1',
        'Thiết lập khung lõi Router MVC',
        'Hoàn thiện lớp App.php phân tích URL và Controller.php nạp Model/View',
        'task',
        'done',
        'highest',
        1,
        1,
        '2026-07-10 18:00:00'
    ),
    (
        2,
        1,
        NULL,
        'TS-2',
        'Kết nối cơ sở dữ liệu PDO',
        'Viết lớp kết nối Singleton Database.php chống SQL Injection',
        'task',
        'done',
        'high',
        1,
        7,
        '2026-07-12 18:00:00'
    ),
    (
        3,
        1,
        NULL,
        'TS-3',
        'Đổ dữ liệu động lên bảng Kanban',
        'Đồng bộ API và viết foreach lặp thẻ Card động từ Database',
        'task',
        'in_progress',
        'medium',
        1,
        2,
        '2026-07-15 18:00:00'
    ),
    (
        4,
        1,
        NULL,
        'TS-4',
        'Bảo mật quyền truy cập chéo',
        'Chặn đứng lỗ hổng bảo mật IDOR không cho phép xem dự án lén',
        'bug',
        'todo',
        'highest',
        1,
        2,
        '2026-07-08 18:00:00'
    ),
    (
        5,
        1,
        NULL,
        'TS-5',
        'Xây dựng Module Bình luận (Comments)',
        'Cho phép thành viên viết thảo luận trực tiếp trong Modal chi tiết công việc',
        'story',
        'in_progress',
        'high',
        1,
        7,
        '2026-07-20 18:00:00'
    ),
    -- Task con (Subtask) của TS-5
    (
        6,
        1,
        5,
        'TS-6',
        'Viết API lưu bình luận mới',
        'Công việc con: Tiếp nhận chuỗi JSON và chạy INSERT lưu bình luận',
        'task',
        'todo',
        'medium',
        1,
        7,
        '2026-07-18 18:00:00'
    ),
    -- Dự án 2: Frontend Reconstruction (WEB)
    (
        7,
        2,
        NULL,
        'WEB-1',
        'Xây dựng Master Layout cho ứng dụng',
        'Cắt nhỏ header, sidebar, footer và tích hợp vào khung layout.php',
        'task',
        'done',
        'highest',
        5,
        6,
        '2026-07-11 18:00:00'
    ),
    (
        8,
        2,
        NULL,
        'WEB-2',
        'Thiết kế CSS Dark/Light Hybrid',
        'Cấu hình CSS Variables cho màu nền xám xanh sáng và sidebar màu tối',
        'task',
        'in_progress',
        'high',
        5,
        6,
        '2026-07-14 18:00:00'
    ),
    (
        9,
        2,
        NULL,
        'WEB-3',
        'Viết kịch bản kéo thả bảng Kanban',
        'Sử dụng Drag & Drop API của HTML5 để di chuyển các thẻ Card mượt mà',
        'task',
        'in_review',
        'medium',
        5,
        6,
        '2026-07-16 18:00:00'
    ),
    (
        10,
        2,
        NULL,
        'WEB-4',
        'Kiểm thử khả năng đáp ứng (Responsive)',
        'Kiểm tra giao diện trên điện thoại và máy tính bảng, sửa các lỗi vỡ khung',
        'bug',
        'todo',
        'high',
        5,
        8,
        '2026-07-09 18:00:00'
    );

-- 6. DANH SÁCH THẢO LUẬN MẪU (Comments)
INSERT INTO
    comments (id, issue_id, user_id, content)
VALUES
    (
        1,
        3,
        1,
        'Nguyễn Văn Cường ơi, kiểm tra xem bộ lọc tên đăng nhập đã so khớp đúng chưa nhé!'
    ),
    (
        2,
        3,
        2,
        'Dạ em đã cập nhật đồng bộ so khớp theo Username rồi anh Át Min ơi, mượt lắm ạ!'
    ),
    (
        3,
        5,
        7,
        'Phần Database cho Comments đã thiết lập xong khóa ngoại CASCADE đầy đủ nhé cả nhà.'
    ),
    (
        4,
        5,
        1,
        'Tuyệt vời Quyền ơi, để chiều nay tớ bảo Thành kết nối nốt giao diện vào là chạy ngon luôn!'
    );

-- 7. Nạp cài đặt hệ thống
INSERT INTO
    `system_settings` (`key`, `value`)
VALUES
    ('maintenance_mode', 'off'),
    ('allow_registration', 'on'),
    ('max_upload_size', '2');