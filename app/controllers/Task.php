<?php
class Task extends Controller
{
    private $taskModel;
    //Nạp thêm projectModel cho các chức năng liên quan
    private $projectModel;
    public function __construct()
    {
        // Tự động nạp TaskModel
        $this->taskModel = $this->model('TaskModel');
        $this->projectModel = $this->model('ProjectModel');
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


    public function create()
    {
        // Tạo task mới
        $data['page_title'] = "Tạo Issue mới";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'] ?? null;
            $type = $_POST['type'] ?? 'task';
            $userId = $_SESSION['user']['id'];

            // Kiểm tra quyền hạn trước khi tạo task
            $projectModel = $this->model('ProjectModel');
            $userProjectRole = $projectModel->getProjectUserRole($projectId, $userId);
            $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';

            // Viewer hoặc không thuộc dự án -> không có quyền
            if (!$isAdmin && $userProjectRole !== 'manager' && $userProjectRole !== 'member') {
                $_SESSION['flash_error'] = "Bạn không có quyền tạo công việc trong dự án này.";
                redirect('project/myProjects');
                exit();
            }

            // Member chỉ được tạo task thường và bug
            if (!$isAdmin && $userProjectRole === 'member' && !in_array($type, ['task', 'bug'])) {
                $_SESSION['flash_error'] = "Thành viên thường chỉ được quyền tạo task thường và bug.";
                redirect('project/kanban/' . $projectId);
                exit();
            }

            // Xử lý due_date: chỉ lưu nếu hợp lệ và lớn hơn thời điểm hiện tại
            $dueDateRaw = trim($_POST['due_date'] ?? '');
            $dueDate = null;
            if (!empty($dueDateRaw)) {
                $parsedDate = strtotime($dueDateRaw);
                if ($parsedDate !== false && $parsedDate > time()) {
                    $dueDate = date('Y-m-d H:i:s', $parsedDate);
                }
            }

            $data = [
                'project_id'  => $projectId,
                'parent_issue_id' => !empty($_POST['parent_issue_id']) ? $_POST['parent_issue_id'] : null,
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'type'        => $type,
                'priority'    => $_POST['priority'] ?? 'MEDIUM',
                'assignee_id' => !empty($_POST['assignee_id']) ? $_POST['assignee_id'] : null,
                'due_date'    => $dueDate,
                'reporter_id' => $userId // Người tạo chính là người đang login
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
                redirect('project/myProjects');
                exit();
            }
        }
        $this->view('pages/tasks/create', $data);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $taskId = $input['task_id'] ?? null;

            if ($taskId) {
                // Phân quyền xóa Task
                $userId = $_SESSION['user']['id'];
                $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
                $projectId = $this->taskModel->getProjectIdByTaskId($taskId);
                $userProjectRole = $this->projectModel->getProjectUserRole($projectId, $userId);

                if (!$isAdmin && $userProjectRole !== 'manager') {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'Bạn không có quyền xóa công việc này.']);
                    exit();
                }

                $taskModel = $this->model('TaskModel');
                $success = $taskModel->deleteTask($taskId);

