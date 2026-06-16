<div class="offcanvas offcanvas-end task-details-panel" tabindex="-1" id="taskDetailCanvas" aria-labelledby="taskDetailCanvasLabel">
    <div class="offcanvas-header">
        <div>
            <h5 class="offcanvas-title" id="taskDetailCanvasLabel">Chi tiết công việc</h5>
            <p class="text-muted">Xem / chỉnh sửa nhanh trạng thái task</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>
    <div class="offcanvas-body">
        <section class="task-detail-card">
            <div class="task-detail-head">
                <span class="badge badge-secondary">In Progress</span>
                <h3>Phát triển API đăng nhập</h3>
                <p class="text-muted">Hoàn thiện xác thực, token và kiểm thử.</p>
            </div>

            <div class="task-detail-section">
                <h4>Mô tả nhanh</h4>
                <textarea class="app-input task-textarea" rows="4" placeholder="Viết mô tả nhanh...">Xây dựng endpoint đăng nhập cho người dùng, xử lý xác thực và trả về JWT.</textarea>
            </div>

            <div class="task-detail-section">
                <h4>Checklist</h4>
                <div class="checklist-item">
                    <label><input type="checkbox" checked> Tạo route API</label>
                </div>
                <div class="checklist-item">
                    <label><input type="checkbox"> Viết unit test</label>
                </div>
                <div class="checklist-item">
                    <label><input type="checkbox"> Đánh giá bảo mật</label>
                </div>
                <div class="checklist-item">
                    <label><input type="checkbox"> Cập nhật tài liệu</label>
                </div>
            </div>

            <div class="task-detail-section">
                <h4>Bình luận</h4>
                <textarea class="app-input task-textarea" rows="3" placeholder="Viết bình luận..."></textarea>
                <button class="app-btn app-btn-sm" type="button">Gửi bình luận</button>
            </div>
        </section>
    </div>
</div>
