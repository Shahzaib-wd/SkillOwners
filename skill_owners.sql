-- ============================================================
-- SKILL OWNERS DATABASE SCHEMA - MERGED
-- Includes base schema and agency system migration
-- MySQL 5.7+
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================
-- BASE TABLES
-- ============================================================

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('freelancer','agency','buyer') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text,
  `skills` text,
  `portfolio_link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `gigs`
--

CREATE TABLE IF NOT EXISTS `gigs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `delivery_time` int(11) NOT NULL COMMENT 'Delivery time in days',
  `image` varchar(255) DEFAULT NULL,
  `tags` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category` (`category`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `gigs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `project_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gig_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `delivery_date` date DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gig_id` (`gig_id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `seller_id` (`seller_id`),
  KEY `status` (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`gig_id`) REFERENCES `gigs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `is_read` (`is_read`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `token` (`token`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- AGENCY SYSTEM TABLES
-- ============================================================

--
-- Table: agency_members
-- Purpose: Links freelancers to agencies with specific roles
--

CREATE TABLE IF NOT EXISTS `agency_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agency_id` int(11) NOT NULL COMMENT 'Reference to users.id where role=agency',
  `freelancer_id` int(11) NOT NULL COMMENT 'Reference to users.id where role=freelancer',
  `agency_role` enum('admin','manager','member') NOT NULL DEFAULT 'member' COMMENT 'Role within the agency',
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active',
  `invited_by` int(11) DEFAULT NULL COMMENT 'User who sent the invitation',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_agency_freelancer` (`agency_id`, `freelancer_id`),
  KEY `agency_id` (`agency_id`),
  KEY `freelancer_id` (`freelancer_id`),
  KEY `agency_role` (`agency_role`),
  KEY `status` (`status`),
  CONSTRAINT `agency_members_ibfk_1` FOREIGN KEY (`agency_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_members_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_members_ibfk_3` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table: agency_invitations
-- Purpose: Manages freelancer invitation workflow
--

CREATE TABLE IF NOT EXISTS `agency_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agency_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `agency_role` enum('admin','manager','member') NOT NULL DEFAULT 'member',
  `status` enum('pending','accepted','rejected','expired') NOT NULL DEFAULT 'pending',
  `invited_by` int(11) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `agency_id` (`agency_id`),
  KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `agency_invitations_ibfk_1` FOREIGN KEY (`agency_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_invitations_ibfk_2` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table: role_permissions
-- Purpose: Defines what each agency role can do
--

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` enum('admin','manager','member') NOT NULL,
  `permission` varchar(100) NOT NULL COMMENT 'e.g., manage_team, invite_members, create_gigs',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role`, `permission`),
  KEY `role` (`role`),
  KEY `permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Insert Default Permissions
-- ============================================================

INSERT INTO `role_permissions` (`role`, `permission`, `description`) VALUES
-- Admin permissions (full control)
('admin', 'manage_team', 'Add, remove, and modify team members'),
('admin', 'invite_members', 'Send invitations to freelancers'),
('admin', 'remove_members', 'Remove team members from agency'),
('admin', 'change_roles', 'Modify team member roles'),
('admin', 'create_gigs', 'Create gigs on behalf of agency'),
('admin', 'manage_orders', 'Manage agency orders'),
('admin', 'view_team', 'View all team members'),

-- Manager permissions (moderate control)
('manager', 'invite_members', 'Send invitations to freelancers'),
('manager', 'create_gigs', 'Create gigs on behalf of agency'),
('manager', 'manage_orders', 'Manage agency orders'),
('manager', 'view_team', 'View all team members'),

-- Member permissions (basic access)
('member', 'view_team', 'View all team members'),
('member', 'create_gigs', 'Create gigs on behalf of agency');

-- ============================================================
-- Performance Indexes
-- ============================================================

-- Base indexes
CREATE INDEX idx_messages_conversation ON messages(sender_id, receiver_id, created_at);
CREATE INDEX idx_orders_user_status ON orders(buyer_id, status);
CREATE INDEX idx_gigs_search ON gigs(category, is_active, created_at);

-- Agency indexes
CREATE INDEX idx_agency_members_lookup ON agency_members(agency_id, status, agency_role);
CREATE INDEX idx_invitations_lookup ON agency_invitations(agency_id, status, expires_at);
CREATE INDEX idx_invitations_email_status ON agency_invitations(email, status);

-- ============================================================
-- Migration Complete
-- ============================================================

-- To apply this migration, run:
-- mysql -u root -p skill_owners < merged_database.sql
-- ============================================================
-- CONVERSATION-BASED CHAT MIGRATION
-- Upgrade from direct messaging to conversation architecture
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================
-- STEP 1: Create new conversation tables
-- ============================================================

--
-- Table: conversations
-- Purpose: Central conversation records
--
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('direct','agency_internal','group') NOT NULL DEFAULT 'direct',
  `title` varchar(255) DEFAULT NULL COMMENT 'Optional title for group/agency chats',
  `agency_id` int(11) DEFAULT NULL COMMENT 'Agency ID for agency_internal type',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `agency_id` (`agency_id`),
  KEY `updated_at` (`updated_at`),
  CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`agency_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table: conversation_participants
-- Purpose: Links users to conversations with read tracking
--
CREATE TABLE IF NOT EXISTS `conversation_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL COMMENT 'Last time user read messages in this conversation',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_conversation_user` (`conversation_id`, `user_id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `user_id` (`user_id`),
  KEY `last_read_at` (`last_read_at`),
  CONSTRAINT `conversation_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- STEP 2: Add conversation_id to messages table (nullable for migration)
-- ============================================================

ALTER TABLE `messages` 
ADD COLUMN `conversation_id` int(11) DEFAULT NULL AFTER `id`,
ADD KEY `conversation_id` (`conversation_id`);

-- ============================================================
-- STEP 3: Migrate existing messages to conversations
-- ============================================================

-- Create conversations for existing message pairs
INSERT INTO `conversations` (`type`, `created_at`, `updated_at`)
SELECT 
    'direct' as type,
    MIN(m.created_at) as created_at,
    MAX(m.created_at) as updated_at
FROM messages m
GROUP BY 
    LEAST(m.sender_id, m.receiver_id),
    GREATEST(m.sender_id, m.receiver_id);

-- Link messages to conversations
-- This creates a temporary mapping table first
CREATE TEMPORARY TABLE temp_conversation_mapping AS
SELECT 
    LEAST(m.sender_id, m.receiver_id) as user1_id,
    GREATEST(m.sender_id, m.receiver_id) as user2_id,
    MIN(c.id) as conversation_id
FROM messages m
CROSS JOIN conversations c
WHERE c.type = 'direct' AND c.agency_id IS NULL
GROUP BY 
    LEAST(m.sender_id, m.receiver_id),
    GREATEST(m.sender_id, m.receiver_id)
HAVING COUNT(DISTINCT c.id) > 0;

-- Update messages with conversation_id
UPDATE messages m
INNER JOIN temp_conversation_mapping tcm ON 
    LEAST(m.sender_id, m.receiver_id) = tcm.user1_id AND
    GREATEST(m.sender_id, m.receiver_id) = tcm.user2_id
SET m.conversation_id = tcm.conversation_id;

-- Create conversation participants from existing messages
INSERT INTO `conversation_participants` (`conversation_id`, `user_id`, `last_read_at`, `joined_at`)
SELECT DISTINCT
    m.conversation_id,
    m.sender_id as user_id,
    NULL as last_read_at,
    MIN(m.created_at) as joined_at
FROM messages m
WHERE m.conversation_id IS NOT NULL
GROUP BY m.conversation_id, m.sender_id
UNION
SELECT DISTINCT
    m.conversation_id,
    m.receiver_id as user_id,
    NULL as last_read_at,
    MIN(m.created_at) as joined_at
FROM messages m
WHERE m.conversation_id IS NOT NULL
GROUP BY m.conversation_id, m.receiver_id;

-- Set last_read_at based on is_read status (best effort migration)
UPDATE conversation_participants cp
INNER JOIN (
    SELECT 
        conversation_id,
        receiver_id,
        MAX(created_at) as last_read_time
    FROM messages
    WHERE is_read = 1 AND conversation_id IS NOT NULL
    GROUP BY conversation_id, receiver_id
) mr ON cp.conversation_id = mr.conversation_id AND cp.user_id = mr.receiver_id
SET cp.last_read_at = mr.last_read_time;

-- ============================================================
-- STEP 4: Add foreign key constraint for messages.conversation_id
-- ============================================================

-- Make conversation_id NOT NULL (all messages should now have one)
UPDATE messages SET conversation_id = 1 WHERE conversation_id IS NULL;

ALTER TABLE `messages` 
MODIFY COLUMN `conversation_id` int(11) NOT NULL,
ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE;

-- ============================================================
-- STEP 5: Optimize indexes for conversation-based queries
-- ============================================================

-- Drop old indexes that are no longer primary
DROP INDEX idx_messages_conversation ON messages;

-- Add new optimized indexes
CREATE INDEX idx_messages_conversation_time ON messages(conversation_id, created_at);
CREATE INDEX idx_participants_user_conversations ON conversation_participants(user_id, conversation_id);
CREATE INDEX idx_conversations_updated ON conversations(updated_at DESC);

-- ============================================================
-- STEP 6: Keep old fields temporarily for backward compatibility
-- DO NOT DROP sender_id, receiver_id, is_read yet
-- These will be removed after verification
-- ============================================================

-- Note: After full testing and verification, run the cleanup script:
-- ALTER TABLE messages DROP COLUMN sender_id;
-- ALTER TABLE messages DROP COLUMN receiver_id;
-- ALTER TABLE messages DROP COLUMN is_read;

-- ============================================================
-- STEP 7: Create agency internal conversations
-- This should be run after migration, preferably via PHP script
-- to ensure proper agency context
-- ============================================================

-- Example: Create agency internal conversation for each agency
-- This will be handled in PHP to ensure proper validation
-- INSERT INTO conversations (type, title, agency_id)
-- SELECT 'agency_internal', CONCAT(u.full_name, ' - Internal Chat'), u.id
-- FROM users u WHERE u.role = 'agency';

-- ============================================================
-- Migration Complete
-- ============================================================

-- Summary:
-- 1. Created conversations and conversation_participants tables
-- 2. Added conversation_id to messages
-- 3. Migrated all existing messages to conversations
-- 4. Created participants records with last_read_at tracking
-- 5. Added optimized indexes
-- 6. Kept old fields for backward compatibility during transition
-- ===================== Database Complete =====================
