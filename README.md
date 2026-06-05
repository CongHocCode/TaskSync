
```text
- các đọc file .md: 
+ mở ở trang riêng: ctrl + shift + v
+ chỉnh sửa và xem trực tiếp: ctrl + K, sau đó ấn v
```
# 1. Cấu trúc thư mục
```text
/TaskSync
│
├── /app                   <-- TẦNG ỨNG DỤNG (Chứa toàn bộ logic, bảo mật và giao diện)
│   │
│   ├── /core              <-- Lõi Framework MVC
│   │   ├── App.php        <-- Trái tim điều hướng URL (Routing), phân tích URL để gọi đúng Controller.
│   │   ├── Controller.php <-- Lớp cha chứa các hàm cơ bản để nạp Model và xuất View.
│   │   └── Database.php   <-- Lớp cấu hình và kết nối CSDL MySQL bằng PDO.
│   │
│   ├── /controllers       <-- TẦNG ĐIỀU KHIỂN (Nhận Request -> Xử lý logic -> Gọi View)
│   │   ├── Auth.php       <-- Xử lý đăng nhập, đăng xuất và kiểm tra phân quyền (Session).
│   │   ├── Admin.php      <-- Xử lý logic cho không gian Quản trị viên (Quản lý User, System Workspace).
│   │   ├── Workspace.php  <-- Xử lý logic cho không gian cá nhân (Dashboard tổng hợp, My Tasks, My Projects).
│   │   ├── Project.php    <-- Xử lý logic bên trong một dự án cụ thể (Bảng Kanban, Danh sách, Thành viên).
│   │   ├── Task.php       <-- Xử lý CRUD cho công việc, nhận API từ AJAX để kéo thả và load chi tiết Task.
│   │   └── User.php       <-- Xử lý logic cập nhật thông tin hồ sơ cá nhân (Profile).
│   │
│   ├── /models            <-- TẦNG DỮ LIỆU (Tương tác trực tiếp với Database)
│   │   ├── UserModel.php  <-- Các câu lệnh SQL liên quan đến người dùng (Tìm kiếm, thêm, sửa, xóa, check login).
│   │   ├── ProjectModel.php<-- Các câu lệnh SQL liên quan đến dự án (Tạo dự án, gán quyền, lấy danh sách).
│   │   └── TaskModel.php  <-- Các câu lệnh SQL liên quan đến công việc (Thêm task, đổi trạng thái cột).
│   │
│   └── /views             <-- TẦNG GIAO DIỆN (Hiển thị HTML và dữ liệu từ Controller)
│       ├── layout.php     <-- [Master Layout] Khung sườn gốc chứa các thẻ <html>, <head>, nhúng CSS/JS.
│       │
│       ├── /partials      <-- Các thành phần giao diện được cắt nhỏ để dùng chung, dễ bảo trì
│       │   ├── topbar.php           <-- Thanh điều hướng ngang trên cùng (Thanh tìm kiếm, thông báo, avatar).
│       │   ├── sidebar_admin.php    <-- Thanh menu dọc bên trái dành riêng cho màn hình Quản trị viên.
│       │   ├── sidebar_workspace.php<-- Thanh menu dọc bên trái dành cho không gian làm việc và dự án.
│       │   └── task_modal_right.php <-- Khung giao diện (Offcanvas/Modal) trượt từ phải sang chứa chi tiết Task.
│       │
│       ├── /pages         <-- Các trang giao diện chức năng cụ thể (Nội dung chính thay đổi theo URL)
│       │   ├── /auth      
│       │   │   └── login.php        <-- Trang đăng nhập hệ thống.
│       │   │
│       │   ├── /admin               
│       │   │   ├── dashboard.php    <-- Trang thống kê tổng quan toàn hệ thống (Dành cho Admin).
│       │   │   ├── users.php        <-- Trang quản lý danh sách và quyền của tất cả tài khoản.
│       │   │   └── projects.php     <-- Trang quản lý danh sách toàn bộ dự án trên hệ thống.
│       │   │
│       │   ├── /workspace           
│       │   │   ├── dashboard.php    <-- Trang tổng quan cá nhân của user khi vừa đăng nhập.
│       │   │   ├── my_tasks.php     <-- Trang liệt kê các công việc được gán cho cá nhân.
│       │   │   └── my_projects.php  <-- Trang hiển thị các dự án mà cá nhân đang tham gia.
│       │   │
│       │   ├── /projects            
│       │   │   ├── kanban.php       <-- Bảng công việc dạng cột (To do, In progress...) hỗ trợ kéo thả.
│       │   │   ├── list.php         <-- Danh sách công việc hiển thị dưới dạng bảng lưới.
│       │   │   └── members.php      <-- Quản lý danh sách thành viên thuộc một dự án cụ thể.
│       │   │
│       │   └── /user                
│       │       └── profile.php      <-- Trang cài đặt hồ sơ và thông tin cá nhân.
│       │
│       └── /errors        
│           └── 404.php    <-- Giao diện báo lỗi khi truy cập URL không tồn tại.
│
├── /public                <-- CỬA NGÕ BẢO MẬT (Thư mục duy nhất public ra ngoài Internet)
│   ├── /css               <-- Thư mục chứa các file định dạng giao diện (style.css).
│   ├── /js                <-- Thư mục chứa các file xử lý kịch bản trình duyệt (script.js xử lý DOM, AJAX).
│   ├── /images            <-- Thư mục chứa tài nguyên hình ảnh tĩnh (logo, default avatar).
│   ├── .htaccess          <-- File cấu hình Apache để điều hướng mọi Request về index.php (Giấu cấu trúc thư mục).
│   └── index.php          <-- Front Controller: Nạp lõi hệ thống và khởi chạy ứng dụng.
│
└── /database              <-- LƯU TRỮ TÀI NGUYÊN
    └── task_sync.sql      <-- File xuất cấu trúc (Schema) của cơ sở dữ liệu để thành viên nhóm import.
```
# 📋 KẾ HOẠCH PHÁT TRIỂN DỰ ÁN TASKSYNC 
# 📘 TÀI LIỆU HƯỚNG DẪN KỸ THUẬT CHI TIẾT (TECHNICAL SPECIFICATIONS)

