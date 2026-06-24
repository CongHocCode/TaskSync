<?php
class Task extends Controller
{
    private $taskModel;

    public function __construct()
    {
        // Tự động nạp TaskModel
        $this->taskModel = $this->model('TaskModel');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Bỏ qua xác thực cho các cuộc gọi API Fetch ngầm nếu cần, nhưng tốt nhất vẫn giữ bảo mật cơ bản
        if (!isset($_SESSION['user'])) {
            // Đối với API, trả về lỗi thay vì redirect
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
                || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Vui lòng đăng nhập để tiếp tục.']);
                exit();
            }
            redirect('auth');
            exit();
        }
    }

    public function index()
    {
        // Thiết lập tiêu đề trang cho Layout
        $data['page_title'] = "Bảng công việc Kanban";
        $this->view('pages/tasks/kanban', $data);
    }

    public function myTasks()
    {
        // Công việc của người dùng hiện tại
        $data['page_title'] = "Task của tôi (2)";
        $this->view('pages/tasks/my-tasks', $data);
    }

    public function create()
    {
        // Tạo task mới
        $data['page_title'] = "Tạo Issue mới";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'project_id'  => $_POST['project_id'] ?? null,
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'type'        => $_POST['type'] ?? 'task',
                'priority'    => $_POST['priority'] ?? 'MEDIUM',
                'assignee_id' => $_POST['assignee_id'] ?? null,
                'reporter_id' => $_SESSION['user']['id'] // Người tạo chính là người đang login
            ];

            if ($data['project_id'] && !empty($data['title'])) {
                $taskModel = $this->model('TaskModel');
                $success = $taskModel->createIssue($data);

                if ($success) {
                    // Tạo xong thành công thì quay lại đúng trang Kanban của dự án đó
                    header('Location: ' . BASE_URL . '/project/kanban/' . $data['project_id']);
                    exit();
                }

                // Nếu lỗi, đẩy ngược về danh sách dự án
                redirect('workspace/my_projects');
                exit();
            }
        }
        $this->view('pages/tasks/create', $data);
    }

    public function edit($taskId = null)
    {
        // Chỉnh sửa task
        $data['page_title'] = "Chỉnh sửa Task";
        $data['task_id'] = $taskId;
        $this->view('pages/tasks/edit', $data);
    }

    // API lấy chi tiết task dạng JSON
    public function detail($id = null)
    {
        header('Content-Type: application/json');

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu ID của task']);
            exit;
        }

        $task = $this->taskModel->getById($id);
        if (!$task) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy công việc']);
            exit;
        }

        // Lấy danh sách subtasks
        $subtasks = $this->taskModel->getSubtasksByTaskId($id);
        $task['subtasks'] = $subtasks;

        echo json_encode($task);
        exit;
    }

    // API cập nhật trạng thái của task
    public function updateStatus()
    {
        header('Content-Type: application/json');

        // Hỗ trợ cả POST thường và JSON Payload
        $input = json_decode(file_get_contents('php://input'), true);
        $taskId = $_POST['task_id'] ?? $input['task_id'] ?? null;
        $status = $_POST['status'] ?? $input['status'] ?? null;

        if (!$taskId || !$status) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu tham số task_id hoặc status']);
            exit;
        }

        $validStatuses = ['todo', 'in_progress', 'in_review', 'done'];
        if (!in_array($status, $validStatuses)) {
            http_response_code(400);
            echo json_encode(['error' => 'Trạng thái không hợp lệ']);
            exit;
        }

        $success = $this->taskModel->updateStatus($taskId, $status);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể cập nhật trạng thái trong cơ sở dữ liệu']);
        }
        exit;
    }

    public function updateAssignee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            $taskId = $input['task_id'] ?? null;
            $assigneeId = $input['assignee_id'] ?? null; // Có thể là null nếu chọn "Unassigned"

            if ($taskId) {
                $taskModel = $this->model('TaskModel');
                $success = $taskModel->updateTaskAssignee($taskId, $assigneeId);

                header('Content-Type: application/json');
                echo json_encode(['success' => $success]);
                exit();
            }
        }
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Yêu cầu không hợp lệ']);
        exit();
    }
}
