<div class="offcanvas offcanvas-end" style="width: 500px; max-width: 100%;" tabindex="-1" id="taskDetailCanvas" aria-labelledby="taskDetailCanvasLabel">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary text-uppercase fs-6">WEB-3</span>
            <h5 class="offcanvas-title text-dark fw-bold mb-0" id="taskDetailCanvasLabel">Chi tiết công việc</h5>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column gap-4">
        
        <div>
            <h4 class="text-dark fw-bold mb-2">Establish responsive user dashboard statistics cards</h4>
            <label class="form-label text-muted fw-bold small text-uppercase">Mô tả công việc</label>
            <textarea class="form-content form-control border bg-light text-dark" rows="3" placeholder="Nhập mô tả chi tiết hoặc ghi chú nhanh về công việc tại đây..."></textarea>
        </div>

        <div class="card border bg-light p-3">
            <div class="row g-2 text-dark small">
                <div class="col-4 text-muted">Người thực hiện:</div>
                <div class="col-8 fw-bold">ADMIN (Bạn)</div>
                <div class="col-4 text-muted">Trạng thái:</div>
                <div class="col-8"><span class="badge bg-primary">In Progress</span></div>
                <div class="col-4 text-muted">Độ ưu tiên:</div>
                <div class="col-8"><span class="badge bg-danger">Highest</span></div>
            </div>
        </div>

        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label text-muted fw-bold small text-uppercase mb-0">Danh sách kiểm tra (Checklist)</label>
                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0">+ Thêm việc</button>
            </div>
            <div class="d-flex flex-column gap-2 text-dark">
                <div class="form-check d-flex align-items-center border p-2 rounded bg-white shadow-xs">
                    <input class="form-check-input ms-1 me-2" type="checkbox" id="check1" checked>
                    <label class="form-check-label small text-decoration-line-through text-muted" for="check1">Thiết kế cấu trúc HTML tĩnh cho Dashboard</label>
                </div>
                <div class="form-check d-flex align-items-center border p-2 rounded bg-white shadow-xs">
                    <input class="form-check-input ms-1 me-2" type="checkbox" id="check2">
                    <label class="form-check-label small" for="check2">Tối ưu hóa Responsive Media Queries màn hình Mobile</label>
                </div>
            </div>
        </div>

        <div class="mt-auto border-top pt-3">
            <label class="form-label text-muted fw-bold small text-uppercase">Bình luận trao đổi</label>
            
            <div class="comment-list d-flex flex-column gap-2 mb-3 max-vh-25 overflow-auto">
                <div class="bg-light p-2 rounded">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="small text-dark">Văn Quyết</strong>
                        <small class="text-muted" style="font-size: 0.75rem;">14:34</small>
                    </div>
                    <p class="mb-0 text-secondary small">Bảng kanban ông xong luôn chưa hay mới chỉnh vậy? 🤔</p>
                </div>
            </div>

            <div class="input-group">
                <input type="text" class="form-control text-dark border" placeholder="Viết bình luận của bạn...">
                <button class="btn btn-outline-primary" type="button"><i class="bi bi-send-fill"></i> Gửi</button>
            </div>
        </div>

    </div>
</div>



<div class="offcanvas offcanvas-end border-0 shadow-lg text-dark" tabindex="-1" id="taskDetailCanvas" aria-labelledby="taskDetailCanvasLabel" style="width: 550px; background-color: #ffffff;">
    <div class="offcanvas-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-layout-sidebar text-primary fs-5"></i>
            <span class="fw-bold text-muted text-uppercase" id="taskDetailCanvasLabel" style="font-size: 1rem;">WEB-1</span>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4 d-flex flex-column gap-4 style-scrollbar" style="overflow-y: auto;">
        <div>
            <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Tiêu đề Issue</label>
            <h5 class="fw-bold text-dark lh-base" style="font-size: 1.15rem;">Migrate active layouts to Tailwind v4 production framework</h5>
        </div>
        <div>
            <label class="form-label text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.7rem;">Mô tả chi tiết</label>
            <div class="bg-light rounded p-3 border border-secondary-subtle">
                <textarea class="form-control border-0 bg-transparent p-0 shadow-none text-dark style-scrollbar" rows="3" style="resize: none; font-size: 0.88rem;">Yêu cầu chuyển dịch sang tiện ích Tailwind v4 production framework.</textarea>
                <div class="d-flex justify-content-end gap-2 mt-2 pt-2 border-top border-secondary-subtle">
                    <button class="btn btn-sm btn-outline-secondary px-3 py-1">Hủy</button>
                    <button class="btn btn-sm btn-primary px-3 py-1 border-0" style="background-color: #4f46e5;">Lưu lại</button>
                </div>
            </div>
        </div>
        <div>
            <label class="form-label text-muted small fw-bold text-uppercase m-0 mb-2" style="font-size: 0.7rem;">Checklist</label>
            <div class="progress mb-3" style="height: 6px;"><div class="progress-bar bg-success rounded" style="width: 50%;"></div></div>
            <div class="d-flex flex-column gap-2">
                <div class="form-check d-flex align-items-center gap-1 bg-light p-2 rounded border border-start border-3 border-start-success m-0">
                    <input class="form-check-input ms-1 mt-0 shadow-none" type="checkbox" id="chkItem1" checked>
                    <label class="form-check-label text-secondary small text-decoration-line-through ps-1" for="chkItem1">Kiểm tra tương thích</label>
                </div>
                <div class="form-check d-flex align-items-center gap-1 bg-light p-2 rounded border border-start border-3 border-start-warning m-0">
                    <input class="form-check-input ms-1 mt-0 shadow-none" type="checkbox" id="chkItem2">
                    <label class="form-check-label text-dark small ps-1" for="chkItem2">Viết file build cấu hình</label>
                </div>
            </div>
        </div>
    </div>
</div>