Tài liệu này mô tả chi tiết các bước nghiệp vụ cần xử lý và danh sách tập tin làm việc của **Quyết, Quyền, Thành** qua 4 giai đoạn của dự án.

---

## 📍 PHASE 1: KHỞI TẠO NỀN TẢNG

### 1. 👨‍💻 QUYẾT (Lớp Lõi & Cấu hình)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Cấu hình hệ thống:** Khởi tạo Git cục bộ, tạo file cấu hình `.gitignore` nhằm loại bỏ thư mục của IDE (`.vscode`, `.idea`) và các tệp tin lưu trữ cục bộ.
    *   **Định tuyến URL (Routing):** Viết mã nguồn cho lớp điều hướng URL tại `App.php` để xử lý chuỗi URL nhận từ biến `$_GET['url']` (ví dụ: `project/kanban/1`), phân tích thành mảng để tự động gọi Controller `Project`, phương thức `kanban` và truyền tham số `1`.
    *   **Lớp cơ sở (Base Class):** Xây dựng `Controller.php` cung cấp phương thức `view()` (sử dụng hàm `extract()` nhằm trích xuất biến truyền sang giao diện) và phương thức `model()` (để khởi tạo đối tượng tương tác CSDL).
    *   **Kết nối CSDL:** Thiết lập lớp kết nối CSDL trong `Database.php` sử dụng thư viện PDO, cấu hình chế độ báo lỗi ngoại lệ (`PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`) để hỗ trợ kiểm soát lỗi trong quá trình vận hành.
*   **Tập tin làm việc:**
    *   `/public/index.php` (Điểm tiếp nhận yêu cầu đầu tiên của hệ thống)
    *   `/.htaccess` và `/public/.htaccess` (Cấu hình máy chủ Apache để viết lại đường dẫn)
    *   `/app/core/App.php` (Lớp lõi Routing)
    *   `/app/core/Controller.php` (Lớp Controller cơ sở)
    *   `/app/core/Database.php` (Lớp kết nối PDO Singleton)

