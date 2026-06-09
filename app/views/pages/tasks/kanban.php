<section class="kanban-page">
    <div class="page-heading">
        <div>
            <h1>Bảng Kanban</h1>
            <p>Quản lý trạng thái công việc theo 4 cột: To Do, In Progress, In Review, Done.</p>
        </div>
        <button class="app-btn app-btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#taskDetailCanvas" aria-controls="taskDetailCanvas">
            Xem chi tiết công việc
        </button>
    </div>

    <div class="kanban-board">
        <div class="kanban-column">
            <div class="kanban-column-header">
                <h2>To Do</h2>
                <span>4</span>
            </div>
            <div class="kanban-card">
                <h3>Thiết kế UI trang đăng nhập</h3>
                <p>Hoàn thiện mẫu giao diện và gửi review cho team.</p>
                <div class="kanban-card-meta">Hạn: 05/06</div>
            </div>
            <div class="kanban-card">
                <h3>Lập kế hoạch sprint mới</h3>
                <p>Chuẩn bị backlog và phân công nhiệm vụ.</p>
                <div class="kanban-card-meta">Hạn: 06/06</div>
            </div>
        </div>
        <div class="kanban-column">
            <div class="kanban-column-header">
                <h2>In Progress</h2>
                <span>3</span>
            </div>
            <div class="kanban-card">
                <h3>Phát triển API đăng nhập</h3>
                <p>Hoàn tất xác thực và trả về token.</p>
                <div class="kanban-card-meta">Hạn: 07/06</div>
            </div>
            <div class="kanban-card">
                <h3>Kiểm thử chức năng tìm kiếm</h3>
                <p>Test các kịch bản tìm kiếm theo từ khóa.</p>
                <div class="kanban-card-meta">Hạn: 08/06</div>
            </div>
        </div>
        <div class="kanban-column">
            <div class="kanban-column-header">
                <h2>In Review</h2>
                <span>2</span>
            </div>
            <div class="kanban-card">
                <h3>Đánh giá UX flow</h3>
                <p>Nhận phản hồi từ nhóm thiết kế và cập nhật.</p>
                <div class="kanban-card-meta">Hạn: 09/06</div>
            </div>
            <div class="kanban-card">
                <h3>Xem lại báo cáo lỗi</h3>
                <p>Kiểm tra lại các báo cáo từ QA.</p>
                <div class="kanban-card-meta">Hạn: 10/06</div>
            </div>
        </div>
        <div class="kanban-column">
            <div class="kanban-column-header">
                <h2>Done</h2>
                <span>5</span>
            </div>
            <div class="kanban-card">
                <h3>Cài đặt môi trường dev</h3>
                <p>Đã hoàn thành cấu hình server và database.</p>
                <div class="kanban-card-meta">Hoàn tất</div>
            </div>
            <div class="kanban-card">
                <h3>Thiết lập CI/CD</h3>
                <p>Pipeline đã sẵn sàng và chạy tự động.</p>
                <div class="kanban-card-meta">Hoàn tất</div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../partials/task_modal_right.php'; ?>
</section>
