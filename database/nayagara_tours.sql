-- Nayagara Tours - Database Schema
-- Database: nayagara_tours


CREATE DATABASE IF NOT EXISTS nayagara_tours
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE nayagara_tours;

-- 1. Admin Users
CREATE TABLE IF NOT EXISTS admin_users (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100)  NOT NULL,
  email       VARCHAR(150)  NOT NULL UNIQUE,
  password    VARCHAR(255)  NOT NULL,          -- bcrypt hash
  role        ENUM('super_admin', 'admin') NOT NULL DEFAULT 'admin',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin (password: Admin@1234 — change after first login)
INSERT INTO admin_users (name, email, password, role) VALUES
('Super Admin', 'admin@nayagaratours.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');


-- 2. Tour Packages
CREATE TABLE IF NOT EXISTS packages (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(200)  NOT NULL,
  slug          VARCHAR(220)  NOT NULL UNIQUE,
  category      ENUM('cultural', 'beach', 'wildlife', 'hill', 'honeymoon', 'adventure') NOT NULL,
  duration      VARCHAR(50)   NOT NULL,          -- e.g. "5 Days / 4 Nights"
  price         DECIMAL(10,2) NOT NULL,
  old_price     DECIMAL(10,2) DEFAULT NULL,       -- for showing discount
  group_size    VARCHAR(50)   DEFAULT NULL,        -- e.g. "2–15 People"
  description   TEXT          NOT NULL,
  highlights    TEXT          DEFAULT NULL,        -- JSON array or newline-separated
  itinerary     LONGTEXT      DEFAULT NULL,        -- JSON or HTML
  inclusions    TEXT          DEFAULT NULL,
  exclusions    TEXT          DEFAULT NULL,
  cover_image   VARCHAR(300)  DEFAULT NULL,        -- file path
  is_featured   TINYINT(1)    NOT NULL DEFAULT 0,
  is_active     TINYINT(1)    NOT NULL DEFAULT 1,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- 3. Bookings (Custom Tour Requests)
CREATE TABLE IF NOT EXISTS bookings (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  package_id      INT UNSIGNED DEFAULT NULL,       -- NULL = custom tour
  full_name       VARCHAR(150) NOT NULL,
  email           VARCHAR(150) NOT NULL,
  phone           VARCHAR(30)  DEFAULT NULL,
  nationality     VARCHAR(100) DEFAULT NULL,
  adults          TINYINT UNSIGNED DEFAULT 1,
  children        TINYINT UNSIGNED DEFAULT 0,
  travel_date     DATE         DEFAULT NULL,
  duration        VARCHAR(100) DEFAULT NULL,
  special_request TEXT         DEFAULT NULL,
  status          ENUM('new', 'contacted', 'confirmed', 'cancelled') NOT NULL DEFAULT 'new',
  admin_notes     TEXT         DEFAULT NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL
) ENGINE=InnoDB;


-- 4. Contact Inquiries
CREATE TABLE IF NOT EXISTS inquiries (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name   VARCHAR(150) NOT NULL,
  email       VARCHAR(150) NOT NULL,
  phone       VARCHAR(30)  DEFAULT NULL,
  subject     VARCHAR(250) DEFAULT NULL,
  message     TEXT         NOT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- 5. Gallery
CREATE TABLE IF NOT EXISTS gallery (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) DEFAULT NULL,
  category    VARCHAR(100) DEFAULT NULL,          -- e.g. 'beaches', 'wildlife'
  image_path  VARCHAR(300) NOT NULL,
  sort_order  SMALLINT UNSIGNED DEFAULT 0,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- 6. Blog Posts
CREATE TABLE IF NOT EXISTS blog_posts (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(250) NOT NULL,
  slug          VARCHAR(270) NOT NULL UNIQUE,
  excerpt       TEXT         DEFAULT NULL,
  content       LONGTEXT     NOT NULL,
  cover_image   VARCHAR(300) DEFAULT NULL,
  category      VARCHAR(100) DEFAULT NULL,
  tags          VARCHAR(300) DEFAULT NULL,         -- comma-separated
  author_id     INT UNSIGNED NOT NULL,
  is_published  TINYINT(1)   NOT NULL DEFAULT 0,
  published_at  TIMESTAMP    DEFAULT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- 7. Reviews / Testimonials
CREATE TABLE IF NOT EXISTS reviews (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  country     VARCHAR(100) DEFAULT NULL,
  rating      TINYINT UNSIGNED NOT NULL DEFAULT 5,  -- 1–5
  review_text TEXT         NOT NULL,
  avatar      VARCHAR(300) DEFAULT NULL,
  is_approved TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- 8. Hero Banners (manage slider from admin)
CREATE TABLE IF NOT EXISTS hero_banners (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  heading     VARCHAR(250) NOT NULL,
  subheading  VARCHAR(350) DEFAULT NULL,
  image_path  VARCHAR(300) NOT NULL,
  btn_label   VARCHAR(100) DEFAULT 'Explore Now',
  btn_link    VARCHAR(300) DEFAULT '#packages',
  sort_order  SMALLINT UNSIGNED DEFAULT 0,
  is_active   TINYINT(1)  NOT NULL DEFAULT 1,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
