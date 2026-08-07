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

--DB for Alumni Hub

CREATE TABLE IF NOT EXISTS alumni_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    current_job_title VARCHAR(255),
    company_name VARCHAR(255),
    journey_story TEXT,
    skills_used VARCHAR(255), -- Tech stack/Skills
    career_roadmap TEXT,      -- Year 1 to Final Year guidance
    biggest_mistake TEXT,
    advice_to_juniors TEXT,
    first_salary VARCHAR(50) NULL, -- Optional
    inspired_count INT DEFAULT 0,  -- Like/Inspire Meter
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- DB for Alumni Hub's Inspire counter

CREATE TABLE IF NOT EXISTS alumni_inspired (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_inspire (story_id, user_id),
    FOREIGN KEY (story_id) REFERENCES alumni_stories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Db for Alumni Hub's Mentorship Platform

CREATE TABLE IF NOT EXISTS alumni_qna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT,
    student_id INT,
    question_text TEXT,
    answer_text TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES alumni_stories(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

--DB for Notice post(updated)


ALTER TABLE notices 
ADD COLUMN image_path VARCHAR(255) NULL AFTER description,
ADD COLUMN external_link TEXT NULL AFTER image_path;

--DB for Post Edit or Delete

ALTER TABLE posts ADD COLUMN is_edited TINYINT(1) DEFAULT 0 AFTER post_image;

--Db for Alumni Hub's Premium Index

CREATE TABLE IF NOT EXISTS alumni_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_id INT,
    job_title VARCHAR(255),
    company VARCHAR(255),
    location VARCHAR(255),
    job_type ENUM('Full-time', 'Internship', 'Part-time', 'Contract'),
    description TEXT,
    apply_link TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (alumni_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE alumni_jobs 
ADD COLUMN vacancy INT DEFAULT 1 AFTER job_type,
ADD COLUMN target_dept VARCHAR(50) AFTER location;

ALTER TABLE alumni_stories ADD COLUMN is_edited TINYINT(1) DEFAULT 0 AFTER first_salary;

--- DB for Message System

--msg request table
CREATE TABLE IF NOT EXISTS message_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    status ENUM('pending', 'accepted', 'declined', 'blocked') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_request (sender_id, receiver_id),
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

--converstation table (after accepting the request)
CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT,
    user2_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE
);

--msg store table
CREATE TABLE IF NOT EXISTS private_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    sender_id INT,
    message_text TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE private_messages ADD COLUMN is_edited TINYINT(1) DEFAULT 0 AFTER message_text;

CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT,
    user2_id INT,
    status ENUM('active', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS message_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    request_msg TEXT, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    UNIQUE KEY unique_req (sender_id, receiver_id),
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS message_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blocker_id INT,
    blocked_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blocker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS private_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    sender_id INT,
    message_text TEXT,
    message_type ENUM('text', 'file', 'image') DEFAULT 'text',
    file_path VARCHAR(255) NULL,
    reply_to INT NULL, 
    is_read TINYINT(1) DEFAULT 0,
    is_deleted TINYINT(1) DEFAULT 0,
    is_edited TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE private_messages 
ADD COLUMN message_type ENUM('text', 'file', 'image') DEFAULT 'text' AFTER message_text,
ADD COLUMN file_path VARCHAR(255) NULL AFTER message_type;

ALTER TABLE private_messages ADD COLUMN reply_to INT NULL AFTER file_path;

ALTER TABLE users ADD COLUMN last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE users ADD COLUMN is_private TINYINT(1) DEFAULT 0; -- 0 = Public, 1 = Private

-- ১. প্রথমে মেসেজ টেবিল থেকে কনভারসেশন আইডি রিলেশন আপডেট করা
ALTER TABLE private_messages DROP FOREIGN KEY private_messages_ibfk_1;

ALTER TABLE private_messages 
ADD CONSTRAINT private_messages_ibfk_1 
FOREIGN KEY (conversation_id) REFERENCES conversations(id) 
ON DELETE CASCADE;

-- ২. একইভাবে মেসেজ টেবিল থেকে সেন্ডার আইডি রিলেশন (বিকল্প সুরক্ষা)
ALTER TABLE private_messages DROP FOREIGN KEY private_messages_ibfk_2;

ALTER TABLE private_messages 
ADD CONSTRAINT private_messages_ibfk_2 
FOREIGN KEY (sender_id) REFERENCES users(id) 
ON DELETE CASCADE;

ALTER TABLE academic_files 
MODIFY COLUMN category ENUM('class_routine', 'exam_routine', 'course_material', 'course_outline') NOT NULL;

-- ১. ইভেন্ট টেবিল
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT, -- কে ইভেন্টটি দিচ্ছে (Teacher/Admin)
    title VARCHAR(255) NOT NULL,
    category ENUM('Seminar', 'Workshop', 'Fest', 'Sports', 'Reunion', 'Others') DEFAULT 'Seminar',
    description TEXT,
    event_date DATE,
    event_time TIME,
    location VARCHAR(255),
    banner_image VARCHAR(255) DEFAULT 'default_event.png',
    seat_limit INT DEFAULT 0, -- ০ মানে আনলিমিটেড
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ২. ইভেন্ট রেজিস্ট্রেশন/RSVP টেবিল
CREATE TABLE IF NOT EXISTS event_participations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    user_id INT,
    status ENUM('going', 'interested') NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_participation (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- কাকে নোটিফিকেশন পাঠানো হচ্ছে
    type VARCHAR(50), -- 'event', 'message', 'reminder'
    message TEXT,
    link VARCHAR(255), -- ক্লিক করলে কোথায় যাবে
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    subject VARCHAR(255),
    suggestion_text TEXT,
    is_anonymous TINYINT(1) DEFAULT 0, -- ১ হলে নাম হাইড থাকবে
    status ENUM('new', 'reviewed', 'implemented') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE alumni_jobs MODIFY COLUMN target_dept VARCHAR(255);