### 2. 🎨 QUYỀN (Database Master)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Vẽ sơ đồ quan hệ thực thể (ERD):** Xác định các thuộc tính, khóa chính, khóa ngoại và mối quan hệ giữa các thực thể: `users`, `projects`, `project_members`, `tasks`, `comments`.
    *   **Xây dựng CSDL:** Thiết lập mã SQL khởi tạo cấu trúc các bảng hoàn chỉnh, áp dụng ràng buộc khóa ngoại chặt chẽ (`ON DELETE CASCADE` hoặc `ON DELETE SET NULL`) để đảm bảo tính toàn vẹn dữ liệu khi có thao tác xóa.
*   **Tập tin làm việc:**
    *   `/database/task_sync.sql` (Kịch bản SQL khởi tạo cơ sở dữ liệu)

### 3. 🚀 THÀNH (Giao diện tĩnh & Môi trường CSS)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Thiết kế nháp giao diện (Mockup):** Phác thảo bố cục cấu trúc trang đăng nhập, bảng Kanban, và cấu trúc trang Dashboard.
    *   **Cài đặt CSS Framework:** Nhúng bộ thư viện Bootstrap 5 (CSS, JS) vào thư mục tĩnh public của hệ thống.
    *   **Căn chỉnh Grid System:** Tạo các file HTML tĩnh thô để thử nghiệm cách sắp xếp bố cục (Layout Grid) của Bootstrap 5 trên các loại màn hình khác nhau.
*   **Tập tin làm việc:**
    *   `/public/css/bootstrap.min.css` và `/public/js/bootstrap.bundle.min.js`
    *   Các file nháp HTML tĩnh đặt trong thư mục tạm `/public/mockup/`

---

## 📍 PHASE 2: THIẾT LẬP NỀN TẢNG (TUẦN 2-3)

### 1. 🚀 THÀNH (Cấu trúc Giao diện Tổng thể - Master UI)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Xây dựng Master Layout:** Thiết kế cấu trúc file `layout.php` đóng vai trò làm khung giao diện chung, sử dụng các hàm nạp của PHP để lắp ghép `header.php`, `sidebar.php`, `footer.php` và nội dung trang động (`$view`).
    *   **Thiết kế CSS dùng chung:** Khai báo các biến CSS (CSS Variables) trong `style.css` định hình bảng màu tối (Dark Blue) cho giao diện. Tự tùy biến định dạng CSS cho các thành phần nút bấm (Button), thông báo (Alert), thẻ hiển thị (Card) và các ô nhập liệu (Input Form).
*   **Tập tin làm việc:**
    *   `/app/views/layout.php` (Tập tin khung chính)
    *   `/app/views/partials/header.php`, `sidebar.php`, `footer.php`
    *   `/public/css/style.css` (Tập tin CSS của hệ thống)

### 2. 👨‍💻 QUYẾT (Module Hệ thống & Tài khoản)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Xử lý Đăng nhập (Auth):** Viết logic nhận dữ liệu từ form Đăng nhập. So sánh thông tin bằng câu truy vấn SELECT trong `UserModel.php`, đối chiếu mật khẩu đã băm bằng hàm `password_verify()`. Khởi tạo và lưu thông tin ID và Vai trò vào biến `$_SESSION['user']`.
    *   **Quản lý nhân sự (User CRUD):**
        *   *View:* Thiết lập bảng hiển thị danh sách nhân sự (Họ tên, Email, Vai trò, Trạng thái hoạt động).
        *   *Controller & Model:* Viết các phương thức thêm nhân viên mới, cập nhật thông tin và nút Đổi trạng thái (Bật/Tắt khóa tài khoản bằng câu truy vấn UPDATE trạng thái `status` trong CSDL).
*   **Tập tin làm việc:**
    *   `/app/controllers/Auth.php` (Xử lý Đăng nhập / Đăng xuất)
    *   `/app/controllers/User.php` (Xử lý logic CRUD Nhân viên)
    *   `/app/models/UserModel.php` (Thực hiện truy vấn bảng users)
    *   `/app/views/pages/auth/login.php` (Giao diện đăng nhập)
    *   `/app/views/pages/users/index.php` (Giao diện quản lý nhân viên)

