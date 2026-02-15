-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50), -- FontAwesome class
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert some default categories
INSERT INTO categories (name, slug, icon) VALUES 
('Graphics & Design', 'graphics-design', 'fas fa-pencil-ruler'),
('Digital Marketing', 'digital-marketing', 'fas fa-bullhorn'),
('Writing & Translation', 'writing-translation', 'fas fa-pen-nib'),
('Video & Animation', 'video-animation', 'fas fa-video'),
('Music & Audio', 'music-audio', 'fas fa-music'),
('Programming & Tech', 'programming-tech', 'fas fa-code'),
('Business', 'business', 'fas fa-briefcase'),
('Lifestyle', 'lifestyle', 'fas fa-heart');

-- Optional: Add is_featured to gigs if not exists
-- ALTER TABLE gigs ADD COLUMN is_featured TINYINT(1) DEFAULT 0;
-- CALL AddColumnIfNotExists('gigs', 'is_featured', 'TINYINT(1) DEFAULT 0');
