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
  `image_2`      VARCHAR(255) NULL DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Projects ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `projects` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(200) NOT NULL,
  `slug`             VARCHAR(220) NOT NULL,
  `description`      TEXT         NOT NULL,
  `full_description` LONGTEXT     NULL DEFAULT NULL,
  `cover_image`      VARCHAR(255) NULL DEFAULT NULL,
  `team_members`     VARCHAR(500) NULL DEFAULT NULL,
  `mentors`          VARCHAR(500) NULL DEFAULT NULL,
  `technologies`     VARCHAR(500) NULL DEFAULT NULL,
  `github_link`      VARCHAR(500) NULL DEFAULT NULL,
  `project_status`   ENUM('ongoing','completed','featured') NOT NULL DEFAULT 'ongoing',
  `display_order`    INT          NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by`       INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_slug` (`slug`),
  CONSTRAINT `fk_project_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`title`, `slug`, `description`, `full_description`, `cover_image`, `team_members`, `mentors`, `technologies`, `project_status`, `display_order`)
VALUES
('Alcohol Detection Mechanism', 'alcohol-detection-mechanism', 'A sensor-based safety prototype that detects alcohol presence and triggers a clear warning response.', 'The Alcohol Detection Mechanism is designed as a compact safety system for situations where alcohol detection can prevent risky operation of machinery or vehicles. The prototype uses an alcohol gas sensor, a microcontroller, and alert indicators to identify unsafe alcohol levels and communicate the result quickly. The team focused on stable sensor readings, simple calibration, and clear output behavior so the system can be demonstrated and improved easily.\n\nThe main challenge was separating meaningful sensor response from noisy environmental readings. The project therefore includes a warm-up period, threshold testing, and repeated measurements before the final warning is shown. This makes the prototype more reliable than a single instant reading.\n\nFuture improvements can include data logging, better enclosure design, and integration with a lockout mechanism for practical safety applications.', 'alcohol-detection-mechanism.jpg', 'Rafiul Islam - EEE, Nusrat Jahan - CSE, Tanvir Ahmed - ECE', 'Dr. Md. Milon Islam, Md. Badiuzzaman Shuvo', 'MQ Sensor, Arduino, Buzzer, Embedded C', 'completed', 1),
('Automated Hand', 'automated-hand', 'A robotic hand prototype that demonstrates servo-driven finger movement and basic assistive automation.', 'The Automated Hand project explores how mechanical structure, servo control, and embedded programming can work together to imitate simple hand gestures. The prototype uses multiple servo motors connected to a lightweight hand frame, with a controller coordinating finger movement through programmed motion sequences.\n\nThe team built this project to understand actuation, linkage movement, and timing control. Instead of focusing only on speed, the design prioritizes repeatable motion and safe mechanical limits. Each finger movement was tested separately before being combined into full gestures.\n\nThis project can grow toward assistive robotics, gesture-controlled systems, or low-cost prosthetic research. The next steps include improving grip feedback, reducing mechanical friction, and adding sensor input for more responsive control.', 'automated-hand.jpg', 'Maliha Rahman - ME, Shakil Hossain - EEE, Arman Kabir - CSE', 'Dr. Muhammad Sheikh Sadi, Mahmudul Islam Shawcha', 'Servo Motor, Arduino, Mechanical Linkage, Robotics', 'featured', 2),
('Digital Attendance System', 'digital-attendance-system', 'A digital attendance prototype for faster class or event check-in using embedded identification logic.', 'The Digital Attendance System was built to make attendance collection faster, cleaner, and less dependent on paper sheets. The prototype records participant identity through a digital input method and stores attendance data for later review. It is useful for club events, workshops, lab sessions, and classroom environments where manual attendance wastes time.\n\nThe project emphasizes reliability and simple operation. A user should be able to check in quickly, while the system stores enough information for organizers to verify attendance later. The team considered duplicate entry prevention, readable data output, and a practical interface for repeated use.\n\nFuture versions can add a web dashboard, exportable spreadsheets, RFID or biometric modules, and stronger authentication depending on the deployment context.', 'digital-attendance-system.jpg', 'Samiul Karim - CSE, Ishrat Jahan - CSE, Mahin Rahman - EEE', 'Amit Kairy, Faysal Mahmud', 'RFID, Microcontroller, Database, Web Dashboard', 'completed', 3),
('Gas and Humidity Detector', 'gas-and-humidity-detector', 'An environmental monitoring prototype that tracks gas presence and humidity changes for safer indoor spaces.', 'The Gas and Humidity Detector combines environmental sensing with simple alert logic. It measures humidity and gas concentration, then reports unsafe or unusual conditions through visual or audible output. The system is intended for labs, rooms, storage areas, and small industrial spaces where early awareness matters.\n\nThe team focused on sensor placement, stable readings, and threshold selection. Environmental sensors can fluctuate, so the project uses repeated sampling and conservative alert behavior to reduce false alarms. The display and alert outputs were kept simple so viewers can understand the state immediately during demonstration.\n\nFuture work can include wireless reporting, long-term logging, battery optimization, and a calibrated enclosure for more accurate field use.', 'gas-and-humidity-detector.jpg', 'Tasnim Akter - EEE, Rakib Hasan - ECE, Farhan Noor - CSE', 'Dr. Md. Milon Islam, Hanium Maria Joli', 'DHT Sensor, Gas Sensor, Arduino, Alert System', 'completed', 4),
('Line Following Robot', 'line-following-robot', 'A compact autonomous robot that detects a track line and follows it using sensor feedback and motor control.', 'The Line Following Robot is a foundational robotics project that teaches sensing, control, and mechanical tuning together. The robot reads contrast from the floor using line sensors and adjusts motor speed to stay on track. Although the idea is simple, the final performance depends on careful sensor placement, balanced motor response, and clean wiring.\n\nThe team tested different speeds, sensor thresholds, and turning behavior to make the robot stable on curves and intersections. The project also introduced practical control concepts such as proportional correction and the importance of battery voltage consistency.\n\nThis project is a strong starting point for competition robotics. Future upgrades can include PID tuning, telemetry, modular chassis design, and better motor drivers for faster tracks.', 'line-following-robot.jpg', 'Sohan Ahmed - CSE, Priya Saha - EEE, Naimul Islam - ME', 'Faysal Mahmud, Mahmudul Islam Shawcha', 'IR Sensor, Motor Driver, PID Control, Robotics', 'featured', 5),
('Obstacle Avoiding Robot', 'obstacle-avoiding-robot', 'An autonomous rover that detects nearby obstacles and changes direction to keep moving safely.', 'The Obstacle Avoiding Robot demonstrates basic autonomous navigation. It uses distance sensing to detect objects in front of the rover, then adjusts motor direction to avoid collisions. The project helps new builders understand sensor feedback, motor control, and decision-making logic in a moving system.\n\nThe team designed the behavior to be predictable rather than overly complex. When an obstacle is detected, the robot stops, checks a safer direction, and turns before continuing. This makes the demonstration easy to follow and helps the team debug one behavior at a time.\n\nFuture improvements can include smoother path planning, multiple sensors for wider coverage, speed control, and mapping logic for more advanced autonomous movement.', 'obstacle-avoiding-robot.jpg', 'Abrar Hossain - EEE, Sumaiya Islam - CSE, Rifat Chowdhury - ECE', 'Md. Repon Islam, Hanium Maria Joli', 'Ultrasonic Sensor, Motor Driver, Arduino, Autonomous Rover', 'completed', 6)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `description` = VALUES(`description`),
  `full_description` = VALUES(`full_description`),
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
  `full_description`  LONGTEXT     NULL DEFAULT NULL,
  `cover_image`       VARCHAR(255) NULL DEFAULT NULL,
  `event_date`        DATE         NOT NULL,
  `event_time`        TIME         NULL DEFAULT NULL,
  `location`          VARCHAR(300) NULL DEFAULT NULL,
  `event_type`        ENUM('workshop','competition','seminar','meetup','other') NOT NULL DEFAULT 'other',
  `status`            ENUM('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `registration_link` VARCHAR(500) NULL DEFAULT NULL,
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
  `gallery_images`    LONGTEXT     NULL DEFAULT NULL,
  `registration_link` VARCHAR(500) NULL DEFAULT NULL,
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