                header('Content-Type: application/json');
                echo json_encode(['success' => $success]);
                exit();
            }
        }
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Yêu cầu không hợp lệ']);
        exit();
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

        // 1. Lấy ID dự án thực tế của Task này
        $projectId = $this->taskModel->getProjectIdByTaskId($id);

        // 2. Kiểm tra tư cách thành viên dự án
        $userRoleInProject = $this->projectModel->getProjectUserRole($projectId, $_SESSION['user']['id']);
        $isAdmin = ($_SESSION['user']['role'] === 'admin');

        if (!$userRoleInProject && !$isAdmin) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'error' => 'Bạn không có quyền truy cập công việc này.']);
            exit();
        }
        
        $task['current_user_role'] = $isAdmin ? 'admin' : $userRoleInProject;

        // Lấy danh sách subtasks
        $subtasks = $this->taskModel->getSubtasksByTaskId($id);
        $task['subtasks'] = $subtasks;

        // Lấy danh sách bình luận (Comments)
        $comments = $this->taskModel->getCommentsByTaskId($id);
        $task['comments'] = $comments;

        // Lấy danh sách thành viên dự án để gán Assignee động
        $members = $this->projectModel->getProjectMembers($projectId);
        $activeMembers = array_filter($members ?? [], function ($m) {
            return ($m['status'] ?? 'active') === 'active';
        });
        $task['project_members'] = array_values($activeMembers);

        echo json_encode($task);
        exit;
    }

    // API thêm bình luận mới
    public function addComment()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $taskId = $_POST['task_id'] ?? $input['task_id'] ?? null;
        $content = $_POST['content'] ?? $input['content'] ?? null;
        $userId = $_SESSION['user']['id'];

        if (!$taskId || empty(trim($content))) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu ID công việc hoặc nội dung bình luận']);
            exit;
        }

        $success = $this->taskModel->addComment($taskId, $userId, $content);

        if ($success) {
            $commentId = $this->taskModel->getLastInsertedId();
            $comment = $this->taskModel->getCommentById($commentId);
            echo json_encode(['success' => true, 'comment' => $comment]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể lưu bình luận vào cơ sở dữ liệu']);
        }
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

        // Phân quyền kéo thả
        $userId = $_SESSION['user']['id'];
        $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
        
        $projectId = $this->taskModel->getProjectIdByTaskId($taskId);
        $userProjectRole = $this->projectModel->getProjectUserRole($projectId, $userId);

        if (!$isAdmin && $userProjectRole !== 'manager') {
            if ($userProjectRole === 'viewer' || !$userProjectRole) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Bạn không có quyền thay đổi trạng thái công việc này.']);
                exit;
            }
            
            if ($userProjectRole === 'member') {
                $taskDetails = $this->taskModel->getById($taskId);
                if ($taskDetails['assignee_id'] != $userId) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'Thành viên chỉ được quyền thay đổi trạng thái của công việc được giao cho mình.']);
                    exit;
                }
            }
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
                // Phân quyền sửa Assignee
                $userId = $_SESSION['user']['id'];
                $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
                $projectId = $this->taskModel->getProjectIdByTaskId($taskId);
                $userProjectRole = $this->projectModel->getProjectUserRole($projectId, $userId);

                if (!$isAdmin && $userProjectRole !== 'manager') {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'Bạn không có quyền thay đổi người thực hiện.']);
                    exit();
                }

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

    public function updateDueDate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            $taskId = $input['task_id'] ?? null;
            $dueDate = $input['due_date'] ?? null;

            if ($taskId) {
                // Phân quyền sửa Due Date
                $userId = $_SESSION['user']['id'];
                $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
                $projectId = $this->taskModel->getProjectIdByTaskId($taskId);
                $userProjectRole = $this->projectModel->getProjectUserRole($projectId, $userId);

                if (!$isAdmin && $userProjectRole !== 'manager') {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'Bạn không có quyền thay đổi hạn hoàn thành.']);
                    exit();
                }

                $taskModel = $this->model('TaskModel');
                $success = $taskModel->updateDueDate($taskId, $dueDate);

                header('Content-Type: application/json');
                echo json_encode(['success' => $success]);
                exit();
            }
        }
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Yêu cầu không hợp lệ']);
        exit();
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            $taskId = $input['task_id'] ?? null;
            $type = $input['type'] ?? null;

            $validTypes = ['epic', 'story', 'task', 'bug'];
            if ($taskId && in_array($type, $validTypes)) {
                // Phân quyền sửa Type
                $userId = $_SESSION['user']['id'];
                $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
                $projectId = $this->taskModel->getProjectIdByTaskId($taskId);
                $userProjectRole = $this->projectModel->getProjectUserRole($projectId, $userId);

                if (!$isAdmin && $userProjectRole !== 'manager') {
                    if ($userProjectRole === 'viewer' || !$userProjectRole) {
                        http_response_code(403);
                        echo json_encode(['success' => false, 'error' => 'Bạn không có quyền thay đổi loại công việc.']);
                        exit();
                    }
                    if ($userProjectRole === 'member') {
                        if ($type !== 'task' && $type !== 'bug') {
                            http_response_code(403);
                            echo json_encode(['success' => false, 'error' => 'Thành viên chỉ được quyền đổi sang loại Task hoặc Bug.']);
                            exit();
                        }
                    }
                }

                $success = $this->taskModel->updateType($taskId, $type);

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
