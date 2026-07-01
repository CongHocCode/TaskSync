<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-2 position-relative">
                <div class="text-muted fw-medium small">DỰ ÁN / <a href="#" class="text-dark fw-bold text-decoration-none" id="modalProjectName" style="transition: color 0.2s;" onmouseover="this.style.color='#4f46e5';" onmouseout="this.style.color='inherit';">WEB-V2</a></div>
                <button type="button" class="btn d-flex align-items-center justify-content-center p-0 rounded-circle border shadow-sm" data-bs-dismiss="modal" 
                        style="width: 36px; height: 36px; background-color: #fff; border-color: #cbd5e1 !important; transition: all 0.2s; position: absolute; right: 24px; top: 20px; z-index: 1051;"
                        onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.transform='scale(1.08)';" 
                        onmouseout="this.style.backgroundColor='#fff'; this.style.transform='scale(1)';"
                        title="Đóng chi tiết công việc">
                    <i class="bi bi-x-lg" style="color: #4b5563; font-size: 1.1rem; -webkit-text-stroke: 0.5px;"></i>
                </button>
            </div>
            <div class="modal-body p-4 pt-2">
                <div class="row g-5">

                    <div class="col-lg-8">
                        <div id="modalMotherTaskDisplay" class="mb-2 d-none">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.75rem;">
                                <i class="bi bi-diagram-3-fill me-1"></i> Mother Task: 
                                <a href="#" id="modalMotherTaskRef" class="text-primary text-decoration-none fw-bold"></a>
                            </span>
                        </div>
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

                            <div class="mt-2">
                                <button type="button" id="openCreateSubtaskModalBtn" class="btn btn-outline-primary btn-sm w-100 fw-bold border-dashed" style="border-style: dashed; border-width: 1.5px;">
                                    <i class="bi bi-plus-circle"></i> Tạo sub-task
                                </button>
                            </div>
                        </div>
                        <!-- BÌNH LUẬN TRAO ĐỔI -->
                        <hr class="border-secondary-subtle my-4 opacity-50">
                        <div class="mb-4">
                            <label class="text-muted fw-bold small mb-3 ps-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">BÌNH LUẬN TRAO ĐỔI</label>
                            
                            <!-- Danh sách bình luận -->
                            <div id="modalCommentsList" class="mb-3" style="max-height: 320px; overflow-y: auto; padding-right: 4px; display: flex; flex-direction: column; gap: 12px;">
                                <!-- Được nạp động từ JS -->
                            </div>

                            <!-- Nhập bình luận mới -->
                            <div class="d-flex gap-2 mt-3 align-items-center">
                                <textarea id="newCommentInput" class="form-control py-2 px-3 bg-white" rows="1" style="border: 1px solid #cbd5e1 !important; border-radius: 8px; resize: none;" placeholder="Viết bình luận hoặc trao đổi..."></textarea>
                                <button id="submitCommentBtn" class="btn btn-outline-primary fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 8px; height: 38px;">
                                    <i class="bi bi-send-fill"></i> <span>Gửi</span>
                                </button>
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
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">LOẠI CÔNG VIỆC (TYPE)</label>
                                <select id="modalTypeSelect" class="form-select seamless-input text-dark fw-semibold" style="background-color: #fff; border: 1px solid #cbd5e1 !important;">
                                    <option value="task">Task (Công việc thường)</option>
                                    <option value="bug">Bug (Sửa lỗi)</option>
                                    <option value="story">Story (Nghiệp vụ)</option>
                                    <option value="epic">Epic (Tính năng lớn)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">NGƯỜI XỬ LÝ (ASSIGNEE)</label>
                                <select id="modalAssigneeSelect" class="form-select border-0 bg-transparent fw-bold text-dark" style="box-shadow: none;">
                                    <option value="">Chưa phân công (Unassigned)</option>
                                    <!-- Do là một phần của trang kanban nên cũng sẽ có được ds members của kanban khi load -->
                                    <?php if (!empty($data['members'])): ?>
                                        <?php foreach ($data['members'] as $member):
                                            $fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
                                            $displayName = !empty($fullName) ? $fullName : $member['username'];

                                            // Tự động trích xuất 2 chữ cái đầu để hiển thị dạng viết tắt (ví dụ: Văn Nguyễn -> VN)
                                            $initials = !empty($member['first_name']) && !empty($member['last_name'])
                                                ? strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1))
                                                : strtoupper(substr($member['username'], 0, 2));
                                        ?>
                                            <!-- Gán value bằng ID số nguyên của user để khớp tuyệt đối với CSDL và JS -->
                                            <option value="<?= $member['id'] ?>">
                                                <?= htmlspecialchars($displayName) ?> (<?= $initials ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">NGÀY TẠO (CREATED AT)</label>
                                <input type="text" id="modalCreatedAtInput" class="form-control seamless-input text-dark fw-semibold bg-white border border-secondary-subtle" readonly style="border-color: #cbd5e1 !important; border-radius: 8px;" placeholder="Đang tải...">
                            </div>

                            <div class="mb-4">
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">HẠN HOÀN THÀNH (DUE DATE)</label>
                                <input type="datetime-local" id="modalDueDateInput" class="form-control seamless-input text-dark fw-semibold bg-white border border-secondary-subtle" style="border-color: #cbd5e1 !important; border-radius: 8px;">
                            </div>

                            <div class="mb-4">
                                <label class="text-muted fw-bold mb-1 ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">GITHUB BRANCH / URL</label>
                                <input type="text" class="form-control seamless-input font-monospace text-dark" style="background-color: #fff; border: 1px solid #cbd5e1 !important;" placeholder="https://github.com/.../pull/1">
                            </div>

                            <hr class="border-secondary-subtle my-4 opacity-50">
                            <button class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2" id="btnDeleteTask" style="background-color: #fff1f2; color: #e11d48; border-radius: 8px;">
                                <i class="bi bi-trash3"></i> XÓA CÔNG VIỆC
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>