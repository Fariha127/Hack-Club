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
