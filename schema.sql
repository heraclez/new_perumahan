-- Database Schema for Sistem Web Company Profile & Katalog Perumahan Dinamis
-- Database: db_perumahan
-- Versi: new_perumahan (AdminLTE v4 + Theme Preset: Industrialist / Brutalist)

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `leads`;
DROP TABLE IF EXISTS `property_images`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `siteplan_pins`;
DROP TABLE IF EXISTS `siteplans`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `settings`;

-- 1. Table `settings` (Key-Value configuration)
CREATE TABLE `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table `admins` (User Management & RBAC)
CREATE TABLE `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('Superadmin', 'Admin') NOT NULL DEFAULT 'Admin',
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table `properties` (Catalog with Soft Deletes)
CREATE TABLE `properties` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `harga` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `deskripsi` TEXT NULL,
  `spesifikasi_kamar` VARCHAR(100) NULL COMMENT 'Contoh: 3 KT, 2 KM, 1 Carport',
  `luas_tanah` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'm2',
  `luas_bangunan` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'm2',
  `status` ENUM('Tersedia', 'Booking', 'Terjual') NOT NULL DEFAULT 'Tersedia',
  `is_promo` TINYINT(1) NOT NULL DEFAULT 0,
  `promo_title` VARCHAR(255) NULL,
  `promo_desc` TEXT NULL,
  `brosur_pdf` VARCHAR(255) NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL DEFAULT NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_is_promo` (`is_promo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table `property_images` (One-to-Many Gallery)
CREATE TABLE `property_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT UNSIGNED NOT NULL,
  `image_name` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_property_images_property` FOREIGN KEY (`property_id`) 
    REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table `siteplans` (Master Plan Maps)
CREATE TABLE `siteplans` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table `siteplan_pins` (Interactive Plotting Coordinates)
CREATE TABLE `siteplan_pins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `siteplan_id` INT UNSIGNED NOT NULL,
  `property_id` INT UNSIGNED NOT NULL,
  `kavling_number` VARCHAR(50) NOT NULL,
  `status` ENUM('Tersedia', 'Booking', 'Terjual') NOT NULL DEFAULT 'Tersedia',
  `pos_x` DECIMAL(6,3) NOT NULL COMMENT 'Persentase X (0-100%)',
  `pos_y` DECIMAL(6,3) NOT NULL COMMENT 'Persentase Y (0-100%)',
  `notes` VARCHAR(255) NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pins_siteplan` FOREIGN KEY (`siteplan_id`) 
    REFERENCES `siteplans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pins_property` FOREIGN KEY (`property_id`) 
    REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Table `leads` (Lead Magnet Form & Pre-Chat Capture)
