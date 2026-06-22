<div class="container d-flex justify-content-center align-items-center py-5">
    <!-- Thẻ Card chính bo góc tròn, đổ bóng nhẹ giống thiết kế -->
    <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 620px; background-color: #ffffff;">
        <div class="card-body p-4">
            
            <!-- Header của Form -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <!-- Icon Folder màu tím -->
                    <span class="me-2 d-flex align-items-center" style="color: #4f46e5;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
                            <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2Zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-1h3.672Z"/>
                            <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5Z"/>
                        </svg>
                    </span>
                    <h5 class="card-title fw-bold m-0" style="color: #1e1b4b; font-size: 1.15rem;">Tạo Dự án mới</h5>
                </div>
                <!-- Nút Đóng trỏ về trang danh sách dự án của tôi -->
                <a href="myProjects" class="text-decoration-none text-muted" style="font-size: 0.95rem;">
                    <i class="bi bi-x-lg me-1"></i> Close
                </a>
            </div>

            <!-- Khu vực thông báo lỗi nếu có từ Controller gửi qua Session -->
            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger py-2 px-3 mb-3 fs-7 rounded-3" role="alert">
                    <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Form Nhập Liệu -->
            <form action="/project/create" method="POST">
                
                <!-- 1. Tên Dự án -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                        Tên Dự án <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           class="form-control py-2 px-3 border-secondary-subtle" 
                           placeholder="e.g. Website Redesign" 
                           required 
                           style="border-radius: 8px; font-size: 0.95rem;">
                </div>

                <!-- 2. Project Key -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                        Project Key <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="key" 
                           class="form-control py-2 px-3 border-secondary-subtle" 
                           placeholder="E.G. WEB, ANDROID, DESIGN" 
                           required 
                           maxlength="10"
                           style="border-radius: 8px; font-size: 0.95rem; text-transform: uppercase;">
                    <div class="form-text text-muted mt-1" style="font-size: 0.75rem; line-height: 1.3;">
                        Mã định danh dự án, dùng làm tiền tố cho các issue (vd: WEB-1, WEB-2). Tối đa 10 ký tự.
                    </div>
                </div>

                <!-- 3. Mô tả (Optional) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                        Mô tả (Optional)
                    </label>
                    <textarea name="description" 
                              class="form-control py-2 px-3 border-secondary-subtle" 
                              rows="3" 
                              placeholder="Mô tả ngắn gọn về mục tiêu của dự án này..."
                              style="border-radius: 8px; font-size: 0.95rem; resize: none;"></textarea>
                </div>

                <!-- 4. GitHub Repository (Optional) -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.9rem;">
                        GitHub Repository (Optional)
                    </label>
                    <input type="url" 
                           name="github_repo_url" 
                           class="form-control py-2 px-3 border-secondary-subtle" 
                           placeholder="https://github.com/username/repo" 
                           style="border-radius: 8px; font-size: 0.95rem;">
                </div>

                <!-- Nút Submit căn phải -->
                <div class="d-flex justify-content-end">
                    <button type="submit" 
                            class="btn text-white px-4 py-2 fw-semibold" 
                            style="background-color: #4f46e5; border-radius: 8px; font-size: 0.95rem; border: none; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                        Tạo Dự án
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Style CSS tùy biến nhỏ cho các ô input trông gọn và chuyên nghiệp giống ảnh mộc -->
<style>
    /* Ép chữ trong các ô input thuộc card này luôn có màu xám đậm và nền trắng */
    .card .form-control {
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