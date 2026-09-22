-- Production MySQL Database Schema for Decoupled News CMS Platform
-- Database Name: news_platform

CREATE DATABASE IF NOT EXISTS `news_platform` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `news_platform`;

-- 1. Users Table (CMS Staff Authentication & RBAC)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'editor', 'reporter') NOT NULL DEFAULT 'reporter',
  `avatar_url` VARCHAR(255) NULL,
  `bio` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table (News Topics)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Subscribers Table (Public Feed Registrations)
CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `category_preference` INT NULL,
  `status` ENUM('active', 'unsubscribed') NOT NULL DEFAULT 'active',
  `subscribed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_preference`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Articles Repository Table
CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `summary` TEXT NOT NULL,
  `content` LONGTEXT NOT NULL,
  `featured_image` VARCHAR(255) NULL,
  `video_embed_url` VARCHAR(255) NULL,
  `audio_embed_url` VARCHAR(500) NULL,
  `gallery_images` TEXT NULL,
  `reference_url` VARCHAR(255) NULL,
  `reference_source_name` VARCHAR(150) NULL,
  `category_id` INT NOT NULL,
  `author_id` INT NOT NULL,
  `template_type` ENUM('standard', 'investigative', 'opinion') NOT NULL DEFAULT 'standard',
  `is_breaking` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
  `views_count` INT NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_articles_slug` (`slug`),
  INDEX `idx_articles_status` (`status`),
  INDEX `idx_articles_published_at` (`published_at`),
  INDEX `idx_articles_breaking` (`is_breaking`),
  INDEX `idx_articles_template` (`template_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Login Attempts Table (Brute-Force Rate Limiting)
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_login_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial Seed Data
-- Password for default staff is 'admin123' -> $2y$10$59b3W/B4X0jX54cO/K2wve5v92XN5kS1F8k8j7I2P.P1B5k2W7wGG
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `bio`, `is_active`) VALUES
(1, 'admin', 'admin@newsplatform.local', '$2y$10$59b3W/B4X0jX54cO/K2wve5v92XN5kS1F8k8j7I2P.P1B5k2W7wGG', 'admin', 'Chief Editor & Lead Investigative Journalist.', 1),
(2, 'eleanor_vane', 'eleanor@newsplatform.local', '$2y$10$59b3W/B4X0jX54cO/K2wve5v92XN5kS1F8k8j7I2P.P1B5k2W7wGG', 'editor', 'Senior Opinion Columnist and Political Analyst.', 1);

INSERT IGNORE INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'បច្ចេកវិទ្យា & AI (Technology & AI)', 'technology-ai', 'បច្ចេកវិទ្យាបញ្ញាសិប្បនិម្មិត និងការអភិវឌ្ឍប្រព័ន្ធសូហ្វវែរ។'),
(2, 'នយោបាយសកល (Global Politics)', 'global-politics', 'ការវិភាគគោលនយោបាយអន្តរជាតិ និងការទូត។'),
(3, 'បរិស្ថាន & វិទ្យាសាស្ត្រ (Climate & Science)', 'climate-science', 'ការស្រាវជ្រាវវិទ្យាសាស្ត្រ និងថាមពលកកើតឡើងវិញ។'),
(4, 'សេដ្ឋកិច្ច & ទីផ្សារ (Economy & Markets)', 'economy-markets', 'ទីផ្សារហិរញ្ញវត្ថុ និងសេដ្ឋកិច្ចពិភពលោក។');

INSERT IGNORE INTO `articles` 
(`id`, `title`, `slug`, `summary`, `content`, `featured_image`, `video_embed_url`, `reference_url`, `reference_source_name`, `category_id`, `author_id`, `template_type`, `is_breaking`, `status`, `views_count`, `published_at`) 
VALUES
(1, 'ប្រព័ន្ធ AI ស្វ័យតជំនាន់ថ្មីផ្លាស់ប្តូរស្ថាបត្យកម្មសូហ្វវែរសហគ្រាស', 'next-generation-autonomous-ai-systems-reshape-enterprise-architecture',
'ក្រុមស្ថាបត្យករសូហ្វវែរឈានមុខគេកំពុងអនុវត្តប្រព័ន្ធ AI ស្វ័យតដែលមានសុវត្ថិភាព និងប្រសិទ្ធភាពខ្ពស់ក្នុងហេដ្ឋារចនាសម្ព័ន្ធអាជីវកម្ម។',
'ក្រុមវិស្វករសូហ្វវែរនៅទូទាំងពិភពលោកកំពុងផ្លាស់ប្តូរពីប្រព័ន្ធបែបបុរាណទៅកាន់ប្រព័ន្ធស្វ័យប្រវត្តដែលដំណើរការដោយ AI។ នៅពេលដែលប្រព័ន្ធចែកចាយកាន់តែមានភាពស្មុគស្មាញ បណ្តាញបច្ចេកវិទ្យាទំនិញទំនើបៗបានប្រើប្រាស់ការសង្កេតតាមពេលវេលាជាក់ស្តែង ដើម្បីរៀបចំកូដឡើងវិញដោយស្វ័យប្រវត្តិ ការសាកល្បងបន្ត និងការកាត់បន្ថយឧប្បត្តិហេតុដោយរហ័ស។\n\nផ្អែកលើការវាស់វែងស្ថាបត្យកម្មថ្មីៗ ការរចនាដែលមានការបែងចែកដាច់ដោយឡែករួមផ្សំជាមួយការគ្រប់គ្រងសិទ្ធិយ៉ាងម៉ត់ចត់ អាចកាត់បន្ថយពេលវេលារំខានដល់ប្រតិបត្តិការរហូតដល់ ៤៣%។ អ្នកជំនាញឧស្សាហកម្មបញ្ជាក់ថា សុវត្ថិភាពនៃប្រព័ន្ធស្វ័យប្រវត្តិកម្មទាមទារឱ្យមានការស៊ើបអង្កេតប្រកបដោយតម្លាភាព និងការគ្រប់គ្រងការចូលប្រើប្រាស់យ៉ាងត្រឹមត្រូវ។',
'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
'https://www.youtube.com/embed/dQw4w9WgXcQ',
'https://arxiv.org',
'បណ្ណសារស្រាវជ្រាវវិទ្យាសាស្ត្រកុំព្យូទ័រ arXiv',
1, 1, 'standard', 1, 'published', 1420, NOW()),

