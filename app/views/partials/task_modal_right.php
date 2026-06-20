<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-2">
                <div class="text-muted fw-medium small">DỰ ÁN / <span class="text-dark fw-bold">WEB-V2</span></div>
                <button type="button" class="btn text-secondary fw-semibold seamless-input w-auto" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Close
                </button>
            </div>
            <div class="modal-body p-4 pt-2">
                <div class="row g-5">
                    
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <textarea id="modalTaskTitle" class="form-control fw-bold text-dark seamless-input" rows="2" style="font-size: 1.4rem; resize: none;" placeholder="Nhập tên công việc..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted fw-bold small mb-2 ps-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">MÔ TẢ CÔNG VIỆC</label>
                            <div class="border border-secondary-subtle rounded-3 bg-white">
                                <div class="bg-light border-bottom border-secondary-subtle px-2 py-1 d-flex gap-1 editor-toolbar">
                                    <span class="tool-btn fw-bold">B</span><span class="tool-btn fst-italic">I</span><span class="tool-btn text-decoration-underline">U</span>
                                </div>
                                <textarea class="form-control border-0 shadow-none p-3" rows="4" placeholder="Thêm mô tả chi tiết công việc ở đây..."></textarea>
                            </div>
                        </div>

                        <div class="mb-4" id="subtaskSection">
                            <div class="d-flex align-items-center mb-2 gap-3 ps-1">
                                <label class="text-muted fw-bold small mb-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">SUB-TASKS CHECKLIST</label>
                                <span class="badge rounded-pill text-secondary border bg-white" id="subtaskBadgeCount">0 / 0</span>
                            </div>
                            <div class="progress mb-3 rounded-pill" style="height: 6px; background-color: #e2e8f0;">
                                <div class="progress-bar rounded-pill bg-primary transition-all" id="subtaskProgressBar" style="width: 0%;"></div>
                            </div>
                            
                            <div class="subtask-list"></div>

                            <div class="mt-2 input-group">
                                <input type="text" id="newSubtaskInput" class="form-control seamless-input py-2" style="border: 1px solid #cbd5e1 !important;" placeholder="Thêm sub-task mới và bấm Enter...">
                                <button id="addSubtaskBtn" class="btn btn-light border text-secondary fw-semibold px-3">+ Thêm</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-light rounded-4 p-4 border border-secondary-subtle h-100">
                            <div class="mb-4">
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">TRẠNG THÁI (STATUS)</label>
                                <select id="modalStatusSelect" class="form-select seamless-input text-dark fw-semibold" style="background-color: #fff; border: 1px solid #cbd5e1 !important;">
                                    <option value="todo">To Do</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="in_review">In Review</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">NGƯỜI XỬ LÝ (ASSIGNEE)</label>
                                <select class="form-select seamless-input text-dark fw-medium" style="background-color: #fff; border: 1px solid #cbd5e1 !important;">
                                    <option>Alex (AL)</option>
                                    <option>Sarah (SA)</option>
                                    <option>Quyen (QU)</option>
                                    <option>Marcus (MA)</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">GITHUB BRANCH / URL</label>
                                <input type="text" class="form-control seamless-input font-monospace text-dark" style="background-color: #fff; border: 1px solid #cbd5e1 !important;" placeholder="https://github.com/.../pull/1">
                            </div>
                            
                            <hr class="border-secondary-subtle my-4 opacity-50">
                            <button class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2" style="background-color: #fff1f2; color: #e11d48; border-radius: 8px;">
                                <i class="bi bi-trash3"></i> XÓA CÔNG VIỆC
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
