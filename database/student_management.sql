-- =====================================================================
-- Student Management System - Database Schema
-- Engine: MySQL 5.7+ / MariaDB 10.3+ (works on XAMPP default)
-- Charset: utf8mb4
-- =====================================================================

CREATE DATABASE IF NOT EXISTS student_management_system
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_management_system;

-- ---------------------------------------------------------------------
-- Table: users
-- Central authentication table for BOTH students and admins (teachers).
-- Role decides which dashboard/features a user can access.
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)        NOT NULL,
    email         VARCHAR(150)        NOT NULL UNIQUE,
    password      VARCHAR(255)        NOT NULL,          -- bcrypt hash
    role          ENUM('student','admin') NOT NULL DEFAULT 'student',
    status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: students
-- Extra profile data for users with role = 'student'
-- ---------------------------------------------------------------------
CREATE TABLE students (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    roll_no         VARCHAR(20)  NOT NULL UNIQUE,
    class           VARCHAR(20)  NOT NULL,
    section         VARCHAR(10)  DEFAULT NULL,
    phone           VARCHAR(20)  DEFAULT NULL,
    address         VARCHAR(255) DEFAULT NULL,
    dob             DATE         DEFAULT NULL,
    gender          ENUM('Male','Female','Other') DEFAULT NULL,
    profile_pic     VARCHAR(255) DEFAULT 'default.png',
    admission_date  DATE         DEFAULT NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: attendance
-- Marked by an admin/teacher for a specific student on a specific date
-- ---------------------------------------------------------------------
CREATE TABLE attendance (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id   INT UNSIGNED NOT NULL,
    date         DATE NOT NULL,
    status       ENUM('Present','Absent','Leave') NOT NULL DEFAULT 'Present',
    marked_by    INT UNSIGNED NOT NULL,       -- users.id of admin
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_att_admin   FOREIGN KEY (marked_by)  REFERENCES users(id)    ON DELETE CASCADE,
    UNIQUE KEY uniq_student_date (student_id, date)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: marks
-- Performance / exam marks per subject
-- ---------------------------------------------------------------------
CREATE TABLE marks (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id     INT UNSIGNED NOT NULL,
    subject        VARCHAR(100) NOT NULL,
    exam_type      VARCHAR(50)  NOT NULL,   -- e.g. Unit Test 1, Midterm, Final
    marks_obtained DECIMAL(5,2) NOT NULL,
    total_marks    DECIMAL(5,2) NOT NULL DEFAULT 100,
    exam_date      DATE DEFAULT NULL,
    added_by       INT UNSIGNED NOT NULL,   -- users.id of admin
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_marks_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_marks_admin   FOREIGN KEY (added_by)   REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: assignments
-- Created by admin/teacher; visible to all (or a class) of students
-- ---------------------------------------------------------------------
CREATE TABLE assignments (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(150) NOT NULL,
    description  TEXT DEFAULT NULL,
    subject      VARCHAR(100) NOT NULL,
    class        VARCHAR(20)  DEFAULT NULL,   -- target class, NULL = all
    file_path    VARCHAR(255) DEFAULT NULL,   -- optional attachment from teacher
    due_date     DATE NOT NULL,
    uploaded_by  INT UNSIGNED NOT NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_assign_admin FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: assignment_submissions
-- Student's uploaded work for a given assignment + review status
-- ---------------------------------------------------------------------
CREATE TABLE assignment_submissions (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assignment_id  INT UNSIGNED NOT NULL,
    student_id     INT UNSIGNED NOT NULL,
    file_path      VARCHAR(255) NOT NULL,
    submitted_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status         ENUM('Pending','Completed','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    remarks        VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_sub_assignment FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    CONSTRAINT fk_sub_student    FOREIGN KEY (student_id)    REFERENCES students(id)    ON DELETE CASCADE,
    UNIQUE KEY uniq_assignment_student (assignment_id, student_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: notes
-- Study material uploaded by teachers, downloadable by students
-- ---------------------------------------------------------------------
CREATE TABLE notes (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(150) NOT NULL,
    subject      VARCHAR(100) NOT NULL,
    class        VARCHAR(20)  DEFAULT NULL,
    file_path    VARCHAR(255) NOT NULL,
    uploaded_by  INT UNSIGNED NOT NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notes_admin FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: announcements
-- Broadcast messages from teachers/admin to students
-- ---------------------------------------------------------------------
CREATE TABLE announcements (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    message     TEXT NOT NULL,
    created_by  INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_announce_admin FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: notifications
-- Per-user notifications (system generated: new marks, assignment status,
-- announcements, attendance alerts, etc.)
-- ---------------------------------------------------------------------
CREATE TABLE notifications (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    message     VARCHAR(255) NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- SAMPLE DATA
-- Default password for ALL sample accounts below is:  Password@123
-- (hash generated with PHP password_hash / bcrypt)
-- =====================================================================

INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@school.com', '$2b$10$KOaXVP0TRW0D31FHLIGV4.avj6SXdyoGISvcE9uNwCI3zpsEnbNaS', 'admin'),
('John Carter',  'john@student.com', '$2b$10$KOaXVP0TRW0D31FHLIGV4.avj6SXdyoGISvcE9uNwCI3zpsEnbNaS', 'student'),
('Emily Stone',  'emily@student.com','$2b$10$KOaXVP0TRW0D31FHLIGV4.avj6SXdyoGISvcE9uNwCI3zpsEnbNaS', 'student');

INSERT INTO students (user_id, roll_no, class, section, phone, address, dob, gender, admission_date) VALUES
(2, 'R2026001', '10', 'A', '9876543210', '221B Baker Street', '2009-05-14', 'Male',   '2024-06-01'),
(3, 'R2026002', '10', 'A', '9876500000', '12 Elm Street',     '2009-08-22', 'Female', '2024-06-01');

INSERT INTO attendance (student_id, date, status, marked_by) VALUES
(1, CURDATE() - INTERVAL 2 DAY, 'Present', 1),
(1, CURDATE() - INTERVAL 1 DAY, 'Absent',  1),
(2, CURDATE() - INTERVAL 2 DAY, 'Present', 1),
(2, CURDATE() - INTERVAL 1 DAY, 'Present', 1);

INSERT INTO marks (student_id, subject, exam_type, marks_obtained, total_marks, exam_date, added_by) VALUES
(1, 'Mathematics', 'Unit Test 1', 88, 100, CURDATE() - INTERVAL 10 DAY, 1),
(1, 'Science',     'Unit Test 1', 76, 100, CURDATE() - INTERVAL 10 DAY, 1),
(2, 'Mathematics', 'Unit Test 1', 92, 100, CURDATE() - INTERVAL 10 DAY, 1);

INSERT INTO assignments (title, description, subject, class, due_date, uploaded_by) VALUES
('Algebra Worksheet', 'Solve chapter 3 exercises 1-20', 'Mathematics', '10', CURDATE() + INTERVAL 7 DAY, 1),
('Lab Report: Photosynthesis', 'Write a lab report based on the experiment', 'Science', '10', CURDATE() + INTERVAL 5 DAY, 1);

INSERT INTO notes (title, subject, class, file_path, uploaded_by) VALUES
('Chapter 3 - Algebra Notes', 'Mathematics', '10', 'notes/sample_notes.pdf', 1);

INSERT INTO announcements (title, message, created_by) VALUES
('Welcome to New Semester', 'Classes begin Monday. Please check your timetable on the notice board.', 1);

INSERT INTO notifications (user_id, message) VALUES
(2, 'Your Unit Test 1 marks have been published.'),
(3, 'Your Unit Test 1 marks have been published.');
