-- ============================================================
-- HACK KUET - Admin Database Schema
-- Hardware Acceleration Club of KUET
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `hack_kuet`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `hack_kuet`;

-- ─── Admins ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admins` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100)    NOT NULL,
  `email`         VARCHAR(150)    NOT NULL,
  `password_hash` VARCHAR(255)    NOT NULL,
  `role`          ENUM('super_admin','admin') NOT NULL DEFAULT 'admin',
  `last_login`    TIMESTAMP       NULL DEFAULT NULL,
  `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Membership Applications ──────────────────────────────────
CREATE TABLE IF NOT EXISTS `membership_applications` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name`        VARCHAR(100) NOT NULL,
  `email`            VARCHAR(150) NOT NULL,
  `department`       VARCHAR(100) NOT NULL,
  `year`             VARCHAR(20)  NOT NULL,
  `area_of_interest` TEXT         NOT NULL,
  `why_join`         TEXT         NOT NULL,
  `status`           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at`      TIMESTAMP    NULL DEFAULT NULL,
  `reviewed_by`      INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_app_status` (`status`),
  CONSTRAINT `fk_app_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Members (approved applicants) ───────────────────────────
CREATE TABLE IF NOT EXISTS `members` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id`   INT UNSIGNED NULL DEFAULT NULL,
  `full_name`        VARCHAR(100) NOT NULL,
  `email`            VARCHAR(150) NOT NULL,
  `department`       VARCHAR(100) NOT NULL,
  `year`             VARCHAR(20)  NOT NULL,
  `area_of_interest` TEXT         NOT NULL,
  `joined_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_member_email` (`email`),
  UNIQUE KEY `uq_member_app` (`application_id`),
  CONSTRAINT `fk_member_app` FOREIGN KEY (`application_id`) REFERENCES `membership_applications` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Blogs ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `blogs` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(200) NOT NULL,
  `slug`         VARCHAR(220) NOT NULL,
  `author_name`  VARCHAR(100) NOT NULL,
  `author_email` VARCHAR(150) NULL DEFAULT NULL,
  `author_university` VARCHAR(150) NULL DEFAULT NULL,
  `author_department` VARCHAR(100) NULL DEFAULT NULL,
  `content`      LONGTEXT     NOT NULL,
  `excerpt`      TEXT         NULL DEFAULT NULL,
  `cover_image`  VARCHAR(255) NULL DEFAULT NULL,
  `tags`         VARCHAR(500) NULL DEFAULT NULL,
  `status`       ENUM('pending','published','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published_at` TIMESTAMP    NULL DEFAULT NULL,
  `reviewed_by`  INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_blog_slug` (`slug`),
  KEY `idx_blog_status` (`status`),
  CONSTRAINT `fk_blog_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `blogs` (`title`, `slug`, `author_name`, `author_email`, `author_university`, `author_department`, `content`, `excerpt`, `cover_image`, `tags`, `status`, `published_at`)
VALUES
('Common MCU Mistakes New Builders Make', 'mcu-mistakes', 'Sadia Jahan, CSE', NULL, 'Shahjalal University of Science and Technology', 'CSE', 'A practical note on the power, reset, pin mapping, and debugging checks new embedded builders should make before blaming firmware.', 'The small setup errors that waste the most time on early embedded projects, and how we catch them faster.', 'mcu-mistakes-1.jpg', 'MCU Basics, Debugging, Power', 'published', '2025-11-01 09:00:00'),
('PCB Review Checklist We Actually Use', 'pcb-review-checklist', 'Marjan Islam, EEE', NULL, 'Khulna University of Engineering and Technology', 'EEE', 'A pre-fabrication checklist for reviewing current paths, connector orientation, footprints, BOMs, and assembly risks before a PCB leaves the bench.', 'A short pre-fabrication routine that catches layout, footprint, and assembly problems before the board leaves the bench.', 'pcb-review-checklist-1.jpg', 'PCB Design, Assembly, Checklist', 'published', '2026-03-01 09:00:00'),
('Debugging Sensor Noise in Real Labs', 'sensor-noise-debugging', 'Abdur Rahim, CSE', NULL, 'Chittagong University of Engineering and Technology', 'CSE', 'A troubleshooting guide for separating electrical noise from software problems by measuring raw signals, checking grounding, and reviewing sampling behavior.', 'How we separate electrical problems from software problems when the signal starts jumping around.', 'sensor-noise-debugging-1.jpg', 'Sensors, Sampling, Filtering', 'published', '2026-01-01 09:00:00'),
('How We Tune PID for Fast Tracks', 'pid-tuning', 'Sohan Ahmed, CSE', NULL, 'Khulna University of Engineering and Technology', 'CSE', 'A practical tuning routine for line followers, including proportional response, careful integral correction, and conservative derivative gain.', 'A practical tuning routine for line followers that need to stay quick on changing floors, batteries, and lighting.', 'pid-tuning-1.jpg', 'Control Systems, Tuning, Robotics', 'published', '2026-04-01 09:00:00'),
('Firmware Architecture for Student Teams', 'firmware-architecture', 'Abed Hasan, EEE', NULL, 'Khulna University of Engineering and Technology', 'EEE', 'A simple firmware layering approach that separates drivers, services, and application logic so student teams can review and maintain embedded code together.', 'A simple structure that keeps embedded projects readable when multiple people are touching the same codebase.', 'firmware-architecture-1.jpg', 'Embedded C, Architecture, Code Review', 'published', '2026-02-01 09:00:00'),
('From Prototype to Demo Day in 10 Days', 'demo-day-sprint', 'Sumaiya Islam, CSE', NULL, 'Khulna University of Engineering and Technology', 'CSE', 'A short sprint workflow for preparing reliable demos by focusing the story, assigning owners, integrating early, and rehearsing under time pressure.', 'A short sprint workflow for teams that need a reliable presentation without pretending the project is perfect.', 'demo-day-sprint-1.jpg', 'Planning, Integration, Demo Prep', 'published', '2025-12-01 09:00:00')
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `author_name` = VALUES(`author_name`),
  `author_university` = VALUES(`author_university`),
  `author_department` = VALUES(`author_department`),
  `content` = VALUES(`content`),
  `excerpt` = VALUES(`excerpt`),
  `cover_image` = VALUES(`cover_image`),
  `tags` = VALUES(`tags`),
  `status` = VALUES(`status`),
  `published_at` = VALUES(`published_at`);