### 3. 🎨 QUYỀN (Module Danh mục Dự án)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Danh sách Dự án:** Thực hiện câu lệnh SQL SELECT liên kết bảng để lấy ra danh sách các dự án mà tài khoản đang đăng nhập được phép tham gia và hiển thị lên giao diện dạng lưới Bootstrap Card.
    *   **Tạo Dự án mới:** Xây dựng form nhập liệu (Tên dự án, Key viết tắt, mô tả). Controller tiếp nhận dữ liệu POST, gọi `ProjectModel.php` thực hiện lệnh INSERT để lưu vào CSDL, đồng thời lưu vai trò Quản lý dự án (PM) cho người tạo vào bảng trung gian `project_members`.
*   **Tập tin làm việc:**
    *   `/app/controllers/Project.php` (Điều phối nghiệp vụ Dự án)
    *   `/app/models/ProjectModel.php` (Thực hiện truy vấn bảng projects)
    *   `/app/views/pages/projects/list.php` (Giao diện danh sách Dự án)
    *   `/app/views/pages/projects/create.php` (Form tạo Dự án mới)

---

## 📍 PHASE 3: QUẢN LÝ (TUẦN 4)

### 1. 🚀 THÀNH (Thư viện UI Nâng cao)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Thiết kế bảng Kanban:** Thiết lập cấu trúc HTML tĩnh cho bảng Kanban có 4 cột trạng thái: To Do, In Progress, In Review, Done. Mỗi cột sử dụng một Container Bootstrap độc lập.
    *   **Thiết kế Offcanvas Modal:** Xây dựng cấu trúc HTML cho Offcanvas Bootstrap (giao diện bảng trượt từ cạnh phải màn hình) hiển thị chi tiết thông tin công việc, vùng cập nhật mô tả nhanh, khu vực danh sách kiểm tra (checklist) và khu vực viết bình luận.
    *   **Tối ưu hóa Responsive:** Sử dụng CSS Media Queries để đảm bảo bảng Kanban tự động co giãn kích thước, ẩn sidebar hoặc đổi menu thành thanh điều hướng dạng gọn khi xem trên màn hình điện thoại hoặc máy tính bảng.
*   **Tập tin làm việc:**
    *   `/app/views/pages/tasks/kanban.php` (Giao diện Kanban tĩnh)
    *   `/app/views/partials/task_modal_right.php` (Giao diện Modal chi tiết task tĩnh)
    *   `/public/css/style.css` (Tập tin định dạng CSS bổ sung mã Responsive)

### 2. 👨‍💻 QUYẾT (Module Điều phối Team)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Gán thành viên vào dự án:** Thiết lập form trong trang chi tiết dự án để thêm nhân viên vào dự án hiện tại. Khi gửi form, Controller thực thi lưu thông tin cặp `project_id` và `user_id` kèm vai trò tương ứng (Member/PM) vào bảng `project_members`.
    *   **Chi tiết nhân sự & dự án:** Viết câu lệnh SQL lấy thông tin chi tiết của một nhân viên (danh sách dự án tham gia, trạng thái công việc được giao) và trang xem thông tin chi tiết dự án (Hiển thị các chỉ số thống kê của riêng dự án đó).
*   **Tập tin làm việc:**
    *   `/app/controllers/Project.php` (Xử lý gán thành viên)
    *   `/app/models/ProjectModel.php` (Truy vấn SQL bảng project_members)
    *   `/app/views/pages/projects/detail.php` (Trang thông tin chi tiết dự án)

### 3. 🎨 QUYỀN (Module Công việc - Issues)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Khởi tạo Task & sinh mã:** Xây dựng form thêm Task mới. Thiết lập thuật toán lấy mã viết tắt của dự án (Key Code) ghép với số thứ tự tăng tự động trong DB để sinh mã hiển thị công việc (Ví dụ: Dự án có Key là `WEB`, task tiếp theo sẽ tự động được gán mã hiển thị là `WEB-12`).
    *   **Bình luận (Comments):** Viết logic tiếp nhận bình luận mới từ người dùng. Nhận dữ liệu POST chứa nội dung, `task_id` và `user_id` hiện tại, thực hiện câu lệnh INSERT dữ liệu vào bảng `comments`.
