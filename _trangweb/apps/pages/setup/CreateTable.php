<?php
/*
# Tạo bảng
*/
define("TABLE_CHARACTER", "utf8mb4");
define("TABLE_COLLATE", "utf8mb4_unicode_ci");









CreateTable("users", "
`id` INT(250) AUTO_INCREMENT,
`name` VARCHAR(100) NOT NULL,
`email` VARCHAR(100) NOT NULL,
`phone` VARCHAR(20) NULL,
`gender` INT(1) NOT NULL DEFAULT '0',
`password` VARCHAR(200) NOT NULL,
`login_key` VARCHAR(100) NULL,
`forget_key` VARCHAR(100) NULL,
`forget_expired` INT(1) NOT NULL DEFAULT '0',
`register_message` VARCHAR(250) NULL,
`email_notify` INT(1) NOT NULL DEFAULT '0',
`check_admin` VARCHAR(100) NULL,
`money` INT(200) NOT NULL DEFAULT '0',
`role` INT(1) NOT NULL DEFAULT '1',
`storage` LONGTEXT NULL,
`notifications_player_ids` TEXT NULL,
`posts_count` INT(250) NOT NULL DEFAULT '0',
`website_created_today` INT(250) NOT NULL DEFAULT '0',
`last_online` TIMESTAMP NULL,
`last_updated` TIMESTAMP NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id)
","Bảng lưu tài khoản người dùng");