SOURCE database/blog_seed_content.sql;

-- ─── Executives ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `executives` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100) NOT NULL,
  `role`          VARCHAR(100) NOT NULL,
  `category`      ENUM('moderator','president','vice_president','secretary','executive','member') NOT NULL DEFAULT 'member',
  `department`    VARCHAR(100) NULL DEFAULT NULL,
  `email`         VARCHAR(150) NULL DEFAULT NULL,
  `photo`         VARCHAR(255) NULL DEFAULT NULL,
  `bio`           TEXT         NULL DEFAULT NULL,
  `display_order` INT          NOT NULL DEFAULT 0,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_executive_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `executives` (`name`, `role`, `category`, `department`, `photo`, `display_order`, `is_active`)
VALUES
('Dr. Muhammad Sheikh Sadi', 'Moderator', 'moderator', 'PhD Mentor', 'Dr. Muhammad Sheikh Sadi-Moderator.jpg', 1, 1),
('Dr. Md. Milon Islam', 'Moderator', 'moderator', 'PhD Mentor', 'Dr. Md. Milon Islam-Moderator.jpg', 2, 1),
('Md. Repon Islam', 'Moderator', 'moderator', 'Mentor', 'Md. Repon Islam- Moderator.jpg', 3, 1),
('Md. Badiuzzaman Shuvo', 'Moderator', 'moderator', 'Mentor', 'Md. Badiuzzaman Shuvo-Moderator.jpg', 4, 1),
('Amit Kairy', 'President (Administration)', 'president', 'Administration', 'Amit Kairy- President(Administration).jpg', 5, 1),
('Faysal Mahmud', 'President (Technical Affairs)', 'president', 'Technical Affairs', 'Faysal Mahmud - President(Technical Affairs).jpg', 6, 1),
('Mahmudul Islam Shawcha', 'Vice President', 'vice_president', 'Executive Panel', 'Mahmudul Islam Shawcha -Vice President.jpg', 7, 1),
('Hanium Maria Joli', 'Vice President', 'vice_president', 'Executive Panel', 'Hanium Maria Joli- Vice President.jpg', 8, 1)
ON DUPLICATE KEY UPDATE
  `role` = VALUES(`role`),
  `category` = VALUES(`category`),
  `department` = VALUES(`department`),
  `photo` = VALUES(`photo`),
  `display_order` = VALUES(`display_order`),
  `is_active` = VALUES(`is_active`);

