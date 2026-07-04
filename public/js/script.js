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

    // Tự động mở modal chi tiết nếu URL chứa ?open_task=ID
    const urlParams = new URLSearchParams(window.location.search);
    const autoOpenTaskId = urlParams.get("open_task");
    if (autoOpenTaskId) {
      const targetCard = document.querySelector(
        `.kanban-item-card[data-id="${autoOpenTaskId}"]`,
      );

      setTimeout(() => {
        openTaskDetailModal(autoOpenTaskId, targetCard || null);

        if (targetCard) {
          targetCard.scrollIntoView({ behavior: "smooth", block: "center" });
          targetCard.style.boxShadow = "0 0 0 3px rgba(79, 70, 229, 0.5)";
          setTimeout(() => {
            targetCard.style.boxShadow = "";
          }, 3000);
        }
      }, 300);

      const cleanUrl = window.location.pathname;
      window.history.replaceState({}, document.title, cleanUrl);
    }
  }

  function openTaskDetailModal(taskId, card) {
    const modalElement = document.getElementById("taskDetailModal");
    if (!modalElement) return;

    window.targetKanbanCard = card;

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
    const createdAtInput = document.getElementById("modalCreatedAtInput");
    const dueDateInput = document.getElementById("modalDueDateInput");

    if (titleTextarea) titleTextarea.value = "Đang tải...";
    if (descTextarea) descTextarea.value = "Đang tải mô tả...";
    if (githubInput) githubInput.value = "";
    if (subtaskList)
      subtaskList.innerHTML =
        '<div class="text-center py-2"><span class="spinner-border spinner-border-sm text-secondary" role="status"></span></div>';
    if (subtaskBadgeCount) subtaskBadgeCount.innerText = "0 / 0";
    if (subtaskProgressBar) subtaskProgressBar.style.width = "0%";

    fetch(`${baseUrl}/task/detail/${taskId}`, {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
      .then((response) => {
        if (!response.ok) throw new Error("Không thể tải chi tiết công việc");
        return response.json();
      })
      .then((task) => {
        if (projectHeader) {
          projectHeader.textContent = `${task.issue_key} / Chi tiết công việc`;
        }

        const projectNameEl = document.getElementById("modalProjectName");
        if (projectNameEl) {
          projectNameEl.textContent = task.project_name || "WEB";
          projectNameEl.href = `${baseUrl}/project/kanban/${task.project_id_ref || task.project_id}`;
        }

        const motherTaskDisplay = document.getElementById(
          "modalMotherTaskDisplay",
        );
        const motherTaskRef = document.getElementById("modalMotherTaskRef");
        if (motherTaskDisplay && motherTaskRef) {
          if (task.parent_issue_id && task.parent_issue_key) {
            motherTaskRef.textContent = `#${task.parent_issue_key} - ${task.parent_title}`;
            motherTaskRef.onclick = (e) => {
              e.preventDefault();
              openTaskDetailModal(task.parent_issue_id, null);
            };
            motherTaskDisplay.classList.remove("d-none");
          } else {
            motherTaskDisplay.classList.add("d-none");
          }
        }

        const commentInput = document.getElementById("newCommentInput");
        if (commentInput) commentInput.value = "";

        const commentsList = document.getElementById("modalCommentsList");
        if (commentsList) {
          commentsList.innerHTML = "";
          if (task.comments && task.comments.length > 0) {
            task.comments.forEach((c) => {
              commentsList.appendChild(createCommentElement(c));
            });
            setTimeout(() => {
              commentsList.scrollTop = commentsList.scrollHeight;
            }, 100);
          } else {
            commentsList.innerHTML = `<div class="text-muted small ps-1 py-2">Chưa có bình luận nào. Hãy trao đổi về công việc này!</div>`;
          }
        }

        const role = task.current_user_role || "viewer";
        const isMember = role === "member";
        const isViewer = role === "viewer";
        const isReadOnly = isMember || isViewer;

        const deleteBtn = document.getElementById("btnDeleteTask");
        if (deleteBtn) {
          if (isReadOnly) {
            deleteBtn.classList.add("d-none");
            deleteBtn.classList.remove("d-flex");
          } else {
            deleteBtn.classList.remove("d-none");
            deleteBtn.classList.add("d-flex");
          }
        }

        if (titleTextarea) {
          titleTextarea.value = task.title || "";
          titleTextarea.disabled = isReadOnly;
        }
        if (descTextarea) {
          descTextarea.value = task.description || "";
          descTextarea.disabled = isViewer;
        }
        if (statusSelect) {
          statusSelect.value = task.status;
          statusSelect.setAttribute("data-task-id", task.id);
          statusSelect.disabled = isViewer;
        }
        if (assigneeSelect && task.project_members) {
          assigneeSelect.innerHTML =
            '<option value="">Chưa phân công (Unassigned)</option>';
          task.project_members.forEach((member) => {
            const fullName = (
              (member.first_name || "") +
              " " +
              (member.last_name || "")
            ).trim();
            const displayName = fullName ? fullName : member.username;
            const initials =
              member.first_name && member.last_name
                ? (
                    member.first_name.charAt(0) + member.last_name.charAt(0)
                  ).toUpperCase()
                : member.username.substring(0, 2).toUpperCase();

            const option = document.createElement("option");
            option.value = member.id;
            option.textContent = `${displayName} (${initials})`;
            assigneeSelect.appendChild(option);
          });
        }
        if (assigneeSelect) {
          assigneeSelect.value = task.assignee_id || "";
          assigneeSelect.disabled = isReadOnly;
        }
        if (githubInput) {
          githubInput.value = task.github_branch_url || "";
          githubInput.disabled = !task.can_edit_github;
        }

        if (createdAtInput) {
          if (task.created_at) {
            const date = new Date(task.created_at);
            if (!isNaN(date.getTime())) {
              createdAtInput.value = date.toLocaleDateString("vi-VN", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
              });
            } else {
              createdAtInput.value = task.created_at;
            }
          } else {
            createdAtInput.value = "Chưa rõ";
          }
        }
        if (dueDateInput) {
          if (task.due_date) {
            let formattedVal = task.due_date.replace(" ", "T");
            if (formattedVal.length === 10) {
              formattedVal += "T00:00";
            } else if (formattedVal.length > 16) {
              formattedVal = formattedVal.substring(0, 16);
            }
            dueDateInput.value = formattedVal;
          } else {
            dueDateInput.value = "";
          }
          dueDateInput.disabled = isReadOnly;
        }

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

        const prContainer = document.getElementById("prStatusBadgeContainer");
        if (prContainer) {
          if (task.pr_status) {
            const pr = task.pr_status;
            if (pr.merged) {
              prContainer.innerHTML = `<span class="badge bg-success rounded-pill small"><i class="bi bi-git"></i> PR #${pr.number}: MERGED (Đã gộp code)</span>`;
            } else if (pr.state === "open") {
              prContainer.innerHTML = `<span class="badge bg-purple rounded-pill small" style="background-color: #7c3aed; color: #fff;"><i class="bi bi-git"></i> PR #${pr.number}: OPEN (Đang mở)</span>`;
            } else {
              prContainer.innerHTML = `<span class="badge bg-danger rounded-pill small"><i class="bi bi-git"></i> PR #${pr.number}: CLOSED (Đã đóng)</span>`;
            }
          } else {
            prContainer.innerHTML = "";
          }
        }

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();
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

  document.addEventListener("change", function (e) {
    if (e.target.id === "modalStatusSelect") {
      const newStatus = e.target.value;
      const taskId = e.target.getAttribute("data-task-id");
      const card = window.targetKanbanCard;
      const destColumn = document.querySelector(
        `.sub-kanban-column[data-status="${newStatus}"]`,
      );

      updateTaskStatus(taskId, newStatus);

      if (card && destColumn) {
        destColumn.appendChild(card);
        updateQuickActionsMenu(card, newStatus);
        toggleDoneStyles(card, newStatus);
        updateColumnCounts();
      } else {
        const taskRow = document.querySelector(
          `.task-row[data-id="${taskId}"], .task-item[data-id="${taskId}"]`,
        );
        if (taskRow) {
          const statusPill = taskRow.querySelector(
            ".badge-status, .status-pill",
          );
          if (statusPill) {
            let bg = "#f1f5f9";
            let color = "#475569";
            let label = "TO DO";

            if (newStatus === "done") {
              bg = "#dcfce7";
              color = "#15803d";
              label = "DONE";
            } else if (newStatus === "in_progress") {
              bg = "#e0f2fe";
              color = "#0369a1";
              label = "IN PROGRESS";
            } else if (newStatus === "in_review") {
              bg = "#faf5ff";
              color = "#7e22ce";
              label = "IN REVIEW";
            }

            statusPill.style.backgroundColor = bg;
            statusPill.style.color = color;
            statusPill.innerText = label;
          }

          const titleEl = taskRow.querySelector(
            ".task-title-link, span.fw-semibold",
          );
          if (titleEl) {
            if (newStatus === "done") {
              titleEl.classList.add(
                "text-decoration-line-through",
                "text-muted",
              );
            } else {
              titleEl.classList.remove(
                "text-decoration-line-through",
                "text-muted",
              );
            }
          }

          if (newStatus === "done") {
            taskRow.classList.add("done-task", "opacity-75");
          } else {
            taskRow.classList.remove("done-task", "opacity-75");
          }
        }
      }
    }
  });
  document.addEventListener("change", function (e) {
    const assigneeSelect =
      document.getElementById("modalAssigneeSelect") ||
      document.querySelector("#taskDetailModal select:not(#modalStatusSelect)");
    if (e.target === assigneeSelect) {
      const newAssigneeId = e.target.value;
      const statusSelect = document.getElementById("modalStatusSelect");
      const taskId = statusSelect
        ? statusSelect.getAttribute("data-task-id")
        : null;
      const card = window.targetKanbanCard;

      if (taskId) {
        updateTaskAssignee(taskId, newAssigneeId, card, e.target);
      }
    }
  });

  function updateTaskAssignee(taskId, assigneeId, cardElement, selectElement) {
    fetch(`${baseUrl}/task/updateAssignee`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        task_id: taskId,
        assignee_id: assigneeId || null,
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

          if (cardElement) {
            const assigneeNameEl = cardElement.querySelector(".task-assignee");
            const avatarImgEl = cardElement.querySelector("img");

            const selectedOptionText =
              selectElement.options[selectElement.selectedIndex].text;
            const cleanedName = selectedOptionText.split(" (")[0];

            if (assigneeNameEl) {
              assigneeNameEl.textContent = assigneeId
                ? cleanedName
                : "Unassigned";
            }
            if (avatarImgEl) {
              const avatarName = assigneeId ? cleanedName : "Unassigned";
              avatarImgEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(avatarName)}&background=64748b&color=fff`;
            }

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

  document.addEventListener("change", function (e) {
    if (e.target.id === "modalDueDateInput") {
      const newDueDate = e.target.value;
      const statusSelect = document.getElementById("modalStatusSelect");
      const taskId = statusSelect
        ? statusSelect.getAttribute("data-task-id")
        : null;

      if (taskId) {
        fetch(`${baseUrl}/task/updateDueDate`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            task_id: taskId,
            due_date: newDueDate ? newDueDate.replace("T", " ") : null,
          }),
        })
          .then((response) => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              console.log(
                `[TaskSync] Cập nhật thành công Hạn hoàn thành cho Task ${taskId}`,
              );

              const taskRow = document.querySelector(
                `.task-row[data-id="${taskId}"], .task-item[data-id="${taskId}"]`,
              );
              if (taskRow) {
                const dueEl = taskRow.querySelector(
                  "td.text-muted.small:last-child, span.small.due-normal, span.small.due-soon, span.small.due-overdue",
                );
                if (dueEl) {
                  if (newDueDate) {
                    const parts = newDueDate.split("T");
                    const datePart = parts[0];
                    const timePart = parts[1] || "";
                    const dateParts = datePart.split("-");
                    let formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                    if (timePart) {
                      formattedDate += ` ${timePart}`;
                    }

                    if (taskRow.classList.contains("task-item")) {
                      dueEl.innerHTML = `<i class="bi bi-calendar3 me-1"></i>${formattedDate}`;

                      const dueTs = new Date(newDueDate).getTime();
                      const now = Date.now();
                      dueEl.className = "small";
                      if (dueTs < now) {
                        dueEl.classList.add("due-overdue");
                      } else if (dueTs - now < 86400000 * 3) {
                        dueEl.classList.add("due-soon");
                      } else {
                        dueEl.classList.add("due-normal");
                      }
                    } else {
                      dueEl.textContent = formattedDate;
                    }
                  } else {
                    dueEl.innerHTML = taskRow.classList.contains("task-item")
                      ? '<i class="bi bi-calendar3 me-1"></i>Không có hạn'
                      : '<span class="text-light-emphasis">-</span>';
                    if (taskRow.classList.contains("task-item")) {
                      dueEl.className = "small due-normal";
                    }
                  }
                }
              }
            } else {
              alert("Không thể lưu hạn hoàn thành mới, vui lòng thử lại.");
            }
          })
          .catch((error) => {
            console.error("[TaskSync] Update due date error:", error);
            alert("Lỗi kết nối mạng khi cập nhật hạn hoàn thành.");
          });
      }
    }
  });

  document.addEventListener("change", function (e) {
    if (e.target.id === "githubBranchUrl") {
      const newUrl = e.target.value.trim();

      const statusSelect = document.getElementById("modalStatusSelect");
      const taskId = statusSelect
        ? statusSelect.getAttribute("data-task-id")
        : null;

      if (taskId) {
        fetch(`${baseUrl}/task/updateBranchUrl`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            task_id: taskId,
            github_branch_url: newUrl,
          }),
        })
          .then((response) => {
            if (!response.ok) throw new Error("Cập nhật link GitHub thất bại");
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              console.log(
                `[TaskSync] Đã cập nhật thành công GitHub URL cho Task ${taskId}`,
              );
            } else {
              alert("Không thể lưu liên kết GitHub, vui lòng thử lại.");
            }
          })
          .catch((error) => {
            console.error("[TaskSync] Update GitHub URL error:", error);
            alert("Lỗi kết nối mạng khi lưu liên kết GitHub.");
          });
      }
    }
  });

  document.addEventListener("click", function (e) {
    if (
      e.target.id === "openCreateSubtaskModalBtn" ||
      e.target.closest("#openCreateSubtaskModalBtn")
    ) {
      const statusSelect = document.getElementById("modalStatusSelect");
      const taskId = statusSelect
        ? statusSelect.getAttribute("data-task-id")
        : null;
      const titleTextarea = document.getElementById("modalTaskTitle");
      const taskTitle = titleTextarea ? titleTextarea.value : "Unknown Task";

      if (taskId) {
        const detailModalEl = document.getElementById("taskDetailModal");
        if (detailModalEl) {
          const detailModal =
            bootstrap.Modal.getInstance(detailModalEl) ||
            new bootstrap.Modal(detailModalEl);
          detailModal.hide();
        }

        setTimeout(() => {
          const parentInput = document.getElementById("parentIssueIdInput");
          const motherTaskInfo = document.getElementById("motherTaskInfo");
          const motherTaskName = document.getElementById("motherTaskName");

          if (parentInput) parentInput.value = taskId;
          if (motherTaskInfo) motherTaskInfo.classList.remove("d-none");
          if (motherTaskName)
            motherTaskName.innerHTML = `<span class="badge bg-secondary me-2">#${taskId}</span> <strong>${taskTitle}</strong>`;

          const taskData = window.currentActiveTaskData;
          if (taskData) {
            const projInput = document.getElementById("createIssueProjectId");
            if (projInput) projInput.value = taskData.project_id;

            const titleText = document.getElementById(
              "createIssueModalTitleText",
            );
            if (titleText)
              titleText.innerHTML = `Tạo Sub-task cho dự án: <span class="text-primary">${taskData.project_name || "Không rõ"}</span>`;

            const assigneeSelect = document.getElementById(
              "createIssueAssigneeSelect",
            );
            if (assigneeSelect && taskData.project_members) {
              assigneeSelect.innerHTML =
                '<option value="" selected>Chưa phân công (Unassigned)</option>';
              taskData.project_members.forEach((member) => {
                const fullName = (
                  (member.first_name || "") +
                  " " +
                  (member.last_name || "")
                ).trim();
                const displayName = fullName ? fullName : member.username;
                const option = document.createElement("option");
                option.value = member.id;
                option.textContent = `${displayName} (${member.role || "member"})`;
                assigneeSelect.appendChild(option);
              });
            }
          }

          const createModalEl = document.getElementById("createIssueModal");
          if (createModalEl) {
            const createModal =
              bootstrap.Modal.getOrCreateInstance(createModalEl);
            createModal.show();
          }
        }, 400);
      }
    }
  });

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

    updateColumnCounts();
  }

  if (filterAssignee)
    filterAssignee.addEventListener("change", applyKanbanFilters);
  if (filterPriority)
    filterPriority.addEventListener("change", applyKanbanFilters);
  if (filterType) filterType.addEventListener("change", applyKanbanFilters);

  const mobileToggleBtn = document.getElementById("mobile-sidebar-toggle");
  const mobileSidebar = document.querySelector(".app-sidebar");
  const mobileOverlay = document.getElementById("sidebar-overlay");

  if (mobileToggleBtn && mobileSidebar && mobileOverlay) {
    mobileToggleBtn.addEventListener("click", function () {
      mobileSidebar.classList.add("show-mobile");
      mobileOverlay.classList.add("show");
      document.body.style.overflow = "hidden";
    });

    mobileOverlay.addEventListener("click", function () {
      mobileSidebar.classList.remove("show-mobile");
      mobileOverlay.classList.remove("show");
      document.body.style.overflow = "";
    });

    const sidebarCloseBtn = mobileSidebar.querySelector(".sidebar-collapse");
    if (sidebarCloseBtn) {
      sidebarCloseBtn.addEventListener("click", function () {
        mobileSidebar.classList.remove("show-mobile");
        mobileOverlay.classList.remove("show");
        document.body.style.overflow = "";
      });
    }
  }

  const submitCommentBtn = document.getElementById("submitCommentBtn");
  const newCommentInput = document.getElementById("newCommentInput");

  if (submitCommentBtn && newCommentInput) {
    submitCommentBtn.addEventListener("click", function () {
      sendComment();
    });

    newCommentInput.addEventListener("keydown", function (e) {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        sendComment();
      }
    });
  }

  function sendComment() {
    const statusSelect = document.getElementById("modalStatusSelect");
    const taskId = statusSelect ? statusSelect.getAttribute("data-task-id") : null;
    const content = newCommentInput.value.trim();

    if (!taskId || !content) return;

    submitCommentBtn.disabled = true;

    fetch(`${baseUrl}/task/addComment`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        task_id: taskId,
        content: content,
      }),
    })
      .then((response) => {
        if (!response.ok) throw new Error("Không thể gửi bình luận");
        return response.json();
      })
      .then((data) => {
        if (data.success && data.comment) {
          newCommentInput.value = "";
          
          const commentsList = document.getElementById("modalCommentsList");
          if (commentsList) {
            if (commentsList.innerHTML.includes("Chưa có bình luận nào")) {
              commentsList.innerHTML = "";
            }
            commentsList.appendChild(createCommentElement(data.comment));
            commentsList.scrollTop = commentsList.scrollHeight;
          }
        } else {
          alert("Lỗi khi gửi bình luận, vui lòng thử lại.");
        }
      })
      .catch((error) => {
        console.error("[TaskSync] Send comment error:", error);
        alert("Lỗi kết nối mạng khi gửi bình luận.");
      })
      .finally(() => {
        submitCommentBtn.disabled = false;
      });
  }

  function createCommentElement(c) {
    const div = document.createElement("div");
    div.className = "p-3 rounded-3";
    div.style.backgroundColor = "#f8fafc";
    div.style.border = "1px solid #e2e8f0";
    
    let timeStr = "";
    if (c.created_at) {
      const date = new Date(c.created_at);
      if (!isNaN(date.getTime())) {
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        timeStr = `${hours}:${minutes} ${day}/${month}`;
      } else {
        timeStr = c.created_at.substring(11, 16) || "";
      }
    }

    const name = c.user_full_name ? c.user_full_name.trim() : c.username;
    
    div.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-bold text-dark" style="font-size: 0.88rem;">${escapeHtml(name)}</span>
        <span class="text-muted" style="font-size: 0.75rem;">${timeStr}</span>
      </div>
      <div class="text-secondary" style="font-size: 0.85rem; white-space: pre-wrap; word-break: break-word;">${escapeHtml(c.content)}</div>
    `;
    return div;
  }

  function escapeHtml(str) {
    if (!str) return "";
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});