CreateTable("roles", "
`id` INT(250) AUTO_INCREMENT,
`label` VARCHAR(50) NOT NULL,
`color` VARCHAR(50) NULL,
`default` INT(50) NULL,
PRIMARY KEY(id)
","Bảng lưu các vai trò (Admin, Quản lý, Tác giả...)");

CreateTable("permissions", "
`id` INT(250) AUTO_INCREMENT,
`name` VARCHAR(50) NOT NULL,
`label` VARCHAR(50) NOT NULL,
PRIMARY KEY(id)
","Bảng lưu các quyền (Sửa bài viết, Quản lý files...)");

CreateTable("roles_permissions", "
`id` INT(250) AUTO_INCREMENT,
`role_id` INT(250) NOT NULL,
`permission_name` VARCHAR(150) NOT NULL,
PRIMARY KEY(id)
","Bảng trung gian các quyền của vai trò");



CreateTable("storage", "
`key` VARCHAR(150) NOT NULL UNIQUE,
`value` LONGTEXT NOT NULL
","Bảng lưu các tùy chọn");




CreateTable("posts_categories", "
`id` INT(250) AUTO_INCREMENT,
`title` TEXT NOT NULL,
`link` TEXT NOT NULL,
`storage` LONGTEXT NULL,
`parent` TEXT NULL,
`grandparents` TEXT NULL,
`children` TEXT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id)
","Bảng chuyên mục");



CreateTable("posts", "
`id` INT(250) AUTO_INCREMENT,
`title` VARCHAR(250) NOT NULL,
`link` VARCHAR(250) NOT NULL,
`content` LONGTEXT NOT NULL,
`storage` LONGTEXT NULL,
`users_id` INT(250) NOT NULL,
`status` VARCHAR(10) NOT NULL,
`count` INT(250) NULL,
`pin` INT(1) NULL,
`parent` INT(250) NOT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
FOREIGN KEY(users_id) REFERENCES users(id)
","Bảng bài viết");






CreateTable("posts_categories_ref", "
`id` INT(250) AUTO_INCREMENT,
`posts_id` INT(250) NOT NULL,
`categories_id` INT(250) NOT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
FOREIGN KEY(posts_id) REFERENCES posts(id),
FOREIGN KEY(categories_id) REFERENCES posts_categories(id)
","Bảng trung gian chuyên mục bài viết");




CreateTable("posts_comments", "
`id` INT(250) AUTO_INCREMENT,
`posts_id` INT(250) NOT NULL,
`users_id` INT(250) NOT NULL DEFAULT '1',
`content` TEXT NOT NULL,
`parent` INT(250) NOT NULL DEFAULT '0',
`status` VARCHAR(10) NOT NULL,
`reply_user` INT(250) NULL,
`reply_read` INT(250) NOT NULL DEFAULT '0',
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
FOREIGN KEY(posts_id) REFERENCES posts(id),
FOREIGN KEY(users_id) REFERENCES users(id),
FOREIGN KEY(reply_user) REFERENCES users(id)
","Bảng lưu bình luận bài viết");




CreateTable("files", "
`id` INT(250) AUTO_INCREMENT,
`name` TEXT NOT NULL,
`size` TEXT NOT NULL,
`folder` TEXT NOT NULL,
`type` TEXT NOT NULL,
`description` TEXT NOT NULL,
`posts_id` INT(250) NULL,
`products_id` INT(250) NULL,
`deleted` INT(1) NOT NULL DEFAULT '0',
`users_id` INT(250) NOT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
FOREIGN KEY(users_id) REFERENCES users(id),
FOREIGN KEY(posts_id) REFERENCES posts(id)
","Bảng lưu thông tin file");


CreateTable("builder_domain", "
`id` INT(250) AUTO_INCREMENT,
`domain` VARCHAR(100) NOT NULL,
`default_domain` VARCHAR(100) NOT NULL,
`alias_domain` VARCHAR(100) NULL,
`users_id` INT(250) NOT NULL,
`app` VARCHAR(50) NOT NULL,
`app_name` VARCHAR(100) NULL,
`app_id` VARCHAR(30) NULL,
`app_price` INT(250) NULL DEFAULT '0',
`app_categories` VARCHAR(100) NULL,
`app_description` TEXT NULL,
`package` VARCHAR(50) NULL,
`expired` INT(250) NOT NULL,
`suspended` INT(1) NOT NULL DEFAULT '0',
`ssl_type` INT(1) NOT NULL DEFAULT '0',
`ssl_private_key` TEXT NULL,
`ssl_certificate` TEXT NULL,
`ssl_cacert` TEXT NULL,
`user_login` VARCHAR(100) NULL,
`password` VARCHAR(100) NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
FOREIGN KEY(users_id) REFERENCES users(id)
","Bảng lưu danh sách website của người dùng");


CreateTable("recharge", "
`id` INT(250) AUTO_INCREMENT,
`users_id` INT(250) NOT NULL,
`amount` INT(10) NOT NULL,
`bank` VARCHAR(100) NOT NULL,
`status` VARCHAR(50) NOT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
FOREIGN KEY(users_id) REFERENCES users(id)
","Bảng lưu lịch sử nạp tiền");


CreateTable("payment_history", "
`id` INT(250) AUTO_INCREMENT,
`users_id` INT(250) NOT NULL,
`category` VARCHAR(100) NOT NULL,
`amount` INT(10) NOT NULL,
`note` TEXT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
FOREIGN KEY(users_id) REFERENCES users(id)
","Bảng lưu lịch sử giao dịch");


CreateTable("payment_history_categories", "
`name` VARCHAR(50) NOT NULL,
`label` VARCHAR(100) NOT NULL,
PRIMARY KEY(name)
","Bảng lưu lịch sử giao dịch");


CreateTable("online_chat", "
`users_id` INT(250) NOT NULL,
`data` LONGTEXT NOT NULL,
`readed_member` INT(1) NOT NULL DEFAULT '0',
`readed_manager` INT(1) NOT NULL DEFAULT '0',
`typing` INT(250) NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(users_id),
FOREIGN KEY(users_id) REFERENCES users(id)
","Chat online với các thành viên");

CreateTable("website_guest", "
`id` INT(250) AUTO_INCREMENT,
`title` VARCHAR(100) NOT NULL,
`domain` VARCHAR(100) NOT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
","Bảng lưu danh sách khách hàng");


CreateTable("app_store", "
`id` INT(250) AUTO_INCREMENT,
`name` VARCHAR(100) NOT NULL,
`button_label` VARCHAR(50) NOT NULL,
`description` VARCHAR(250) NULL,
`content` TEXT NULL,
`paid_content` TEXT NULL,
`price` INT(250) DEFAULT '0',
`category` INT(250) NOT NULL,
`type` VARCHAR(50) NOT NULL,
`image` VARCHAR(250) NOT NULL,
`plugin_file` VARCHAR(250) NULL,
`renew_type` VARCHAR(50) NOT NULL,
`required_domain` INT(1) DEFAULT '0',
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
","Bảng lưu kho ứng dụng");


CreateTable("app_store_categories", "
`id` INT(250) AUTO_INCREMENT,
`name` VARCHAR(100) NOT NULL,
`description` TEXT NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
","Bảng lưu chuyên mục kho ứng dụng");

CreateTable("app_store_owned", "
`id` INT(250) AUTO_INCREMENT,
`user_id` INT(250) NOT NULL,
`app_id` INT(250) NOT NULL,
`app_name` VARCHAR(250) NOT NULL,
`app_price` INT(250) NOT NULL,
`domain` VARCHAR(150) NULL,
`expired` INT(250) NULL,
`created_at` TIMESTAMP NULL,
`updated_at` TIMESTAMP NULL,
PRIMARY KEY(id),
","Bảng lưu ứng dụng đã mua của người dùng");