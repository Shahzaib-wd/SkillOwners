-- ============================================================
-- SKILL OWNERS — COMPLETE DATABASE SCHEMA
-- Production Database: u382083643_skillowners
-- Domain: skillowners.com
-- Single-file installation for MySQL 5.7+ / MariaDB 10.3+
-- Last updated: 2026-02-18
-- ============================================================

SET SQL_MODE  = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- ============================================================
-- 1. USERS
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
  `id`                INT(11)       NOT NULL AUTO_INCREMENT,
  `email`             VARCHAR(255)  NOT NULL,
  `google_id`         VARCHAR(255)  DEFAULT NULL,
  `password`          VARCHAR(255)  NOT NULL,
  `full_name`         VARCHAR(255)  NOT NULL,
  `role`              ENUM('freelancer','agency','buyer') NOT NULL,
  `profile_image`     VARCHAR(255)  DEFAULT NULL,
  `professional_title` VARCHAR(255) DEFAULT NULL,
  `bio`               TEXT,
  `skills`            TEXT,
  `portfolio_link`    VARCHAR(500)  DEFAULT NULL,
  `location`          VARCHAR(255)  DEFAULT NULL,
  `phone`             VARCHAR(50)   DEFAULT NULL,
  `experience_years`  INT(11)       DEFAULT NULL,
  `linkedin_url`      VARCHAR(500)  DEFAULT NULL,
  `twitter_url`       VARCHAR(500)  DEFAULT NULL,
  `github_url`        VARCHAR(500)  DEFAULT NULL,
  `languages`         TEXT,
  `is_official`       TINYINT(1)    NOT NULL DEFAULT 0,
  `is_active`         TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email`     (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `role`       (`role`),
  KEY `is_active`  (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. GIGS
-- ============================================================

CREATE TABLE IF NOT EXISTS `gigs` (
  `id`              INT(11)        NOT NULL AUTO_INCREMENT,
  `user_id`         INT(11)        NOT NULL,
  `title`           VARCHAR(255)   NOT NULL,
  `description`     TEXT           NOT NULL,
  `category`        VARCHAR(100)   NOT NULL,
  `price`           DECIMAL(10,2)  NOT NULL,
  `delivery_time`   INT(11)        NOT NULL COMMENT 'Delivery time in days',
  `image`           VARCHAR(255)   DEFAULT NULL,
  `tags`            TEXT,
  `impressions`     INT            DEFAULT 0,
  `clicks`          INT            DEFAULT 0,
  `is_active`       TINYINT(1)     NOT NULL DEFAULT 1,
  `created_at`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id`    (`user_id`),
  KEY `category`   (`category`),
  KEY `is_active`  (`is_active`),
  CONSTRAINT `gigs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 3. PROJECTS (Portfolio)
-- ============================================================

CREATE TABLE IF NOT EXISTS `projects` (
  `id`            INT(11)        NOT NULL AUTO_INCREMENT,
  `user_id`       INT(11)        NOT NULL,
  `title`         VARCHAR(255)   NOT NULL,
  `description`   TEXT           NOT NULL,
  `image`         VARCHAR(255)   DEFAULT NULL,
  `project_url`   VARCHAR(500)   DEFAULT NULL,
  `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. ORDERS
-- ============================================================

