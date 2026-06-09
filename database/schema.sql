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

SET FOREIGN_KEY_CHECKS = 1;