CREATE TABLE `leads` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT UNSIGNED NULL,
  `nama_prospek` VARCHAR(150) NOT NULL,
  `no_wa` VARCHAR(30) NOT NULL,
  `email` VARCHAR(100) NULL,
  `sumber` VARCHAR(100) NULL DEFAULT 'Website',
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_leads_property` FOREIGN KEY (`property_id`) 
    REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Initial Settings Data Seeder
-- ============================================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
('company_name', 'Grand Harmoni Residence', NOW(), NOW()),
('company_tagline', 'Hunian Mewah, Asri, dan Strategis untuk Keluarga Idaman', NOW(), NOW()),
('company_address', 'Jl. Boulevard Raya No. 88, Grand City, Jakarta Barat', NOW(), NOW()),
('company_phone', '021-5558989', NOW(), NOW()),
('company_whatsapp', '6281234567890', NOW(), NOW()),
('company_email', 'marketing@grandharmoni.co.id', NOW(), NOW()),
('company_about', 'Grand Harmoni Residence merupakan pengembang properti terkemuka yang menghadirkan hunian modern berwawasan lingkungan hijau dengan fasilitas eksklusif seperti clubhouse, security 24 jam CCTV, underground utilities, dan akses transportasi strategis.', NOW(), NOW()),
('primary_color', '#1e3a8a', NOW(), NOW()),
('secondary_color', '#0d9488', NOW(), NOW()),
('site_theme_preset', 'industrialist', NOW(), NOW()),
('promo_modal_active', '1', NOW(), NOW()),
('promo_modal_title', 'Promo Grand Launching Cluster Lavender!', NOW(), NOW()),
('promo_modal_image', 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&auto=format&fit=crop&q=60', NOW(), NOW()),
('promo_modal_desc', 'Dapatkan Free DP 0%, Subsidi KPR up to 50 Juta, Free Biaya BPHTB & Notaris, serta Smart Home System untuk 10 Pembeli Pertama bulan ini!', NOW(), NOW()),
('promo_modal_btn_text', 'Klaim Promo Sekarang via WA', NOW(), NOW()),
('va_name', 'Sarah - Konsultan Properti', NOW(), NOW()),
('va_phone', '6281234567890', NOW(), NOW()),
('chat_provider', 'n8n', NOW(), NOW()),
('n8n_webhook_url', 'https://ercy.app.n8n.cloud/webhook/a3da6735-771c-435f-b4c8-c9ae179329f0/chat', NOW(), NOW()),
('company_logo', '', NOW(), NOW()),
('global_brochure_pdf', '', NOW(), NOW());

-- ============================================================
-- Initial Admins Data Seeder (Password: admin123)
-- Hash: password_hash('admin123', PASSWORD_BCRYPT)
-- ============================================================
INSERT INTO `admins` (`name`, `email`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
('Super Administrator', 'superadmin@grandharmoni.co.id', 'superadmin', '$2y$10$f6B0L6aC3t4Z5mCj8oI0wO9tKxK9Csmc2mJ5EevwZq8YhJv7/p8yK', 'Superadmin', NOW(), NOW()),
('Marketing Sales', 'sales@grandharmoni.co.id', 'salesadmin', '$2y$10$f6B0L6aC3t4Z5mCj8oI0wO9tKxK9Csmc2mJ5EevwZq8YhJv7/p8yK', 'Admin', NOW(), NOW());

-- ============================================================
-- Initial Properties Sample Data
-- ============================================================
INSERT INTO `properties` (`id`, `title`, `slug`, `harga`, `deskripsi`, `spesifikasi_kamar`, `luas_tanah`, `luas_bangunan`, `status`, `is_promo`, `promo_title`, `promo_desc`, `brosur_pdf`, `created_at`, `updated_at`) VALUES
(1, 'Type 45/90 - Cluster Jasmine', 'type-45-90-cluster-jasmine', 650000000.00, 'Rumah minimalis modern 1 lantai cocok untuk keluarga muda, dilengkapi taman depan dan belakang yang asri serta sistem ventilasi silang optimal.', '2 KT, 1 KM, 1 Carport', 90, 45, 'Tersedia', 1, '🔥 Promo Subsidi DP 0% & Free Biaya KPR', 'Diskon uang muka 100% dan bebas seluruh biaya akad notaris untuk pembelian minggu ini.', NULL, NOW(), NOW()),
(2, 'Type 72/120 - Cluster Lavender', 'type-72-120-cluster-lavender', 1250000000.00, 'Hunian elegan 2 lantai dengan ceiling tinggi 4 meter, pencahayaan alami optimal, dan ruang keluarga luas terintegrasi.', '3 KT, 2 KM, 2 Carport', 120, 72, 'Tersedia', 1, '🎁 Bonus Smart Home Package & AC Tiap Kamar', 'Instalasi smart door lock, CCTV indoor, dan 3 unit AC gratis.', NULL, NOW(), NOW()),
(3, 'Type 120/180 - Premium Villa', 'type-120-180-premium-villa', 2100000000.00, 'Villa eksklusif 2 lantai dengan private backyard, smart lock system, dan material premium kelas atas dengan pemandangan danau buatan.', '4 KT, 3 KM, 2 Carport', 180, 120, 'Booking', 0, NULL, NULL, NULL, NOW(), NOW());

-- ============================================================
-- Initial Property Images Sample Data
-- ============================================================
INSERT INTO `property_images` (`property_id`, `image_name`, `created_at`) VALUES
(1, 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800&auto=format&fit=crop&q=80', NOW()),
(1, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&auto=format&fit=crop&q=80', NOW()),
(2, 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&auto=format&fit=crop&q=80', NOW()),
(2, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&auto=format&fit=crop&q=80', NOW()),
(3, 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&auto=format&fit=crop&q=80', NOW());

-- ============================================================
-- Initial Siteplans Sample Data (2 Demo Siteplans)
-- ============================================================
INSERT INTO `siteplans` (`id`, `title`, `image`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Master Plan Tahap 1 - Cluster Jasmine', 'siteplan_sample.png', 'Master layout kavling dan unit klaster Jasmine & Lavender.', 1, NOW(), NOW()),
(2, 'Master Plan Tahap 2 - Premium Villa', 'siteplan_sample.png', 'Denah master plan pengembangan tahap 2 dan area private villa.', 1, NOW(), NOW());

-- ============================================================
-- Initial Siteplan Pins Sample Data
-- ============================================================
INSERT INTO `siteplan_pins` (`id`, `siteplan_id`, `property_id`, `kavling_number`, `status`, `pos_x`, `pos_y`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Blok A1 - No. 01', 'Tersedia', 25.500, 35.200, 'Posisi Hook, Dekat Clubhouse', NOW(), NOW()),
(2, 1, 2, 'Blok A2 - No. 05', 'Tersedia', 42.100, 48.600, 'Hadap Taman Utama', NOW(), NOW()),
(3, 1, 3, 'Blok B1 - No. 12', 'Booking', 68.300, 62.400, 'View Danau Buatan', NOW(), NOW()),
(4, 2, 3, 'Villa Blok V1 - No. 01', 'Tersedia', 30.000, 40.000, 'Private Pool & Garden', NOW(), NOW()),
(5, 2, 2, 'Villa Blok V2 - No. 08', 'Tersedia', 55.000, 60.000, 'Dekat Jogging Track', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
