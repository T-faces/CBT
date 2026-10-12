CREATE DATABASE IF NOT EXISTS cbt_sekolah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cbt_sekolah;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(80) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 full_name VARCHAR(160) NOT NULL,
 role ENUM('admin','guru','siswa') NOT NULL DEFAULT 'siswa',
 class_id BIGINT UNSIGNED NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 last_login_at DATETIME NULL,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 INDEX idx_users_role_active(role,is_active)
) ENGINE=InnoDB;

CREATE TABLE school_classes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL UNIQUE,
 academic_year VARCHAR(20) NOT NULL DEFAULT '2026/2027',
 homeroom_teacher_id BIGINT UNSIGNED NULL,
 created_at DATETIME NULL, updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE subjects (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 code VARCHAR(30) NULL UNIQUE,
 name VARCHAR(120) NOT NULL UNIQUE,
 created_at DATETIME NULL, updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE exams (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(180) NOT NULL,
 description TEXT NULL,
 subject_id BIGINT UNSIGNED NULL,
 class_id BIGINT UNSIGNED NULL,
 duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
 start_at DATETIME NULL, end_at DATETIME NULL,
 access_pin VARCHAR(20) NULL,
 shuffle_questions TINYINT(1) NOT NULL DEFAULT 0,
 shuffle_options TINYINT(1) NOT NULL DEFAULT 0,
 max_attempts TINYINT UNSIGNED NOT NULL DEFAULT 1,
 status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
 created_by BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 CONSTRAINT fk_exams_creator FOREIGN KEY(created_by) REFERENCES users(id),
 CONSTRAINT fk_exams_subject FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
 CONSTRAINT fk_exams_class FOREIGN KEY(class_id) REFERENCES school_classes(id) ON DELETE SET NULL,
 INDEX idx_exams_status_schedule(status,start_at,end_at)
) ENGINE=InnoDB;

CREATE TABLE questions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 exam_id BIGINT UNSIGNED NOT NULL,
 question_type ENUM('mcq','essay') NOT NULL DEFAULT 'mcq',
 question_text LONGTEXT NOT NULL,
 option_a TEXT NULL, option_b TEXT NULL, option_c TEXT NULL, option_d TEXT NULL,
 correct_option ENUM('A','B','C','D') NULL,
 points DECIMAL(7,2) NOT NULL DEFAULT 1,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 CONSTRAINT fk_questions_exam FOREIGN KEY(exam_id) REFERENCES exams(id) ON DELETE CASCADE,
 INDEX idx_questions_exam(exam_id)
) ENGINE=InnoDB;

CREATE TABLE exam_attempts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 exam_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 started_at DATETIME NOT NULL,
 submitted_at DATETIME NULL,
 last_seen_at DATETIME NULL,
 status ENUM('in_progress','submitted','graded') NOT NULL DEFAULT 'in_progress',
 objective_score DECIMAL(9,2) NULL DEFAULT 0,
 essay_pending TINYINT(1) NOT NULL DEFAULT 0,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 CONSTRAINT fk_attempt_exam FOREIGN KEY(exam_id) REFERENCES exams(id) ON DELETE CASCADE,
 CONSTRAINT fk_attempt_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_attempt_exam_user(exam_id,user_id),
 INDEX idx_attempt_status(status,last_seen_at)
) ENGINE=InnoDB;

CREATE TABLE attempt_questions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 attempt_id BIGINT UNSIGNED NOT NULL,
 question_id BIGINT UNSIGNED NOT NULL,
 position_no INT UNSIGNED NOT NULL,
 option_order JSON NULL,
 created_at DATETIME NULL,
 CONSTRAINT fk_aq_attempt FOREIGN KEY(attempt_id) REFERENCES exam_attempts(id) ON DELETE CASCADE,
 CONSTRAINT fk_aq_question FOREIGN KEY(question_id) REFERENCES questions(id) ON DELETE CASCADE,
 UNIQUE KEY unique_attempt_question(attempt_id,question_id),
 UNIQUE KEY unique_attempt_position(attempt_id,position_no)
) ENGINE=InnoDB;

CREATE TABLE answers (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 attempt_id BIGINT UNSIGNED NOT NULL,
 question_id BIGINT UNSIGNED NOT NULL,
 answer_text LONGTEXT NULL,
 selected_option_key ENUM('A','B','C','D') NULL,
 is_correct TINYINT(1) NULL,
 score DECIMAL(7,2) NULL,
 created_at DATETIME NULL, updated_at DATETIME NULL,
 CONSTRAINT fk_answers_attempt FOREIGN KEY(attempt_id) REFERENCES exam_attempts(id) ON DELETE CASCADE,
 CONSTRAINT fk_answers_question FOREIGN KEY(question_id) REFERENCES questions(id) ON DELETE CASCADE,
 UNIQUE KEY unique_attempt_answer(attempt_id,question_id)
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NULL,
 event_type VARCHAR(80) NOT NULL,
 details TEXT NULL,
 ip_address VARCHAR(45) NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_logs_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_logs_user_event(user_id,event_type)
) ENGINE=InnoDB;
