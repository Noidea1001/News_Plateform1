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
  `title_kh` VARCHAR(255) NULL,
  `title_en` VARCHAR(255) NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `summary` TEXT NOT NULL,
  `summary_kh` TEXT NULL,
  `summary_en` TEXT NULL,
  `content` LONGTEXT NOT NULL,
  `content_kh` LONGTEXT NULL,
  `content_en` LONGTEXT NULL,
  `featured_image` VARCHAR(500) NULL,
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

-- 6. Comments Table (Reader Community & Threaded Discussions)
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `article_id` INT NOT NULL,
  `parent_id` INT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `user_email` VARCHAR(150) NOT NULL,
  `content` TEXT NOT NULL,
  `likes_count` INT NOT NULL DEFAULT 0,
  `status` ENUM('approved', 'pending', 'spam') NOT NULL DEFAULT 'approved',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE,
  INDEX `idx_comments_article` (`article_id`),
  INDEX `idx_comments_parent` (`parent_id`),
  INDEX `idx_comments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Readers Table (Public Reader Registration & Authentication)
CREATE TABLE IF NOT EXISTS `readers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `avatar_url` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_readers_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Notifications Table (Real-Time Breaking & News Alerts for Readers)
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `article_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('breaking', 'published', 'system') NOT NULL DEFAULT 'published',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
  INDEX `idx_notif_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Reader Subscriptions Table (Topic Subscriptions for Readers)