(2, 'ការស៊ើបអង្កេតជម្រៅលើបណ្តាញខ្សែកាបបាតសមុទ្រពិភពលោក', 'silent-subsea-cable-revolution-deep-dive-global-fiber-optics',
'ការស៊ើbអង្កេតយ៉ាងល្អិតល្អន់លើហេដ្ឋារចនាសម្ព័ន្ធទូរគមនាគមន៍បាតសមុទ្រដែលទ្រទ្រង់ ៩៩% នៃចរាចរណ៍អ៊ីនធឺណិតពិភពលោក។',
'នៅក្រោមផ្ទៃសមុទ្រដ៏ជ្រៅ មានបណ្តាញខ្សែកាបហ្វៃប័រអុបទិកដែលមានសមត្ថភាពខ្ពស់ ដឹកជញ្ជូនប្រតិបត្តិការហិរញ្ញវត្ថុ ការទំនាក់ទំនងផ្ទាល់ខ្លួន និងទិន្នន័យក្លោដរាប់ពាន់តេរ៉ាបៃរៀងរាល់វិនាទី។ ទោះបីជាបណ្តាញរណបអវកាសទទួលបានការចាប់អារម្មណ៍ខ្លាំងយ៉ាងណាក៏ដោយ ហេដ្ឋារចនាសម្ព័ន្ធខ្សែកាបសមុទ្រនៅតែជាឆ្អឹងខ្នងដែលមិនអាចជំនួសបាននៃការភ្ជាប់ទំនាក់ទំនងសកល។\n\nការស៊ើបអង្កេតរយៈពេលបីខែរបស់យើងបង្ហាញពីរបៀបដែលសម្ព័ន្ធអន្តរជាតិរក្សាភាពធន់នឹងកំហុសឆ្លងកាត់តំបន់រញ្ជួយដី ព្រមទាំងរុករកផ្ទៃទឹកភូមិសាស្ត្រនយោបាយដ៏ស្មុគស្មាញ។ ប្រព័ន្ធពង្រីកសញ្ញាទំនើបឥឡូវនេះប្រើប្រាស់ Erbium-doped fiber amplifiers ដើម្បីបង្កើនភាពត្រឹមត្រូវនៃសញ្ញាឆ្លងកាត់មហាសមុទ្រដោយគ្មានការអាក់ខាន។',
'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1200&q=80',
'https://www.youtube.com/embed/dQw4w9WgXcQ',
'https://www.submarinenetworks.com',
'របាយការណ៍សម្ព័ន្ធបណ្តាញខ្សែកាបបាតសមុទ្រពិភពលោក',
3, 1, 'investigative', 0, 'published', 3890, NOW()),

(3, 'ហេតុអ្វីបានជាវិចារណញាណរបស់មនុស្សនៅតែមានសារៈសំខាន់ក្នុងយុគសម័យស្វ័យប្រវត្តិកម្ម', 'why-human-intuition-remains-imperative-in-an-automated-era',
'អាល់កូរីតអាចបង្កើនប្រសិទ្ធភាពសម្រាប់គោលដៅប្រវត្តិសាស្ត្រ ប៉ុន្តែសិល្បៈ និងទស្សនវិជ្ជាបំបែកកំណត់ត្រាទាមទារការក្លាហាន និងគំនិតច្នៃប្រឌិតរបស់មនុស្ស។',
'យើងរស់នៅក្នុងយុគសម័យដែលវាស់វែងដោយទិន្នផល និងការព្យាករណ៍តាមអាល់កូរីត។ ប៉ុន្តែរាល់ការលោតផ្លោះដ៏អស្ចារ្យនៅក្នុងវប្បធម៌មនុស្សជាតិ—ចាប់ពីអក្សរសិល្ប៍បុរាណរហូតដល់ការរចនាឧស្សាហកម្មបដិវត្តន៍—បានកើតចេញពីការចង់ដឹងចង់ឃើញដែលមិនអាចកាត់ថ្លៃបាន ជាជាងការបូកសរុបតាមប្រូបាប៊ីលីតេ។\n\nនៅពេលដែលអាល់កូរីតបំពេញភារកិច្ចដែលច្រកដែលដដែលៗ តម្លៃនៃភាពទន់ភ្លន់ ការយល់ចិត្ត និងការគ្រប់គ្រងផ្នែកសីលធម៌របស់មនុស្សកាន់តែមានការកើនឡើងជាលំដាប់។ ការសាកល្បងពិតប្រាកដនៃកាដឹកនាំសម័យទំនើប មិនមែនជាល្បឿនដែលយើងដំណើរការទិន្នន័យនោះទេ ប៉ុន្តែជាជម្រៅដែលយើងយល់អំពីកិត្តិយសរបស់មនុស្សជាតិ។',
'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1200&q=80',
NULL,
'https://plato.stanford.edu',
'វចនានុក្រមទស្សនវិជ្ជារបស់សាកលវិទ្យាល័យ Stanford',
2, 2, 'opinion', 0, 'published', 950, NOW());
