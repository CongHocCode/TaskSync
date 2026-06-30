<div class="page-content">
    <div class="page-header">
        <h2>Chào mừng quay lại, ADMIN!</h2>
        <p>Giám sát nhanh các Task và Dự án của bạn hàng ngày.</p>
    </div>

    <!-- Hộp chứa biểu đồ trực quan -->
    <div class="dashboard-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        <div class="app-card">
            <h3 style="color: #1a1632 !important; font-weight: bold !important; margin-bottom: 15px;">Tần suất xử lý công việc</h3>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="taskFrequencyChart"></canvas>
            </div>
        </div>

        <div class="app-card">
            <h3 style="color: #1a1632 !important; font-weight: bold !important; margin-bottom: 15px;">Thống kê người dùng mới</h3>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="newUsersChart"></canvas>
            </div>
        </div>
    </div>

    <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
        <div class="app-card">
            <h3 style="color: #1a1632 !important; font-weight: bold !important;">Tổng quan Task</h3>
            <div class="issues-list">
                <div class="issue-item">
                    <div>
                        <strong>5</strong>
                        <div class="project-desc" style="color: #4b5563 !important;">Task đang mở</div>
                    </div>
                </div>
                <div class="issue-item">
                    <div>
                        <strong>2</strong>
                        <div class="project-desc" style="color: #4b5563 !important;">Task của tôi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card">
            <h3 style="color: #1a1632 !important; font-weight: bold !important;">Trạng thái dự án</h3>
            <div class="issues-list">
                <div class="issue-item">
                    <div>
                        <strong>3</strong>
                        <div class="project-desc" style="color: #4b5563 !important;">Dự án hoạt động</div>
                    </div>
                </div>
                <div class="issue-item">
                    <div>
                        <strong>1</strong>
                        <div class="project-desc" style="color: #4b5563 !important;">Dự án cần kiểm tra</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card">
            <h3 style="color: #1a1632 !important; font-weight: bold !important;">Thông báo nhanh</h3>
            <div class="issues-list">
                <div class="issue-item">
                    <span class="issue-title">WEB - Cập nhật trạng thái Kanban</span>
                </div>
                <div class="issue-item">
                    <span class="issue-title">AUTH - Kiểm tra bảo mật xác thực</span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="app-card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div>
                    <h3 style="color: #1a1632 !important; font-weight: bold !important; margin: 0;">My Assigned Issues</h3>
                    <p class="project-desc" style="color: #4b5563 !important; margin: 4px 0 0 0;">Các issue đang được giao cho bạn.</p>
                </div>
                <span class="badge">2</span>
            </div>
            <div class="issues-list">
                <div class="issue-item">
                    <span class="issue-id">WEB-3</span>
                    <span class="issue-title">Establish responsive user dashboard statistics cards</span>
                    <span class="priority-badge medium">Medium</span>
                </div>
                <div class="issue-item">
                    <span class="issue-id">AUTH-1</span>
                    <span class="issue-title">Architect multi-factor visual validation modal</span>
                    <span class="priority-badge highest">Highest</span>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div>
                    <h3 style="color: #1a1632 !important; font-weight: bold !important; margin: 0;">Danh sách Dự án</h3>
                    <p class="project-desc" style="color: #4b5563 !important; margin: 4px 0 0 0;">Các dự án quan trọng đang theo dõi.</p>
                </div>
                <span class="badge">3</span>
            </div>
            <div class="projects-list">
                <div class="project-item">
                    <span class="project-name">WEB</span>
                    <span class="project-desc">Frontend Reconstruction</span>
                    <span class="project-admin">ADMIN</span>
                </div>
                <div class="project-item">
                    <span class="project-name">API</span>
                    <span class="project-desc">Build task sync endpoints</span>
                    <span class="project-admin">ADMIN</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tải thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Gán dữ liệu PHP cho JavaScript -->
<script>
    window.taskFrequencyData = <?php echo json_encode($data['task_frequency'] ?? []); ?>;
    window.newUsersStatsData = <?php echo json_encode($data['new_users_stats'] ?? []); ?>;
</script>
