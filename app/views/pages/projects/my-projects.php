<!-- <div class="bg-dark text-white p-3 rounded mb-4" style="font-family: monospace; font-size: 0.85rem; z-index: 9999;">
    <h5>[DEBUG AREA] KIỂM TRA DỮ LIỆU ĐANG CHẠY:</h5>
    <hr class="border-secondary">
    <strong>1. ID User trong Session hiện tại:</strong> 
    <span class="text-warning"><?= $_SESSION['user']['id'] ?? 'TRỐNG!' ?></span>
    <br><br>
    <strong>2. Mảng $projects nhận từ Controller gửi sang View:</strong>
    <pre class="text-info"><?php print_r($projects); ?></pre>
</div> -->

<!-- PHẦN 1: Page Header -->
<div class="page-header d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light-subtle">
    <div>
        <h2 class="fw-bold m-0" style="color: #1e1b4b; font-size: 1.5rem;">Các dự án của bạn</h2>
        <p class="text-muted m-0 mt-1" style="font-size: 0.85rem;">
            Xem danh sách các dự án bạn đang tham gia.
        </p>
    </div>
    
    <!-- Nút chuyển thành nút mở MODAL của Bootstrap 5 -->
    <button type="button" class="btn text-white fw-semibold px-3 py-2 d-flex align-items-center" 
            data-bs-toggle="modal" data-bs-target="#createProjectModal"
            style="background-color: #4f46e5; border-radius: 8px; font-size: 0.9rem; border: none; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.15);">
        <span class="me-1">+</span> Tạo Dự án
    </button>
</div>

<!-- Hiển thị thông báo LỖI hoặc THÀNH CÔNG ngay trên trang Danh sách -->
<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger py-2 px-3 mb-4 fs-7 rounded-3" role="alert">
        <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success py-2 px-3 mb-4 fs-7 rounded-3" role="alert">
        <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
    </div>
<?php endif; ?>

<!-- PHẦN 2: Hiển thị lưới danh sách dự án -->
<div class="row">
    <?php if (!empty($projects)): ?>
        <?php foreach ($projects as $project): ?>
            <?php 
                $roleLabel = 'MEMBER';
                if (strtolower($project['role']) === 'manager') {
                    $roleLabel = 'ADMIN';
                } elseif (strtolower($project['role']) === 'viewer') {
                    $roleLabel = 'VIEWER';
                }
                $createdAt = date('Y-m-d', strtotime($project['created_at']));
            ?>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border border-light-subtle rounded-3" style="background-color: #ffffff;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-light text-secondary border px-2 py-1 font-monospace" style="font-size: 0.75rem;">
                                    <?= htmlspecialchars($project['key']) ?>
                                </span>
                                <span class="badge px-2 py-1 fw-bold" style="font-size: 0.65rem; <?= $project['role'] === 'manager' ? 'color: #4f46e5 !important; background-color: #e0e7ff !important;' : '' ?>">
                                    <?= $roleLabel ?>
                                </span>
                            </div>

                            <h5 class="card-title fw-bold mb-2" style="color: #111827; font-size: 1.05rem;">
                                <?= htmlspecialchars($project['name']) ?>
                            </h5>

                            <p class="card-text text-muted mb-4" style="font-size: 0.85rem; line-height: 1.5; min-height: 4.5rem;">
                                <?= htmlspecialchars($project['description'] ?? 'Không có mô tả cho dự án này.') ?>
                            </p>
                        </div>

                        <div>
                            <hr class="text-secondary opacity-25 my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    <?= $createdAt ?>
                                </span>
                                <a href="kanban/<?= $project['id'] ?>" class="text-decoration-none fw-semibold" style="color: #4f46e5; font-size: 0.85rem;">
                                    Vào dự án →
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="text-center py-5 bg-white border border-light-subtle rounded-3">
                <p class="text-muted mb-3">Bạn chưa tham gia hoặc sở hữu dự án nào.</p>
                <button type="button" class="btn text-white btn-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal" style="background-color: #4f46e5;">
                    Tạo dự án ngay
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- BOOTSTRAP 5 MODAL (Hộp thoại nổi chứa Form Tạo dự án) -->
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 620px;">
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background-color: #ffffff;">
            <div class="modal-body p-4">
                
                <!-- Header của Modal -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <span class="me-2 d-flex align-items-center" style="color: #4f46e5;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
                                <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2Zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-1h3.672Z"/>
                                <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5Z"/>
                            </svg>
                        </span>
                        <h5 class="modal-title fw-bold m-0" id="createProjectModalLabel" style="color: #1e1b4b; font-size: 1.15rem;">Tạo Dự án mới</h5>
                    </div>
                    <!-- Nút Đóng Modal của Bootstrap 5 -->
                    <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.85rem;"></button>
                </div>

                <!-- Form gửi dữ liệu sang hành động 'create' tương đối -->
                <form action="create" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                            Tên Dự án <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control py-2 px-3 border-secondary-subtle" placeholder="e.g. Website Redesign" required style="border-radius: 8px; font-size: 0.95rem;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                            Project Key <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="key" class="form-control py-2 px-3 border-secondary-subtle" placeholder="E.G. WEB, ANDROID, DESIGN" required maxlength="10" style="border-radius: 8px; font-size: 0.95rem; text-transform: uppercase;">
                        <div class="form-text text-muted mt-1" style="font-size: 0.75rem; line-height: 1.3;">
                            Mã định danh dự án, dùng làm tiền tố cho các issue (vd: WEB-1, WEB-2). Tối đa 10 ký tự.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                            Mô tả (Optional)
                        </label>
                        <textarea name="description" class="form-control py-2 px-3 border-secondary-subtle" rows="3" placeholder="Mô tả ngắn gọn về mục tiêu của dự án này..." style="border-radius: 8px; font-size: 0.95rem; resize: none;"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                            GitHub Repository (Optional)
                        </label>
                        <input type="url" name="github_repo_url" class="form-control py-2 px-3 border-secondary-subtle" placeholder="https://github.com/username/repo" style="border-radius: 8px; font-size: 0.95rem;">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn text-white px-4 py-2 fw-semibold" style="background-color: #4f46e5; border-radius: 8px; font-size: 0.95rem; border: none; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                            Tạo Dự án
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- Custom CSS sửa lỗi màu chữ ô input -->
<style>
    .modal-content .form-control {
        color: #1f2937 !important;
        background-color: #ffffff !important;
    }
    .form-control:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15) !important;
        color: #1f2937 !important;
    }
    .form-control::placeholder {
        color: #9ca3af;
        opacity: 0.8;
    }
</style>