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

---link option for academic part

ALTER TABLE academic_files MODIFY COLUMN file_path VARCHAR(255) NULL;
ALTER TABLE academic_files ADD COLUMN external_link TEXT NULL AFTER file_path;

--- DB for CGPA Calculator

CREATE TABLE IF NOT EXISTS gpa_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    semester_name VARCHAR(100),
    gpa DECIMAL(3,2),
    total_credits INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


--- update for cgpa calculator

DROP TABLE IF EXISTS gpa_records;

CREATE TABLE gpa_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    semester_name VARCHAR(100),
    gpa DECIMAL(3,2),
    total_credits INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_semester (user_id, semester_name), 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE gpa_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT,
    course_name VARCHAR(255),
    credits INT,
    grade DECIMAL(3,2),
    FOREIGN KEY (record_id) REFERENCES gpa_records(id) ON DELETE CASCADE
);

--- DB for Assignment Module

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

CREATE TABLE IF NOT EXISTS assignment_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT,
    student_id INT,
    submission_file VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

--- DB for Authentication(User Registration OTP)

ALTER TABLE users ADD COLUMN otp VARCHAR(6) NULL;
ALTER TABLE users ADD COLUMN is_verified TINYINT(1) DEFAULT 0; -- 0 = Not Verified, 1 = Verified

-- DB for User Profile

ALTER TABLE users 
ADD COLUMN phone VARCHAR(20) NULL,
ADD COLUMN bio TEXT NULL,
ADD COLUMN batch VARCHAR(50) NULL,
ADD COLUMN skills VARCHAR(255) NULL,
ADD COLUMN linkedin_url VARCHAR(255) NULL;

--- DB for User Connections

CREATE TABLE IF NOT EXISTS connections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    status ENUM('pending', 'accepted') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

--- DB for Like Handling

CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (post_id, user_id), -- একই মানুষ এক পোস্টে দুইবার লাইক দিতে পারবে না
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);