CREATE TABLE IF NOT EXISTS `orders` (
  `id`               INT(11)        NOT NULL AUTO_INCREMENT,
  `gig_id`           INT(11)        NOT NULL,
  `buyer_id`         INT(11)        NOT NULL,
  `seller_id`        INT(11)        NOT NULL,
  `amount`           DECIMAL(10,2)  NOT NULL,
  `status`           ENUM('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `delivery_date`    DATE           DEFAULT NULL,
  `buyer_confirmed`  TINYINT(1)     NOT NULL DEFAULT 0,
  `seller_confirmed` TINYINT(1)     NOT NULL DEFAULT 0,
  `completed_at`     TIMESTAMP      NULL DEFAULT NULL,
  `created_at`       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gig_id`     (`gig_id`),
  KEY `buyer_id`   (`buyer_id`),
  KEY `seller_id`  (`seller_id`),
  KEY `status`     (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`gig_id`)    REFERENCES `gigs`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`buyer_id`)  REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 5. REVIEWS
-- ============================================================

CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT(11)    NOT NULL AUTO_INCREMENT,
  `order_id`    INT(11)    NOT NULL,
  `gig_id`      INT(11)    NOT NULL,
  `buyer_id`    INT(11)    NOT NULL,
  `rating`      INT(1)     NOT NULL,
  `comment`     TEXT       NOT NULL,
  `created_at`  TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_order_review` (`order_id`),
  KEY `gig_id`   (`gig_id`),
  KEY `buyer_id` (`buyer_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`gig_id`)   REFERENCES `gigs`   (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`buyer_id`) REFERENCES `users`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 6. CONVERSATIONS (Chat System)
-- ============================================================

CREATE TABLE IF NOT EXISTS `conversations` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `type`        ENUM('direct','agency_internal','group') NOT NULL DEFAULT 'direct',
  `direct_key`  VARCHAR(50)   DEFAULT NULL COMMENT 'Normalized key e.g. 2_5 for dedup',
  `title`       VARCHAR(255)  DEFAULT NULL COMMENT 'Optional title for group/agency chats',
  `agency_id`   INT(11)       DEFAULT NULL COMMENT 'Agency ID for agency_internal type',
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_direct_key` (`direct_key`),
  KEY `type`       (`type`),
  KEY `agency_id`  (`agency_id`),
  KEY `updated_at` (`updated_at`),
  CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`agency_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 7. CONVERSATION PARTICIPANTS
-- ============================================================

CREATE TABLE IF NOT EXISTS `conversation_participants` (
  `id`               INT(11)    NOT NULL AUTO_INCREMENT,
  `conversation_id`  INT(11)    NOT NULL,
  `user_id`          INT(11)    NOT NULL,
  `last_read_at`     TIMESTAMP  NULL DEFAULT NULL COMMENT 'Last time user read messages',
  `joined_at`        TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_conversation_user` (`conversation_id`, `user_id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `user_id`         (`user_id`),
  KEY `last_read_at`    (`last_read_at`),
  CONSTRAINT `conversation_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_participants_ibfk_2` FOREIGN KEY (`user_id`)          REFERENCES `users`          (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 8. MESSAGES
-- ============================================================

CREATE TABLE IF NOT EXISTS `messages` (
  `id`               INT(11)    NOT NULL AUTO_INCREMENT,
  `conversation_id`  INT(11)    NOT NULL,
  `sender_id`        INT(11)    NOT NULL,
  `receiver_id`      INT(11)    NULL,
  `message`          TEXT       NOT NULL,
  `is_read`          TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `sender_id`       (`sender_id`),
  KEY `receiver_id`     (`receiver_id`),
  KEY `is_read`         (`is_read`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`)       REFERENCES `users`         (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`)     REFERENCES `users`         (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 9. PASSWORD RESETS
-- ============================================================

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `email`       VARCHAR(255)  NOT NULL,
  `token`       VARCHAR(255)  NOT NULL,
  `expires_at`  TIMESTAMP     NOT NULL,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email`      (`email`),
  KEY `token`      (`token`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 10. AGENCY MEMBERS
-- ============================================================

CREATE TABLE IF NOT EXISTS `agency_members` (
  `id`             INT(11)  NOT NULL AUTO_INCREMENT,
  `agency_id`      INT(11)  NOT NULL COMMENT 'Reference to users.id where role=agency',
  `freelancer_id`  INT(11)  NOT NULL COMMENT 'Reference to users.id where role=freelancer',
  `agency_role`    ENUM('admin','manager','member') NOT NULL DEFAULT 'member',
  `status`         ENUM('active','inactive','pending') NOT NULL DEFAULT 'active',
  `invited_by`     INT(11)  DEFAULT NULL,
  `joined_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_agency_freelancer` (`agency_id`, `freelancer_id`),
  KEY `agency_id`      (`agency_id`),
  KEY `freelancer_id`  (`freelancer_id`),
  KEY `agency_role`    (`agency_role`),
  KEY `status`         (`status`),
  CONSTRAINT `agency_members_ibfk_1` FOREIGN KEY (`agency_id`)     REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_members_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_members_ibfk_3` FOREIGN KEY (`invited_by`)    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 11. AGENCY INVITATIONS
-- ============================================================

CREATE TABLE IF NOT EXISTS `agency_invitations` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `agency_id`   INT(11)       NOT NULL,
  `email`       VARCHAR(255)  NOT NULL,
  `token`       VARCHAR(64)   NOT NULL,
  `agency_role` ENUM('admin','manager','member') NOT NULL DEFAULT 'member',
  `status`      ENUM('pending','accepted','rejected','expired') NOT NULL DEFAULT 'pending',
  `invited_by`  INT(11)       NOT NULL,
  `expires_at`  TIMESTAMP     NOT NULL,
  `accepted_at` TIMESTAMP     NULL DEFAULT NULL,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `agency_id`  (`agency_id`),
  KEY `email`      (`email`),
  KEY `status`     (`status`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `agency_invitations_ibfk_1` FOREIGN KEY (`agency_id`)  REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_invitations_ibfk_2` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 12. AGENCY GIGS
-- ============================================================

CREATE TABLE IF NOT EXISTS `agency_gigs` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `agency_id`      INT(11) NOT NULL COMMENT 'Reference to users.id where role=agency',
  `freelancer_id`  INT(11) NOT NULL COMMENT 'Reference to users.id where role=freelancer',
  `gig_id`         INT(11) NOT NULL COMMENT 'Reference to gigs.id',
  `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_agency_freelancer` (`agency_id`, `freelancer_id`),
  UNIQUE KEY `unique_gig` (`gig_id`),
  KEY `agency_id`     (`agency_id`),
  KEY `freelancer_id` (`freelancer_id`),
  KEY `gig_id`        (`gig_id`),
  KEY `status`        (`status`),
  CONSTRAINT `agency_gigs_ibfk_1` FOREIGN KEY (`agency_id`)     REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_gigs_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_gigs_ibfk_3` FOREIGN KEY (`gig_id`)        REFERENCES `gigs`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 13. ROLE PERMISSIONS
-- ============================================================

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `role`        ENUM('admin','manager','member') NOT NULL,
  `permission`  VARCHAR(100)  NOT NULL COMMENT 'e.g. manage_team, invite_members, create_gigs',
  `description` VARCHAR(255)  DEFAULT NULL,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role`, `permission`),
  KEY `role`       (`role`),
  KEY `permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 14. REPORTS (Abuse / Content Moderation)
-- ============================================================

CREATE TABLE IF NOT EXISTS `reports` (
  `id`               INT(11)    NOT NULL AUTO_INCREMENT,
  `reporter_id`      INT(11)    NOT NULL,
  `reported_id`      INT(11)    NOT NULL,
  `conversation_id`  INT(11)    NOT NULL,
  `message_id`       INT(11)    NULL,
  `reason`           TEXT       NOT NULL,
  `status`           ENUM('pending','reviewed','resolved','dismissed') DEFAULT 'pending',
  `created_at`       TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`)     REFERENCES `users`         (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_id`)     REFERENCES `users`         (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 15. CATEGORIES
-- ============================================================

CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)  NOT NULL,
  `slug`        VARCHAR(100)  NOT NULL,
  `icon`        VARCHAR(50)   DEFAULT NULL COMMENT 'FontAwesome class',
  `is_active`   TINYINT(1)    DEFAULT 1,
  `created_at`  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 16. CONTACT MESSAGES
-- ============================================================

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255)  NOT NULL,
  `email`       VARCHAR(255)  NOT NULL,
  `subject`     VARCHAR(255)  NOT NULL,
  `message`     TEXT          NOT NULL,
  `status`      ENUM('pending','read','replied') DEFAULT 'pending',
  `created_at`  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 17. GIG ANALYTICS
-- ============================================================

CREATE TABLE IF NOT EXISTS `gig_analytics` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `gig_id`      INT(11)      NOT NULL,
  `user_id`     INT(11)      NULL,
  `ip_address`  VARCHAR(45)  NOT NULL,
  `type`        ENUM('impression','click') NOT NULL,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gig_type`  (`gig_id`, `type`),
  KEY `idx_user_ip`   (`user_id`, `ip_address`),
  CONSTRAINT `gig_analytics_ibfk_1` FOREIGN KEY (`gig_id`) REFERENCES `gigs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- SEED DATA: Default Role Permissions
-- ============================================================

INSERT INTO `role_permissions` (`role`, `permission`, `description`) VALUES
-- Admin (full control)
('admin', 'manage_team',     'Add, remove, and modify team members'),
('admin', 'invite_members',  'Send invitations to freelancers'),
('admin', 'remove_members',  'Remove team members from agency'),
('admin', 'change_roles',    'Modify team member roles'),
('admin', 'create_gigs',     'Create gigs on behalf of agency'),
('admin', 'manage_orders',   'Manage agency orders'),
('admin', 'view_team',       'View all team members'),
-- Manager (moderate control)
('manager', 'invite_members', 'Send invitations to freelancers'),
('manager', 'create_gigs',    'Create gigs on behalf of agency'),
('manager', 'manage_orders',  'Manage agency orders'),
('manager', 'view_team',      'View all team members'),
-- Member (basic access)
('member', 'view_team',   'View all team members'),
('member', 'create_gigs', 'Create gigs on behalf of agency');


-- ============================================================
-- SEED DATA: Default Categories
-- ============================================================

INSERT INTO `categories` (`name`, `slug`, `icon`) VALUES
('Graphics & Design',       'graphics-design',       'fas fa-pencil-ruler'),
('Digital Marketing',        'digital-marketing',     'fas fa-bullhorn'),
('Writing & Translation',   'writing-translation',   'fas fa-pen-nib'),
('Video & Animation',       'video-animation',        'fas fa-video'),
('Music & Audio',            'music-audio',            'fas fa-music'),
('Programming & Tech',      'programming-tech',       'fas fa-code'),
('Business',                 'business',               'fas fa-briefcase'),
('Lifestyle',                'lifestyle',              'fas fa-heart');


-- ============================================================
-- SEED DATA: Default Admin User
-- ============================================================

INSERT INTO `users` (`email`, `password`, `full_name`, `role`, `is_active`) VALUES
('info@skillowners.com', '$2y$10$UaQJODKFo5C0PBrtkUg/WungtyT0WCUw4gRzdbX7SnHrLNxauO3lS', 'Admin', 'buyer', 1);


-- ============================================================
-- PERFORMANCE INDEXES
-- ============================================================

CREATE INDEX idx_messages_conversation_time     ON messages(conversation_id, created_at);
CREATE INDEX idx_participants_user_conversations ON conversation_participants(user_id, conversation_id);
CREATE INDEX idx_orders_user_status             ON orders(buyer_id, status);
CREATE INDEX idx_gigs_search                    ON gigs(category, is_active, created_at);
CREATE INDEX idx_agency_members_lookup          ON agency_members(agency_id, status, agency_role);
CREATE INDEX idx_invitations_lookup             ON agency_invitations(agency_id, status, expires_at);
CREATE INDEX idx_invitations_email_status       ON agency_invitations(email, status);


-- ============================================================
-- DATABASE COMPLETE
-- ============================================================
-- To install:  mysql -u root -p < skill_owners.sql
-- To import via phpMyAdmin: Select this file in the Import tab.
-- ============================================================