CREATE TABLE IF NOT EXISTS `reader_subscriptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reader_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reader_id`) REFERENCES `readers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_reader_category` (`reader_id`, `category_id`),
  INDEX `idx_reader_sub_reader` (`reader_id`),
  INDEX `idx_reader_sub_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Web Push Subscriptions Table (Browser Push Notifications with VAPID)
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `endpoint` TEXT NOT NULL,
  `endpoint_hash` VARCHAR(64) NOT NULL UNIQUE,
  `p256dh` VARCHAR(255) NOT NULL,
  `auth` VARCHAR(255) NOT NULL,
  `reader_id` INT NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_notified_at` DATETIME NULL,
  INDEX `idx_push_sub_reader` (`reader_id`),
  FOREIGN KEY (`reader_id`) REFERENCES `readers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Initial Seed Data
-- Password for default staff is 'admin123' -> $2y$10$59b3W/B4X0jX54cO/K2wve5v92XN5kS1F8k8j7I2P.P1B5k2W7wGG
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `bio`, `is_active`) VALUES
(1, 'admin', 'admin@newsplatform.local', '$2y$10$59b3W/B4X0jX54cO/K2wve5v92XN5kS1F8k8j7I2P.P1B5k2W7wGG', 'admin', 'Chief Editor & Lead Investigative Journalist.', 1),
(2, 'eleanor_vane', 'eleanor@newsplatform.local', '$2y$10$59b3W/B4X0jX54cO/K2wve5v92XN5kS1F8k8j7I2P.P1B5k2W7wGG', 'editor', 'Senior Opinion Columnist and Political Analyst.', 1);

INSERT IGNORE INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'បច្ចេកវិទ្យា & AI (Technology & AI)', 'technology-ai', 'បច្ចេកវិទ្យាបញ្ញាសិប្បនិម្មិត និងការអភិវឌ្ឍប្រព័ន្ធសូហ្វវែរ។ (Artificial intelligence technology and software development.)'),
(2, 'នយោបាយសកល (Global Politics)', 'global-politics', 'ការវិភាគគោលនយោបាយអន្តរជាតិ និងការទូត។ (International policy analysis and diplomacy.)'),
(3, 'បរិស្ថាន & វិទ្យាសាស្ត្រ (Climate & Science)', 'climate-science', 'ការស្រាវជ្រាវវិទ្យាសាស្ត្រ និងថាមពលកកើតឡើងវិញ។ (Scientific research and renewable energy.)'),
(4, 'សេដ្ឋកិច្ច & ទីផ្សារ (Economy & Markets)', 'economy-markets', 'ទីផ្សារហិរញ្ញវត្ថុ និងសេដ្ឋកិច្ចពិភពលោក។ (Financial markets and global economy.)'),
(7, 'កីឡា & ព័ត៌មានអន្តរជាតិ (Sports & World News)', 'sports-world', 'ព័ត៌មានកីឡាអន្តរជាតិ ការប្រកួតបាល់ទាត់ និងព្រឹត្តិការណ៍កីឡាពិភពលោក។ (World sports news, football championships, and global sporting events.)');

INSERT IGNORE INTO `articles` 
(`id`, `title`, `slug`, `summary`, `content`, `featured_image`, `video_embed_url`, `reference_url`, `reference_source_name`, `category_id`, `author_id`, `template_type`, `is_breaking`, `status`, `views_count`, `published_at`) 
VALUES
(1, 'ប្រព័ន្ធ AI ស្វ័យតជំនាន់ថ្មីផ្លាស់ប្តូរស្ថាបត្យកម្មសូហ្វវែរសហគ្រាស', 'next-generation-autonomous-ai-systems-reshape-enterprise-architecture',
'ក្រុមស្ថាបត្យករសូហ្វវែរឈានមុខគេកំពុងអនុវត្តប្រព័ន្ធ AI ស្វ័យតដែលមានសុវត្ថិភាព និងប្រសិទ្ធភាពខ្ពស់ក្នុងហេដ្ឋារចនាសម្ព័ន្ធអាជីវកម្ម។ (Leading software architects are implementing autonomous AI systems across enterprise infrastructure.)',
'ក្រុមវិស្វករសូហ្វវែរនៅទូទាំងពិភពលោកកំពុងផ្លាស់ប្តូរពីប្រព័ន្ធបែបបុរាណទៅកាន់ប្រព័ន្ធស្វ័យប្រវត្តដែលដំណើរការដោយ AI។ នៅពេលដែលប្រព័ន្ធចែកចាយកាន់តែមានភាពស្មុគស្មាញ បណ្តាញបច្ចេកវិទ្យាទំនិញទំនើបៗបានប្រើប្រាស់ការសង្កេតតាមពេលវេលាជាក់ស្តែង ដើម្បីរៀបចំកូដឡើងវិញដោយស្វ័យប្រវត្តិ ការសាកល្បងបន្ត និងការកាត់បន្ថយឧប្បត្តិហេតុដោយរហ័ស។\n\nផ្អែកលើការវាស់វែងស្ថាបត្យកម្មថ្មីៗ ការរចនាដែលមានការបែងចែកដាច់ដោយឡែករួមផ្សំជាមួយការគ្រប់គ្រងសិទ្ធិយ៉ាងម៉ត់ចត់ អាចកាត់បន្ថយពេលវេលារំខានដល់ប្រតិបត្តិការរហូតដល់ ៤៣%។\n--- \nSoftware engineering teams worldwide are transitioning from legacy setups to autonomous AI-driven systems. As distributed architectures grow complex, modern stacks leverage real-time observability for automated code refactoring, continuous testing, and rapid incident mitigation.',
'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
'https://www.youtube.com/embed/dQw4w9WgXcQ',
'https://arxiv.org',
'បណ្ណសារស្រាវជ្រាវវិទ្យាសាស្ត្រកុំព្យូទ័រ arXiv',
1, 1, 'standard', 1, 'published', 1420, NOW()),

(2, 'ការស៊ើបអង្កេតជម្រៅលើបណ្តាញខ្សែកាបបាតសមុទ្រពិភពលោក', 'silent-subsea-cable-revolution-deep-dive-global-fiber-optics',
'ការស៊ើបអង្កេតយ៉ាងល្អិតល្អន់លើហេដ្ឋារចនាសម្ព័ន្ធទូរគមនាគមន៍បាតសមុទ្រដែលទ្រទ្រង់ ៩៩% នៃចរាចរណ៍អ៊ីនធឺណិតពិភពលោក។ (Meticulous investigation into subsea telecommunications infrastructure supporting 99% of global internet traffic.)',
'នៅក្រោមផ្ទៃសមុទ្រដ៏ជ្រៅ មានបណ្តាញខ្សែកាបហ្វៃប័រអុបទិកដែលមានសមត្ថភាពខ្ពស់ ដឹកជញ្ជូនប្រតិបត្តិការហិរញ្ញវត្ថុ ការទំនាក់ទំនងផ្ទាល់ខ្លួន និងទិន្នន័យក្លោដរាប់ពាន់តេរ៉ាបៃរៀងរាល់វិនាទី។\n--- \nBeneath the deep ocean waters lies a high-capacity fiber optic cable network carrying financial transactions, communications, and cloud data every second.',
'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1200&q=80',
'https://www.youtube.com/embed/dQw4w9WgXcQ',
'https://www.submarinenetworks.com',
'របាយការណ៍សម្ព័ន្ធបណ្តាញខ្សែកាបបាតសមុទ្រពិភពលោក',
3, 1, 'investigative', 0, 'published', 3890, NOW()),

(3, 'ហេតុអ្វីបានជាវិចារណញាណរបស់មនុស្សនៅតែមានសារៈសំខាន់ក្នុងយុគសម័យស្វ័យប្រវត្តិកម្ម', 'why-human-intuition-remains-imperative-in-an-automated-era',
'អាល់កូរីតអាចបង្កើនប្រសិទ្ធភាពសម្រាប់គោលដៅប្រវត្តិសាស្ត្រ ប៉ុន្តែសិល្បៈ និងទស្សនវិជ្ជាបំបែកកំណត់ត្រាទាមទារការក្លាហាន និងគំនិតច្នៃប្រឌិតរបស់មនុស្ស។ (Algorithms optimize historical targets, but groundbreaking art and philosophy demand human courage and intuition.)',
'យើងរស់នៅក្នុងយុគសម័យដែលវាស់វែងដោយទិន្នផល និងការព្យាករណ៍តាមអាល់កូរីត។ ប៉ុន្តែរាល់ការលោតផ្លោះដ៏អស្ចារ្យនៅក្នុងវប្បធម៌មនុស្សជាតិ—ចាប់ពីអក្សរសិល្ប៍បុរាណរហូតដល់ការរចនាឧស្សាហកម្មបដិវត្តន៍—បានកើតចេញពីការចង់ដឹងចង់ឃើញដែលមិនអាចកាត់ថ្លៃបាន ជាជាងការបូកសរុបតាមប្រូបាប៊ីលីតេ។\n--- \nWe live in an era measured by yield and algorithmic forecasts. But every milestone in human culture—from literature to industrial design—stemmed from boundless curiosity.',
'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1200&q=80',
NULL,
'https://plato.stanford.edu',
'វចនានុក្រមទស្សនវិជ្ជារបស់សាកលវិទ្យាល័យ Stanford',
2, 2, 'opinion', 0, 'published', 950, NOW()),

(13, 'គ្រាប់បាល់ដ៏អស្ចារ្យនៅចុងម៉ោងរបស់ Cristiano Ronaldo ជួយក្រុមទទួលបានជ័យជម្នះ', 'cristiano-ronaldo-late-stunning-goal-secures-victory',
'ព័ត៌មានកីឡាអន្តរជាតិ៖ Cristiano Ronaldo បានស៊ុតបញ្ចូលទីគ្រាប់បាល់ឈ្នះនៅចុងម៉ោង យ៉ាងអស្ចារ្យក្នុងការប្រកួតបាល់ទាត់អន្តរជាតិ។ (World sports news: Cristiano Ronaldo scored a dramatic late winning goal in international football match.)',
'ក្នុងការប្រកួតបាល់ទាត់ដ៏រំភើបអស្ចារ្យ កីឡាករបាល់ទាត់ឆ្នើមពិភពលោក Cristiano Ronaldo បានស៊ុតបញ្ចូលទីគ្រាប់បាល់ឈ្នះនៅនាទីចុងក្រោយ នៃកការប្រកួតដើម្បីជួយក្រុមទទួលបានជ័យជម្នះ ៣-២ យ៉ាងរំភើប។\n--- \nIn a thrilling international football championship match, global football superstar Cristiano Ronaldo scored a spectacular late winning goal in the final minutes to secure a thrilling 3-2 victory.',
'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80',
NULL,
'https://www.fifa.com',
'សហព័ន្ធបាល់ទាត់ពិភពលោក (FIFA)',
7, 1, 'standard', 1, 'published', 8450, NOW());