-- ─── Projects ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `projects` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(200) NOT NULL,
  `slug`             VARCHAR(220) NOT NULL,
  `description`      TEXT         NOT NULL,
  `cover_image`      VARCHAR(255) NULL DEFAULT NULL,
  `team_members`     VARCHAR(500) NULL DEFAULT NULL,
  `mentors`          VARCHAR(500) NULL DEFAULT NULL,
  `technologies`     VARCHAR(500) NULL DEFAULT NULL,
  `project_status`   ENUM('ongoing','completed','featured') NOT NULL DEFAULT 'ongoing',
  `display_order`    INT          NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by`       INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_slug` (`slug`),
  CONSTRAINT `fk_project_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`title`, `slug`, `description`, `cover_image`, `team_members`, `mentors`, `technologies`, `project_status`, `display_order`)
VALUES
('Alcohol Detection Mechanism', 'alcohol-detection-mechanism', 'A sensor-based safety prototype that detects alcohol presence and triggers a clear warning response.', 'alcohol-detection-mechanism.jpg', 'Rafiul Islam - EEE, Nusrat Jahan - CSE, Tanvir Ahmed - ECE', 'Dr. Md. Milon Islam, Md. Badiuzzaman Shuvo', 'MQ Sensor, Arduino, Buzzer, Embedded C', 'completed', 1),
('Automated Hand', 'automated-hand', 'A robotic hand prototype that demonstrates servo-driven finger movement and basic assistive automation.', 'automated-hand.jpg', 'Maliha Rahman - ME, Shakil Hossain - EEE, Arman Kabir - CSE', 'Dr. Muhammad Sheikh Sadi, Mahmudul Islam Shawcha', 'Servo Motor, Arduino, Mechanical Linkage, Robotics', 'featured', 2),
('Digital Attendance System', 'digital-attendance-system', 'A digital attendance prototype for faster class or event check-in using embedded identification logic.', 'digital-attendance-system.jpg', 'Samiul Karim - CSE, Ishrat Jahan - CSE, Mahin Rahman - EEE', 'Amit Kairy, Faysal Mahmud', 'RFID, Microcontroller, Database, Web Dashboard', 'completed', 3),
('Gas and Humidity Detector', 'gas-and-humidity-detector', 'An environmental monitoring prototype that tracks gas presence and humidity changes for safer indoor spaces.', 'gas-and-humidity-detector.jpg', 'Tasnim Akter - EEE, Rakib Hasan - ECE, Farhan Noor - CSE', 'Dr. Md. Milon Islam, Hanium Maria Joli', 'DHT Sensor, Gas Sensor, Arduino, Alert System', 'completed', 4),
('Line Following Robot', 'line-following-robot', 'A compact autonomous robot that detects a track line and follows it using sensor feedback and motor control.', 'line-following-robot.jpg', 'Sohan Ahmed - CSE, Priya Saha - EEE, Naimul Islam - ME', 'Faysal Mahmud, Mahmudul Islam Shawcha', 'IR Sensor, Motor Driver, PID Control, Robotics', 'featured', 5),
('Obstacle Avoiding Robot', 'obstacle-avoiding-robot', 'An autonomous rover that detects nearby obstacles and changes direction to keep moving safely.', 'obstacle-avoiding-robot.jpg', 'Abrar Hossain - EEE, Sumaiya Islam - CSE, Rifat Chowdhury - ECE', 'Md. Repon Islam, Hanium Maria Joli', 'Ultrasonic Sensor, Motor Driver, Arduino, Autonomous Rover', 'completed', 6)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `description` = VALUES(`description`),
  `cover_image` = VALUES(`cover_image`),
  `team_members` = VALUES(`team_members`),
  `mentors` = VALUES(`mentors`),
  `technologies` = VALUES(`technologies`),
  `project_status` = VALUES(`project_status`),
  `display_order` = VALUES(`display_order`);

-- ─── Events ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `events` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`             VARCHAR(200) NOT NULL,
  `slug`              VARCHAR(220) NOT NULL,
  `description`       TEXT         NOT NULL,
  `cover_image`       VARCHAR(255) NULL DEFAULT NULL,
  `event_date`        DATE         NOT NULL,
  `event_time`        TIME         NULL DEFAULT NULL,
  `location`          VARCHAR(300) NULL DEFAULT NULL,
  `event_type`        ENUM('workshop','competition','seminar','meetup','other') NOT NULL DEFAULT 'other',
  `status`            ENUM('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by`        INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_slug` (`slug`),
  KEY `idx_event_date` (`event_date`),
  CONSTRAINT `fk_event_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Contact Info ─────────────────────────────────────────────
SOURCE database/event_seed_content.sql;

CREATE TABLE IF NOT EXISTS `club_activities` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`             VARCHAR(200) NOT NULL,
  `slug`              VARCHAR(220) NOT NULL,
  `activity_date`     DATE         NOT NULL,
  `description`       TEXT         NOT NULL,
  `content`           LONGTEXT     NOT NULL,
  `cover_image`       VARCHAR(255) NULL DEFAULT NULL,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_club_activity_slug` (`slug`),
  KEY `idx_club_activity_date` (`activity_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SOURCE database/activity_seed_content.sql;

CREATE TABLE IF NOT EXISTS `contact_info` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key_name`   VARCHAR(100) NOT NULL,
  `label`      VARCHAR(100) NOT NULL,
  `value`      TEXT         NOT NULL,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_contact_key` (`key_name`),
  CONSTRAINT `fk_contact_admin` FOREIGN KEY (`updated_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name`    VARCHAR(100) NOT NULL,
  `email`        VARCHAR(150) NOT NULL,
  `message`      TEXT         NOT NULL,
  `status`       ENUM('unread','read') NOT NULL DEFAULT 'unread',
  `submitted_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at`      TIMESTAMP    NULL DEFAULT NULL,
  `read_by`      INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_contact_message_status` (`status`),
  CONSTRAINT `fk_contact_message_reader` FOREIGN KEY (`read_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;



