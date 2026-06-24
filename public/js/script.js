// File script.js - Mã xử lý JavaScript tương tác động hệ thống TaskSync
document.addEventListener("DOMContentLoaded", function () {
  // Hàm helper lấy url
  const getBaseUrl = () => {
    //  Ưu tiên lấy biến baseUrl đã được PHP định nghĩa sẵn từ file layout.php
    if (window.baseUrl) {
      return window.baseUrl;
    }

    // Dự phòng
    const pathParts = window.location.pathname.split("/");
    if (pathParts[1] === "TaskSync") {
      return window.location.origin + "/TaskSync/public"; // Thêm /public vào đây
    }
    return window.location.origin;
  };
  const baseUrl = getBaseUrl();

  // ==============================================================
  // 1. TÍCH HỢP CHART.JS TRÊN TRANG DASHBOARD
  // ==============================================================
  const taskFrequencyCtx = document.getElementById("taskFrequencyChart");
  const newUsersCtx = document.getElementById("newUsersChart");

  if (taskFrequencyCtx) {
    // Dữ liệu từ PHP gán qua biến toàn cục window
    const rawData = window.taskFrequencyData || [];

    // Chuẩn bị nhãn (Tên nhân sự) và số liệu (Số lượng task xử lý)
    const labels = rawData.map((item) => item.member_name || "Chưa gán");
    const dataValues = rawData.map((item) => parseInt(item.task_count) || 0);

    new Chart(taskFrequencyCtx, {
      type: "bar",
      data: {
        labels: labels.length > 0 ? labels : ["Không có dữ liệu"],
        datasets: [
          {
            label: "Số lượng Task đang xử lý",
            data: dataValues.length > 0 ? dataValues : [0],
            backgroundColor: "rgba(99, 102, 241, 0.75)", // Màu Indigo hiện đại
            borderColor: "rgb(99, 102, 241)",
            borderWidth: 1.5,
            borderRadius: 6,
            hoverBackgroundColor: "rgba(79, 70, 229, 0.9)",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            padding: 12,
            backgroundColor: "#1e1b4b",
            titleColor: "#fff",
            bodyColor: "#e2e8f0",
            cornerRadius: 8,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: "rgba(148, 163, 184, 0.1)" },
            ticks: { stepSize: 1, color: "#64748b" },
          },
          x: {
            grid: { display: false },
            ticks: { color: "#64748b", font: { weight: "600" } },
          },
        },
      },
    });
  }

  if (newUsersCtx) {
    // Dữ liệu đăng ký từ PHP
    const rawData = window.newUsersStatsData || [];

    // Chuẩn bị nhãn (Ngày đăng ký) và số liệu (Số lượng user mới)
    let labels = rawData.map((item) => item.reg_date);
    let dataValues = rawData.map((item) => parseInt(item.user_count) || 0);

    // Tạo dữ liệu mượt mà nếu chỉ có 1 điểm dữ liệu (nhằm vẽ biểu đồ đường đẹp hơn)
    if (labels.length === 1) {
      labels = ["2026-06-20", "2026-06-21", labels[0]];
      dataValues = [1, 3, dataValues[0]];
    }

    new Chart(newUsersCtx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Người dùng mới",
            data: dataValues,
            fill: true,
            backgroundColor: "rgba(168, 85, 247, 0.15)", // Màu Tím mịn màng
            borderColor: "rgb(168, 85, 247)",
            borderWidth: 3,
            pointBackgroundColor: "rgb(168, 85, 247)",
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8,
            tension: 0.4, // Bo góc mềm mại
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            padding: 12,
            backgroundColor: "#1e1b4b",
            titleColor: "#fff",
            bodyColor: "#e2e8f0",
            cornerRadius: 8,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: "rgba(148, 163, 184, 0.1)" },
            ticks: { stepSize: 1, color: "#64748b" },
          },
          x: {
            grid: { color: "rgba(148, 163, 184, 0.05)" },
            ticks: { color: "#64748b" },
          },
        },
      },
    });
  }

  // ==============================================================
  // 2. KÉO THẢ KANBAN NATIVE HTML5 & CẬP NHẬT FETCH API
  // ==============================================================
  const kanbanBoard = document.querySelector(".kanban-board-wrapper");

  if (kanbanBoard) {
    // Khởi tạo các sự kiện drag & drop cho các cards hiện tại
    const cards = document.querySelectorAll(".kanban-item-card");
    const columns = document.querySelectorAll(".sub-kanban-column");

    cards.forEach((card) => {
      // Dragstart: lưu ID của thẻ
      card.addEventListener("dragstart", function (e) {
        card.classList.add("dragging");
        e.dataTransfer.setData("text/plain", card.getAttribute("data-id"));
        e.dataTransfer.effectAllowed = "move";
      });

      // Dragend: xóa lớp mờ
      card.addEventListener("dragend", function () {
        card.classList.remove("dragging");
      });
    });

    columns.forEach((column) => {
      // Dragover: thay đổi giao diện vùng thả
      column.addEventListener("dragover", function (e) {
        e.preventDefault();
        column.classList.add("drag-over");
      });

      // Dragleave: trả lại giao diện ban đầu
      column.addEventListener("dragleave", function () {
        column.classList.remove("drag-over");
      });

      // Drop: bắt ID và di chuyển thẻ HTML, gọi Fetch API ngầm
      column.addEventListener("drop", function (e) {
        e.preventDefault();
        column.classList.remove("drag-over");

        const taskId = e.dataTransfer.getData("text/plain");
        const card = document.querySelector(
          `.kanban-item-card[data-id="${taskId}"]`,
        );
        const newStatus = column.getAttribute("data-status");

        if (card && newStatus) {
          // Đưa thẻ HTML vào cột mới
          column.appendChild(card);

          // Cập nhật giao diện của thẻ
          updateQuickActionsMenu(card, newStatus);
          toggleDoneStyles(card, newStatus);
          updateColumnCounts();

          // Gọi Fetch API cập nhật lên server
          updateTaskStatus(taskId, newStatus);
        }
      });
    });
  }

  // Gửi yêu cầu cập nhật trạng thái Task lên server (Fetch API)
  function updateTaskStatus(taskId, status) {
    fetch(`${baseUrl}/task/updateStatus`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        task_id: taskId,
        status: status,
      }),
    })
      .then((response) => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          console.log(
            `[TaskSync] Đã cập nhật thành công Task ${taskId} sang ${status}`,
          );
        } else {
          console.error("[TaskSync] Lỗi cập nhật:", data.error);
          alert("Không thể lưu trạng thái mới, vui lòng thử lại.");
          location.reload();
        }
      })
      .catch((error) => {
        console.error("[TaskSync] Fetch error:", error);
        alert("Lỗi kết nối mạng, đang khôi phục giao diện...");
        location.reload();
      });
  }

  // Di chuyển Task bằng click nút chuyển nhanh
  window.moveTask = function (buttonElement, targetStatus) {
    const card = buttonElement.closest(".kanban-item-card");
    const taskId = card.getAttribute("data-id");
    const targetColumn = document.querySelector(
      `.sub-kanban-column[data-status="${targetStatus}"]`,
    );

    if (card && targetColumn) {
      targetColumn.appendChild(card);
      updateQuickActionsMenu(card, targetStatus);
      toggleDoneStyles(card, targetStatus);
      updateColumnCounts();

      // Gọi Fetch API cập nhật lên server
      updateTaskStatus(taskId, targetStatus);
    }
  };

  // Hàm cập nhật menu chuyển cột nhanh
  function updateQuickActionsMenu(card, currentStatus) {
    const container = card.querySelector(".quick-actions-container");
    if (!container) return;

    const actions = {
      todo: { label: "To D", icon: "bi-arrow-right" },
      in_progress: { label: "In P", icon: "bi-arrow-right" },
      in_review: { label: "In R", icon: "bi-arrow-right" },
      done: { label: "Done", icon: "bi-arrow-right" },
    };

    let newHtml = "";
    for (const [statusKey, action] of Object.entries(actions)) {
      if (statusKey !== currentStatus) {
        newHtml += `<span class="text-muted fw-medium quick-action-btn" style="font-size: 0.65rem; cursor: pointer;" onclick="event.stopPropagation(); moveTask(this, '${statusKey}')"><i class="bi ${action.icon}"></i> ${action.label}</span>`;
      }
    }
    container.innerHTML = newHtml;
  }

  // Hàm bật/tắt CSS cho cột DONE (gạch ngang, làm mờ)
  function toggleDoneStyles(card, status) {
    const titleEl = card.querySelector(".task-title");
    const codeEl = card.querySelector(".task-code");
    const assigneeEl = card.querySelector(".task-assignee");

    if (status === "done") {
      card.classList.add("opacity-75");
      if (titleEl) {
        titleEl.classList.add("text-secondary", "text-decoration-line-through");
        titleEl.classList.remove("text-dark");
      }
      if (codeEl) codeEl.classList.add("text-decoration-line-through");
      if (assigneeEl) assigneeEl.classList.add("text-decoration-line-through");
    } else {
      card.classList.remove("opacity-75");
      if (titleEl) {
        titleEl.classList.remove(
          "text-secondary",
          "text-decoration-line-through",
        );
        titleEl.classList.add("text-dark");
      }
      if (codeEl) codeEl.classList.remove("text-decoration-line-through");
      if (assigneeEl)
        assigneeEl.classList.remove("text-decoration-line-through");
    }
  }

  // Hàm đếm số lượng thẻ hiển thị trong mỗi cột
  function updateColumnCounts() {
    const kanbanColumns = document.querySelectorAll(".sub-kanban-column");
    kanbanColumns.forEach((column) => {
      const visibleCards = Array.from(
        column.querySelectorAll(".kanban-item-card"),
      ).filter((card) => card.style.display !== "none");
      const headerDiv = column.previousElementSibling;
      if (headerDiv) {
        const countSpan = headerDiv.querySelector(".count-badge");
        if (countSpan) countSpan.innerText = visibleCards.length;
      }
    });
  }

  // ==============================================================
  // 3. CLICK THẺ TASK - FETCH API - HIỂN THỊ OFFCANVAS MODAL
  // ==============================================================
  if (kanbanBoard) {
    // Lắng nghe sự kiện click thẻ Card (sử dụng Event Delegation)
    document.addEventListener("click", function (e) {
      const card = e.target.closest(".kanban-item-card");
      if (!card) return;

      // Nếu click trúng nút chuyển cột nhanh thì bỏ qua không mở Modal
      if (e.target.closest(".quick-action-btn")) return;

      const taskId = card.getAttribute("data-id");
      openTaskDetailModal(taskId, card);
    });
  }

  function openTaskDetailModal(taskId, card) {
    const modalElement = document.getElementById("taskDetailModal");
    if (!modalElement) return;

    // Khóa card đang tương tác vào window
    window.targetKanbanCard = card;

    // Lấy tất cả các selector form đúng chuẩn của Quyền
    const titleTextarea = document.getElementById("modalTaskTitle");
    const statusSelect = document.getElementById("modalStatusSelect");
    const descTextarea = modalElement.querySelector(
      ".mb-4 textarea:not(#modalTaskTitle)",
    );
    const assigneeSelect = modalElement.querySelector(
      "select:not(#modalStatusSelect)",
    );
    const githubInput =
      modalElement.querySelector(
        'input[type="text"][placeholder*="github" i]',
      ) || modalElement.querySelector('.col-lg-4 input[type="text"]');
    const projectHeader = modalElement.querySelector(
      ".modal-header .text-muted",
    );
    const subtaskList = modalElement.querySelector(".subtask-list");
    const subtaskBadgeCount = document.getElementById("subtaskBadgeCount");
    const subtaskProgressBar = document.getElementById("subtaskProgressBar");

    // Reset dữ liệu cũ để tạo hiệu ứng chuyển tiếp mượt mà
    if (titleTextarea) titleTextarea.value = "Đang tải...";
    if (descTextarea) descTextarea.value = "Đang tải mô tả...";
    if (githubInput) githubInput.value = "";
    if (subtaskList)
      subtaskList.innerHTML =
        '<div class="text-center py-2"><span class="spinner-border spinner-border-sm text-secondary" role="status"></span></div>';
    if (subtaskBadgeCount) subtaskBadgeCount.innerText = "0 / 0";
    if (subtaskProgressBar) subtaskProgressBar.style.width = "0%";

    // Khởi tạo và hiển thị Modal
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();

    // Thực thi Fetch API lấy JSON chi tiết của task
    fetch(`${baseUrl}/task/detail/${taskId}`, {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
      .then((response) => {
        if (!response.ok) throw new Error("Không thể tải chi tiết công việc");
        return response.json();
      })
      .then((task) => {
        // Điền dữ liệu động vào đúng chuẩn form có sẵn của Quyền
        if (projectHeader) {
          projectHeader.innerHTML = `DỰ ÁN / <span class="text-dark fw-bold">${task.project_key || "WEB"} / ${task.issue_key}</span>`;
        }
        if (titleTextarea) titleTextarea.value = task.title || "";
        if (descTextarea) descTextarea.value = task.description || "";
        if (statusSelect) {
          statusSelect.value = task.status;
          statusSelect.setAttribute("data-task-id", task.id);
        }
        if (assigneeSelect) {
          // Gán trực tiếp bằng ID (số nguyên) của người dùng được lưu trong Database (ví dụ: 1, 2)
          // Nếu công việc chưa được gán cho ai, tự động chọn option đầu tiên (Unassigned)
          assigneeSelect.value = task.assignee_id || "";
        }
        if (githubInput) {
          githubInput.value = task.github_branch_url || "";
        }

        // Nạp checklist subtasks
        let subtasksHtml = "";
        let completedCount = 0;

        if (task.subtasks && task.subtasks.length > 0) {
          task.subtasks.forEach((sub) => {
            const isDone = sub.status === "done";
            if (isDone) completedCount++;

            subtasksHtml += `
                        <div class="d-flex align-items-center mb-2">
                            <input class="form-check-input me-2 shadow-none" type="checkbox" ${isDone ? "checked" : ""} disabled>
                            <span class="${isDone ? "text-secondary text-decoration-line-through" : "text-dark fw-medium"}" style="font-size: 0.9rem;">${sub.title}</span>
                        </div>
                    `;
          });

          // Cập nhật badge và thanh tiến trình
          if (subtaskBadgeCount)
            subtaskBadgeCount.innerText = `${completedCount} / ${task.subtasks.length}`;
          if (subtaskProgressBar)
            subtaskProgressBar.style.width = `${(completedCount / task.subtasks.length) * 100}%`;
        } else {
          subtasksHtml =
            '<div class="text-muted small py-1">Không có sub-task nào.</div>';
          if (subtaskBadgeCount) subtaskBadgeCount.innerText = "0 / 0";
          if (subtaskProgressBar) subtaskProgressBar.style.width = "0%";
        }
        if (subtaskList) subtaskList.innerHTML = subtasksHtml;
      })
      .catch((err) => {
        console.error("[TaskSync] Error loading details:", err);
        if (titleTextarea) titleTextarea.value = "Lỗi tải thông tin!";
        if (descTextarea)
          descTextarea.value = "Không thể kết nối đến server để lấy mô tả.";
        if (subtaskList)
          subtaskList.innerHTML =
            '<div class="text-danger small py-1">Lỗi tải danh sách sub-tasks.</div>';
      });
  }

  // Lắng nghe sự kiện thay đổi Trạng thái (Status) ngay trong Modal
  document.addEventListener("change", function (e) {
    if (e.target.id === "modalStatusSelect") {
      const newStatus = e.target.value;
      const taskId = e.target.getAttribute("data-task-id");
      const card = window.targetKanbanCard;
      const destColumn = document.querySelector(
        `.sub-kanban-column[data-status="${newStatus}"]`,
      );

      if (card && destColumn) {
        // Di chuyển thẻ Card trên Kanban board
        destColumn.appendChild(card);
        updateQuickActionsMenu(card, newStatus);
        toggleDoneStyles(card, newStatus);
        updateColumnCounts();

        // Đồng bộ thay đổi lên máy chủ
        updateTaskStatus(taskId, newStatus);
      }
    }
  });
  // ==============================================================
  // 4. LẮNG NGHE SỰ KIỆN THAY ĐỔI NGƯỜI GÁN TRONG MODAL & LƯU DB
  // ==============================================================
  document.addEventListener("change", function (e) {
    const assigneeSelect = document.querySelector(
      "#taskDetailModal select:not(#modalStatusSelect)",
    );
    if (e.target === assigneeSelect) {
      const newAssigneeId = e.target.value;
      const statusSelect = document.getElementById("modalStatusSelect");
      const taskId = statusSelect
        ? statusSelect.getAttribute("data-task-id")
        : null;
      const card = window.targetKanbanCard;

      if (taskId) {
        // Gửi Fetch API lưu thay đổi xuống Database
        updateTaskAssignee(taskId, newAssigneeId, card, e.target);
      }
    }
  });

  // Hàm gửi API cập nhật Người gán (Assignee ID) lên Server
  function updateTaskAssignee(taskId, assigneeId, cardElement, selectElement) {
    fetch(`${baseUrl}/task/updateAssignee`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        task_id: taskId,
        assignee_id: assigneeId || null, // Nếu rỗng thì truyền null (Unassigned)
      }),
    })
      .then((response) => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          console.log(
            `[TaskSync] Cập nhật thành công Assignee cho Task ${taskId}`,
          );

          // Cập nhật lại giao diện
          if (cardElement) {
            const assigneeNameEl = cardElement.querySelector(".task-assignee");
            const avatarImgEl = cardElement.querySelector("img");

            // Lấy văn bản đang hiển thị của Option được chọn
            const selectedOptionText =
              selectElement.options[selectElement.selectedIndex].text;
            const cleanedName = selectedOptionText.split(" (")[0]; // Cắt bỏ phần thông tin vai trò phía sau

            if (assigneeNameEl) {
              assigneeNameEl.textContent = assigneeId
                ? cleanedName
                : "Unassigned";
            }
            if (avatarImgEl) {
              const avatarName = assigneeId ? cleanedName : "Unassigned";
              avatarImgEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(avatarName)}&background=64748b&color=fff`;
            }

            // Đồng bộ dữ liệu thuộc tính để bộ lọc hoạt động chính xác
            cardElement.setAttribute(
              "data-assignee",
              assigneeId ? cleanedName : "Unassigned",
            );
          }
        } else {
          alert("Không thể lưu người thực hiện mới, vui lòng thử lại.");
          location.reload();
        }
      })
      .catch((error) => {
        console.error("[TaskSync] Fetch error:", error);
        alert("Lỗi kết nối mạng, đang khôi phục giao diện...");
        location.reload();
      });
  }

  // ==============================================================
  // 5. BỘ LỌC THÔNG TIN (FILTER) TRÊN BẢNG KANBAN
  // ==============================================================
  const filterAssignee = document.getElementById("filterAssignee");
  const filterPriority = document.getElementById("filterPriority");
  const filterType = document.getElementById("filterType");

  function applyKanbanFilters() {
    const selectedAssignee = filterAssignee ? filterAssignee.value : "all";
    const selectedPriority = filterPriority ? filterPriority.value : "all";
    const selectedType = filterType ? filterType.value : "all";
    const kanbanCards = document.querySelectorAll(".kanban-item-card");

    kanbanCards.forEach((card) => {
      const cardAssignee = card.getAttribute("data-assignee") || "Unassigned";
      const cardPriority = card.getAttribute("data-priority") || "";
      const cardType = card.getAttribute("data-type") || "";

      // Kiểm tra điều kiện so khớp
      const matchAssignee =
        selectedAssignee === "all" || cardAssignee === selectedAssignee;
      const matchPriority =
        selectedPriority === "all" || cardPriority === selectedPriority;
      const matchType = selectedType === "all" || cardType === selectedType;

      if (matchAssignee && matchPriority && matchType) {
        card.style.display = "block";
        card.classList.remove("d-none");
      } else {
        card.style.display = "none";
        card.classList.add("d-none");
      }
    });

    // Cập nhật lại số đếm ở đầu mỗi cột sau khi lọc
    updateColumnCounts();
  }

  // Đăng ký sự kiện lắng nghe bộ lọc thay đổi
  if (filterAssignee)
    filterAssignee.addEventListener("change", applyKanbanFilters);
  if (filterPriority)
    filterPriority.addEventListener("change", applyKanbanFilters);
  if (filterType) filterType.addEventListener("change", applyKanbanFilters);
});
