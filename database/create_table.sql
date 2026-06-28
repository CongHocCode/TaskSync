-- ==========================================
-- 1. TẠO CẤU TRÚC BẢNG (CREATE TABLE)
-- ==========================================
CREATE TABLE
  `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(255) UNIQUE NOT NULL,
    `email` varchar(255) UNIQUE NOT NULL,
    `password_hash` varchar(255) NOT NULL,
    `role` ENUM ('admin', 'user') DEFAULT 'user',
    `status` ENUM ('active', 'inactive') DEFAULT 'active', -- Bổ sung cột Trạng thái hoạt động
    `last_name` varchar(255),
    `first_name` varchar(255),
    `avatar_url` varchar(255),
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP
  );

CREATE TABLE
  `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255) NOT NULL,
    `key` varchar(255) UNIQUE NOT NULL,
    `description` text,
    `owner_id` INT NOT NULL,
    `github_repo_url` varchar(255),
    `issue_counter` INT DEFAULT 0,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP
  );

CREATE TABLE
  `project_members` (
    `project_id` INT,
    `user_id` INT,
    `role` ENUM ('manager', 'member', 'viewer') DEFAULT 'member',
    `status` ENUM('pending', 'active') NOT NULL DEFAULT 'pending', -- Bổ sung cột Trạng thái lời mời
    `invited_by` INT DEFAULT NULL, -- Bổ sung ID người gửi lời mời
    PRIMARY KEY (`project_id`, `user_id`)
  );

CREATE TABLE
  `issues` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `parent_issue_id` INT DEFAULT NULL, -- Tự liên kết cho việc con (Subtasks)
    `issue_key` varchar(255) UNIQUE NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text,
    `type` ENUM ('epic', 'story', 'task', 'bug') DEFAULT 'task',
    `status` ENUM ('todo', 'in_progress', 'in_review', 'done') DEFAULT 'todo',
    `priority` ENUM ('highest', 'high', 'medium', 'low') DEFAULT 'medium',
    `reporter_id` INT NOT NULL,
    `assignee_id` INT DEFAULT NULL,
    `due_date` datetime,
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP,
    `github_branch_url` varchar(255),
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP
  );

CREATE TABLE
  `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `issue_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `content` text,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP
  );

-- ==========================================
-- 2. THIẾT LẬP MỐI QUAN HỆ (ALTER TABLE)
-- ==========================================
-- Khóa ngoại cho bảng Projects
ALTER TABLE `projects` ADD FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Khóa ngoại cho bảng Project_Members
ALTER TABLE `project_members` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

ALTER TABLE `project_members` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Bổ sung khóa ngoại liên kết người mời (invited_by) về bảng users.id (SET NULL để khi user bị xóa thì liên kết lời mời chỉ bị rỗng chứ không xóa mất dòng thành viên)
ALTER TABLE `project_members` ADD FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Khóa ngoại cho bảng Issues
ALTER TABLE `issues` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

ALTER TABLE `issues` ADD FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`);

ALTER TABLE `issues` ADD FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`);

ALTER TABLE `issues` ADD FOREIGN KEY (`parent_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;

-- Khóa ngoại cho bảng Comments
ALTER TABLE `comments` ADD FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;

ALTER TABLE `comments` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;