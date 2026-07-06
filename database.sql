SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS notices;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100),
    university_id VARCHAR(50) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('student', 'teacher', 'admin') DEFAULT 'student',
    dept VARCHAR(50),
    profile_pic VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    content TEXT,
    post_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    target_audience ENUM('all', 'students', 'teachers') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SET FOREIGN_KEY_CHECKS = 1;

-- 2nd Change in DB (for Delete Post whcih a teacher posts)

ALTER TABLE notices ADD COLUMN user_id INT AFTER id;

--- DB for Comment Option

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    user_id INT,
    comment_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

--- DB for Alumni Role

ALTER TABLE users MODIFY COLUMN role ENUM('student', 'teacher', 'admin', 'alumni') DEFAULT 'student';

--- DB for Lost and Found

CREATE TABLE IF NOT EXISTS lost_found (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    item_status ENUM('lost', 'found') NOT NULL,
    item_image VARCHAR(255) DEFAULT 'no_image.png',
    contact_info VARCHAR(255),
    is_resolved TINYINT(1) DEFAULT 0, -- 0 = Active, 1 = Found/Returned
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

--- DB for Academic Section

-- Table for Academic Files (Routines, Notes, Materials)
CREATE TABLE IF NOT EXISTS academic_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    category ENUM('class_routine', 'exam_routine', 'course_material') NOT NULL,
    dept VARCHAR(50),
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for Assignments
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    deadline DATETIME,
    file_path VARCHAR(255),
    dept VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);