-- Tạo cơ sở dữ liệu nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS member_management;

-- Sử dụng cơ sở dữ liệu
USE member_management;

-- Tạo bảng users
CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng user_profiles
CREATE TABLE IF NOT EXISTS user_profiles (
    profile_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    address VARCHAR(200),
    city VARCHAR(50),
    country VARCHAR(50),
    phone VARCHAR(20),
    bio TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm tài khoản admin mặc định (password: admin123)
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$hXPL.8TQz5BTNNji1QaJHu9oVR./bWpyXuVgc2nhLJ3OhKkDgb2li', 'admin@example.com', 'admin');

-- Tạo profile cho admin
INSERT INTO user_profiles (user_id, first_name, last_name) 
VALUES (1, 'Admin', 'System');