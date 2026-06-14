<div class="page-content">
    <div class="page-header">
        <h2>Tạo Issue mới</h2>
    </div>

    <div class="app-card">
        <form method="POST">
            <div class="app-form-group">
                <label for="task-title">Tiêu đề</label>
                <input type="text" id="task-title" name="title" class="form-control" required>
            </div>
            <div class="app-form-group">
                <label for="task-desc">Mô tả</label>
                <textarea id="task-desc" name="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="app-btn">Tạo Task</button>
        </form>
    </div>
