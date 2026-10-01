<?php
/**
 * Database Singleton Wrapper Class (PDO)
 * news-platform / src / Core / Database.php
 */

namespace App\Core;

use PDO;
use PDOException;
use Exception;
use Throwable;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $configPath = __DIR__ . '/../../config/database.php';
        if (!file_exists($configPath)) {
            throw new Exception("Database configuration file missing at: {$configPath}");
        }

        $config = require $configPath;

        $host = $config['host'];
        $port = $config['port'];
        $dbname = $config['dbname'];
        $username = $config['username'];
        $password = $config['password'];
        $charset = $config['charset'];
        $options = $config['options'];

        try {
            // First attempt direct connection to specified database (standard for managed/cloud MySQL databases)
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            try {
                $this->pdo = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $directEx) {
                // If database does not exist yet (local dev environment), attempt host-level database creation
                try {
                    $dsnHostOnly = "mysql:host={$host};port={$port};charset={$charset}";
                    $tmpPdo = new PDO($dsnHostOnly, $username, $password, $options);
                    $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    unset($tmpPdo);
                    $this->pdo = new PDO($dsn, $username, $password, $options);
                } catch (PDOException $fallbackEx) {
                    throw $directEx;
                }
            }

            // Auto-initialize schema and sync default admin credentials
            $this->autoInitializeSchema();
        } catch (PDOException $e) {
            throw new Exception("Database Connection Error: " . $e->getMessage(), (int) $e->getCode());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Auto-runs schema.sql if key table is missing and syncs default staff password.
     */
    private function autoInitializeSchema(): void
    {
        try {
            $check = $this->pdo->query("SHOW TABLES LIKE 'articles'");
            $tablesExist = ($check && $check->rowCount() > 0);

            if (!$tablesExist) {
                $schemaFile = __DIR__ . '/../../schema.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    if ($sql !== false) {
                        $this->pdo->exec($sql);
                    }
                }
            }

            // Sync default admin staff user password_hash for 'admin123' if needed
            $adminUser = $this->fetch("SELECT id, password_hash FROM users WHERE username = 'admin' LIMIT 1");
            if (!$adminUser || !password_verify('admin123', $adminUser['password_hash'])) {
                $freshHash = password_hash('admin123', PASSWORD_BCRYPT);
                if ($adminUser) {
                    $this->execute("UPDATE users SET password_hash = :hash, is_active = 1 WHERE username = 'admin'", ['hash' => $freshHash]);
                } else {
                    $this->execute(
                        "INSERT INTO users (username, email, password_hash, role, is_active, created_at) VALUES ('admin', 'admin@newsplatform.local', :hash, 'admin', 1, NOW())",
                        ['hash' => $freshHash]
                    );
                }
            }

            // Ensure columns exist on articles table if table was created earlier without them
            if ($tablesExist) {
                $columnsQuery = $this->pdo->query("SHOW COLUMNS FROM articles");
                $existingColumns = $columnsQuery ? $columnsQuery->fetchAll(PDO::FETCH_COLUMN) : [];

                if (!in_array('audio_embed_url', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `audio_embed_url` VARCHAR(500) NULL AFTER `video_embed_url`");
                }
                if (!in_array('gallery_images', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `gallery_images` TEXT NULL AFTER `audio_embed_url`");
                }
                if (!in_array('has_drop_cap', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `has_drop_cap` TINYINT(1) NOT NULL DEFAULT 0");
                }
                if (!in_array('title_kh', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `title_kh` VARCHAR(255) NULL AFTER `title`");
                }
                if (!in_array('title_en', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `title_en` VARCHAR(255) NULL AFTER `title_kh`");
                }
                if (!in_array('summary_kh', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `summary_kh` TEXT NULL AFTER `summary`");
                }
                if (!in_array('summary_en', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `summary_en` TEXT NULL AFTER `summary_kh`");
                }
                if (!in_array('content_kh', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `content_kh` LONGTEXT NULL AFTER `content`");
                }
                if (!in_array('content_en', $existingColumns, true)) {
                    $this->pdo->exec("ALTER TABLE articles ADD COLUMN `content_en` LONGTEXT NULL AFTER `content_kh`");
                }

                // Sync existing articles into dedicated title_kh / title_en columns if empty
                $unsynced = $this->fetchAll("SELECT id, title, summary, content FROM articles WHERE title_kh IS NULL OR title_en IS NULL");
                foreach ($unsynced as $uArt) {
                    $tKh = function_exists('article_title') ? article_title($uArt['title'], 'kh') : $uArt['title'];
                    $tEn = function_exists('article_title') ? article_title($uArt['title'], 'en') : $uArt['title'];
                    $sKh = function_exists('article_summary') ? article_summary($uArt['summary'], 'kh') : $uArt['summary'];
                    $sEn = function_exists('article_summary') ? article_summary($uArt['summary'], 'en') : $uArt['summary'];
                    $cKh = function_exists('parse_dual_lang') ? parse_dual_lang($uArt['content'], 'kh') : $uArt['content'];
                    $cEn = function_exists('parse_dual_lang') ? parse_dual_lang($uArt['content'], 'en') : $uArt['content'];
                    $this->execute(
                        "UPDATE articles SET title_kh = :tkh, title_en = :ten, summary_kh = :skh, summary_en = :sen, content_kh = :ckh, content_en = :cen WHERE id = :id",
                        [
                            'tkh' => $tKh,
                            'ten' => $tEn,
                            'skh' => $sKh,
                            'sen' => $sEn,
                            'ckh' => $cKh,
                            'cen' => $cEn,
                            'id' => $uArt['id']
                        ]
                    );
                }
            }

            // Ensure comments table exists
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `comments` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // Ensure readers (public users) table exists
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `readers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(120) NOT NULL UNIQUE,
                `password_hash` VARCHAR(255) NOT NULL,
                `avatar_url` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_readers_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // Ensure notifications table exists
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `article_id` INT NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `message` TEXT NOT NULL,
                `type` ENUM('breaking', 'published', 'system') NOT NULL DEFAULT 'published',
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
                INDEX `idx_notif_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // Ensure reader_subscriptions table exists
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `reader_subscriptions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `reader_id` INT NOT NULL,
                `category_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`reader_id`) REFERENCES `readers`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
                UNIQUE KEY `uk_reader_category` (`reader_id`, `category_id`),
                INDEX `idx_reader_sub_reader` (`reader_id`),
                INDEX `idx_reader_sub_category` (`category_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");


            // Seed initial notifications if empty
            $notifCount = (int)$this->fetchColumn("SELECT COUNT(*) FROM notifications");
            if ($notifCount === 0) {
                $recentNews = $this->fetchAll("SELECT id, title, summary, is_breaking FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 3");
                foreach ($recentNews as $rNews) {
                    $notifType = ((int)$rNews['is_breaking'] === 1) ? 'breaking' : 'published';
                    $this->execute(
                        "INSERT INTO notifications (article_id, title, message, type, created_at) VALUES (:aid, :title, :msg, :type, NOW())",
                        [
                            'aid' => (int)$rNews['id'],
                            'title' => $rNews['title'],
                            'msg' => mb_strimwidth(strip_tags($rNews['summary']), 0, 120, '...'),
                            'type' => $notifType
                        ]
                    );
                }
            }

            // Seed sample reader comments if table is empty
            $commentCount = (int)$this->fetchColumn("SELECT COUNT(*) FROM comments");
            if ($commentCount === 0) {
                $sampleArticle = $this->fetch("SELECT id FROM articles WHERE status = 'published' LIMIT 1");
                if ($sampleArticle) {
                    $artId = (int)$sampleArticle['id'];
                    $this->execute(
                        "INSERT INTO comments (article_id, parent_id, user_name, user_email, content, likes_count, status, created_at) VALUES 
                        (:art_id1, NULL, 'Sophal Meas', 'sophal.meas@example.com', 'This in-depth coverage provides critical clarity on the latest regional technological shifts. Excellent journalistic analysis!', 8, 'approved', NOW() - INTERVAL 2 HOUR)",
                        ['art_id1' => $artId]
                    );
                    $firstCommentId = (int)$this->lastInsertId();
                    $this->execute(
                        "INSERT INTO comments (article_id, parent_id, user_name, user_email, content, likes_count, status, created_at) VALUES 
                        (:art_id2, NULL, 'Dara Seng', 'dara.seng@example.com', 'The data points cited regarding automated system resilience match what we observe across distributed infrastructure.', 4, 'approved', NOW() - INTERVAL 1 HOUR)",
                        ['art_id2' => $artId]
                    );
                    if ($firstCommentId) {
                        $this->execute(
                            "INSERT INTO comments (article_id, parent_id, user_name, user_email, content, likes_count, status, created_at) VALUES 
                            (:art_id3, :pid, 'Editorial Desk', 'desk@newsplatform.local', 'Thank you for following our coverage! We will be publishing a follow-up analysis later this week.', 3, 'approved', NOW() - INTERVAL 30 MINUTE)",
                            ['art_id3' => $artId, 'pid' => $firstCommentId]
                        );
                    }
                }
            }
        } catch (Throwable $e) {
            error_log("Schema auto-initialization exception: " . $e->getMessage());
        }
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result !== false ? $result : null;
    }

    public function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        return $this->query($sql, $params)->fetchColumn($column);
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}