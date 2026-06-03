CREATE TABLE `users` (
  `id` integer PRIMARY KEY,
  `username` varchar(255) UNIQUE NOT NULL,
  `email` varchar(255) UNIQUE NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` ENUM ('admin', 'user') DEFAULT 'user',
  `last_name` varchar(255),
  `first_name` varchar(255),
  `avatar_url` varchar(255),
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `projects` (
  `id` integer PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) UNIQUE NOT NULL,
  `description` text,
  `owner_id` integer NOT NULL,
  `github_repo_url` varchar(255),
  `issue_counter` integer DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `project_members` (
  `project_id` integer,
  `user_id` integer,
  `role` ENUM ('manager', 'member', 'viewer') DEFAULT 'member',
  PRIMARY KEY (`project_id`, `user_id`)
);

CREATE TABLE `issues` (
  `id` integer PRIMARY KEY,
  `project_id` integer NOT NULL,
  `parent_issue_id` integer,
  `issue_key` varchar(255) UNIQUE NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `type` ENUM ('epic', 'story', 'task', 'bug') DEFAULT 'task',
  `status` ENUM ('todo', 'in_progress', 'in_review', 'done') DEFAULT 'todo',
  `priority` ENUM ('highest', 'high', 'medium', 'low') DEFAULT 'medium',
  `reporter_id` integer NOT NULL,
  `assignee_id` integer,
  `due_date` datetime,
  `updated_at` datetime,
  `github_branch_url` varchar(255),
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `comments` (
  `id` integer PRIMARY KEY,
  `issue_id` integer NOT NULL,
  `user_id` integer NOT NULL,
  `content` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime
);

ALTER TABLE `projects` ADD FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

ALTER TABLE `project_members` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `project_members` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `issues` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `issues` ADD FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`);

ALTER TABLE `issues` ADD FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`);

ALTER TABLE `issues` ADD FOREIGN KEY (`parent_issue_id`) REFERENCES `issues` (`id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);