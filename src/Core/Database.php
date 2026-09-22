<?php
/**
 * Database Singleton Wrapper Class (PDO)
 * news-platform / src / Core / Database.php
 */

namespace App\Core;

use PDO;
use PDOException;
use Exception;

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
            // First attempt connection to host (without dbname to ensure database exists)
            $dsnHostOnly = "mysql:host={$host};port={$port};charset={$charset}";
            $tmpPdo = new PDO($dsnHostOnly, $username, $password, $options);
            $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            unset($tmpPdo);

            // Connect to specific database
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            $this->pdo = new PDO($dsn, $username, $password, $options);

            // Auto-initialize schema and sync default admin credentials
            $this->autoInitializeSchema();
        } catch (PDOException $e) {
            throw new Exception("Database Connection Error: " . $e->getMessage(), (int)$e->getCode());
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
            if ($check && $check->rowCount() === 0) {
                $schemaFile = __DIR__ . '/../../schema.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    if ($sql !== false) {
                        $this->pdo->exec($sql);
                    }
                }
            }

            // Sync default admin staff user password_hash for 'admin123'
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

            // Ensure audio_embed_url and gallery_images columns exist on articles table
            @$this->pdo->exec("ALTER TABLE articles ADD COLUMN `audio_embed_url` VARCHAR(500) NULL AFTER `video_embed_url`");
            @$this->pdo->exec("ALTER TABLE articles ADD COLUMN `gallery_images` TEXT NULL AFTER `audio_embed_url`");

            // Ensure category names are translated to Khmer in DB
            @$this->pdo->exec("UPDATE categories SET name = 'បច្ចេកវិទ្យា & AI' WHERE id = 1 AND name LIKE '%Technology%'");
            @$this->pdo->exec("UPDATE categories SET name = 'នយោបាយសកល' WHERE id = 2 AND name LIKE '%Politics%'");
            @$this->pdo->exec("UPDATE categories SET name = 'បរិស្ថាន & វិទ្យាសាស្ត្រ' WHERE id = 3 AND name LIKE '%Climate%'");
            @$this->pdo->exec("UPDATE categories SET name = 'សេដ្ឋកិច្ច & ទីផ្សារ' WHERE id = 4 AND name LIKE '%Economy%'");

        } catch (PDOException $e) {
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
?>
