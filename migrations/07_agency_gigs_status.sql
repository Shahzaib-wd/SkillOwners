ALTER TABLE agency_gigs ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER gig_id;
