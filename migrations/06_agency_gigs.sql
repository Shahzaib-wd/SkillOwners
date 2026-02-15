-- ============================================================
-- AGENCY GIGS - Freelancer Gig Contribution to Agency
-- Each freelancer member can contribute 1 gig to the agency
-- Agency owner/admin must approve before it shows in services
-- ============================================================

CREATE TABLE IF NOT EXISTS `agency_gigs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agency_id` int(11) NOT NULL COMMENT 'Reference to users.id where role=agency',
  `freelancer_id` int(11) NOT NULL COMMENT 'Reference to users.id where role=freelancer',
  `gig_id` int(11) NOT NULL COMMENT 'Reference to gigs.id',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_agency_freelancer` (`agency_id`, `freelancer_id`),
  UNIQUE KEY `unique_gig` (`gig_id`),
  KEY `agency_id` (`agency_id`),
  KEY `freelancer_id` (`freelancer_id`),
  KEY `gig_id` (`gig_id`),
  KEY `status` (`status`),
  CONSTRAINT `agency_gigs_ibfk_1` FOREIGN KEY (`agency_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_gigs_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agency_gigs_ibfk_3` FOREIGN KEY (`gig_id`) REFERENCES `gigs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