*   **Tập tin làm việc:**
    *   `/app/controllers/Task.php` (Xử lý nghiệp vụ tạo Task và Bình luận)
    *   `/app/models/TaskModel.php` (Thực hiện truy vấn SQL bảng tasks và comments)
    *   `/app/views/pages/tasks/detail.php` (Trang thông tin chi tiết Task đơn lẻ)

---

## 📍 PHASE 4: VẬN HÀNH (TUẦN 5)

### 1. 🚀 THÀNH (Module Trực quan hóa Dữ liệu)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Tích hợp Chart.js:** Nhúng thư viện vẽ biểu đồ Chart.js vào trang Dashboard tổng. Viết mã JavaScript để khởi tạo biểu đồ cột (Tần suất xử lý công việc) và biểu đồ đường (Thống kê người dùng mới) bằng cách truyền dữ liệu mảng nhận được từ PHP.
    *   **JavaScript cho bảng Kanban:**
        *   Gán thuộc tính `draggable="true"` cho các thẻ Task. Viết sự kiện `dragstart` lưu ID của phần tử đang di chuyển.
        *   Viết sự kiện `dragover` trên các cột để thay đổi hiển thị vùng thả.
        *   Viết sự kiện `drop` bắt ID, cập nhật vị trí thẻ HTML trên giao diện động và thực thi Fetch API gọi ngầm lên server để cập nhật trạng thái mới mà không cần tải lại toàn bộ trang.
        *   Bắt sự kiện click vào thẻ Task để thực thi Fetch API gửi ID lên server lấy dữ liệu JSON chi tiết, dùng JavaScript điền dữ liệu động vào Offcanvas Modal của Quyền rồi hiển thị ra ngoài.
*   **Tập tin làm việc:**
    *   `/public/js/script.js` (Mã xử lý JavaScript tương tác động)
    *   `/app/views/pages/dashboard/index.php` (Khu vực khởi tạo thẻ canvas vẽ biểu đồ)

### 2. 👨‍💻 QUYẾT (Module Thống kê & Báo cáo)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **Truy vấn thống kê:** Viết các câu lệnh SQL sử dụng các hàm gom nhóm `GROUP BY` để đếm số lượng Task theo các trạng thái hoặc đếm số tài khoản đăng ký mới theo thời gian tháng.
    *   **Trả dữ liệu cho biểu đồ:** Trong controller `Dashboard.php`, gọi các hàm thống kê từ các Model để lấy dữ liệu. Dùng hàm `json_encode()` để định dạng dữ liệu thống kê thành dạng chuỗi JSON và truyền ra khối script trên View của Thành để JavaScript xử lý vẽ biểu đồ.
*   **Tập tin làm việc:**
    *   `/app/controllers/Dashboard.php` (Điều phối dữ liệu Dashboard tổng)
    *   `/app/models/TaskModel.php` / `ProjectModel.php` (Bổ sung các hàm SQL thống kê)
    *   `/app/models/UserModel.php` (Bổ sung câu lệnh SQL thống kê tài khoản mới)

### 3. 🎨 QUYỀN (Module Luồng công việc - Workflow)
*   **Mô tả chi tiết nhiệm vụ:**
    *   **API Cập nhật trạng thái:** Thiết lập phương thức API `updateStatus()` trong controller `Task.php` để nhận yêu cầu Fetch API gửi lên từ phía máy khách.
    *   **Kiểm soát luồng di chuyển trạng thái:** Viết logic kiểm tra nghiệp vụ trước khi cập nhật dữ liệu (Ví dụ: Kiểm tra xem tài khoản thực hiện có phải là Người được phân công - Assignee, Người tạo - Creator hoặc Quản lý của dự án đó hay không; chỉ cho phép người có quyền hạn thực hiện chuyển trạng thái công việc sang *Done*).
    *   **Cập nhật CSDL:** Nếu thông tin kiểm tra hợp lệ, gọi model thực hiện câu lệnh SQL UPDATE thay đổi trạng thái trong CSDL và phản hồi về máy khách chuỗi JSON báo thành công. Trường hợp không hợp lệ, trả về mã trạng thái lỗi HTTP 403.
*   **Tập tin làm việc:**
    *   `/app/controllers/Task.php` (Tiếp nhận cập nhật trạng thái)
    *   `/app/models/TaskModel.php` (Thực hiện cập nhật thay đổi trạng thái vào CSDL)