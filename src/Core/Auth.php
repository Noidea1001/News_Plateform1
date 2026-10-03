<?php
/**
 * Authentication & Security Manager (RBAC, CSRF, Session Fingerprint, Rate Limiting)
 * news-platform / src / Core / Auth.php
 */

namespace App\Core;

use Exception;

class Auth
{
    private static bool $sessionStarted = false;

    /**
     * Start secure HTTP-only session with fingerprint checks
     */
    public static function startSession(): void
    {
        if (!ob_get_level()) {
            ob_start();
        }

        if (self::$sessionStarted || session_status() === PHP_SESSION_ACTIVE) {
            self::$sessionStarted = true;
            return;
        }

        if (headers_sent()) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            self::$sessionStarted = true;
            return;
        }

        // Configure security cookie settings before session start (30-day persistence)
        @ini_set('session.gc_maxlifetime', 2592000); // 30 days
        @ini_set('session.cookie_lifetime', 2592000); // 30 days

        $cookieParams = [
            'lifetime' => 2592000, // 30 days
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ];

        @session_set_cookie_params($cookieParams);
        @session_start();
        self::$sessionStarted = true;

        // Verify session fingerprint to prevent session hijacking
        self::verifyFingerprint();

        // Restore reader session from persistent cookie if session was cleared
        self::restoreReaderSessionFromCookie();
    }

    /**
     * Generate or verify unique client session fingerprint (User-Agent based)
     * Note: IP address is intentionally excluded to prevent mobile users from being logged out
     * when switching cellular towers, toggling Wi-Fi/4G/5G, or roaming.
     */
    private static function verifyFingerprint(): void
    {
        $currentFingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');

        if (!isset($_SESSION['_fingerprint'])) {
            $_SESSION['_fingerprint'] = $currentFingerprint;
        } elseif ($_SESSION['_fingerprint'] !== $currentFingerprint) {
            // User-agent changed significantly: regenerate session securely without destroying session state
            session_regenerate_id(true);
            $_SESSION['_fingerprint'] = $currentFingerprint;
        }
    }

    /**
     * Authenticate staff user credentials
     */
    public static function login(string $username, string $password): bool
    {
        self::startSession();
        $db = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 1. Check rate limit
        if (self::isRateLimited($ip)) {
            throw new Exception("Too many failed login attempts. Please try again in 15 minutes.");
        }

        // 2. Fetch staff user
        $user = $db->fetch("SELECT * FROM users WHERE (username = :username OR email = :email) AND is_active = 1 LIMIT 1", [
            'username' => $username,
            'email' => $username
        ]);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Reset failed login attempts for this IP
            $db->execute("DELETE FROM login_attempts WHERE ip_address = :ip", ['ip' => $ip]);

            // Regenerate session ID upon privilege elevation
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['avatar_url'] = $user['avatar_url'] ?? null;
            $_SESSION['logged_in_at'] = time();

            return true;
        }

        // Record failed attempt
        self::recordLoginAttempt($ip);
        return false;
    }

    /**
     * Logout active staff session
     */
    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get currently logged-in user array metadata
     */
    public static function user(): ?array
    {
        self::startSession();
        if (!self::check()) {
            return null;
        }

        if (!array_key_exists('avatar_url', $_SESSION)) {
            $db = Database::getInstance();
            $_SESSION['avatar_url'] = $db->fetchColumn("SELECT avatar_url FROM users WHERE id = :id", ['id' => (int)$_SESSION['user_id']]) ?: null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'avatar_url' => $_SESSION['avatar_url'] ?? null,
        ];
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string|array $roles): bool
    {
        self::startSession();
        if (!self::check()) {
            return false;
        }

        $userRole = $_SESSION['role'] ?? 'guest';
        if (is_array($roles)) {
            return in_array($userRole, $roles, true);
        }

        return $userRole === $roles;
    }

    private const READER_COOKIE_NAME = 'np_reader_session';
    private const AUTH_SALT = 'NewsPlatformSecureSecretKey2026';

    /**
     * Set a persistent signed reader cookie for 30 days
     */
    public static function setReaderRememberCookie(array $reader): void
    {
        if (headers_sent()) {
            return;
        }
        $payload = [
            'id' => (int)$reader['id'],
            'email' => $reader['email'],
            'hash' => hash_hmac('sha256', $reader['id'] . '|' . $reader['email'] . '|' . ($reader['password_hash'] ?? ''), self::AUTH_SALT),
            'expires' => time() + (86400 * 30),
        ];
        $val = base64_encode(json_encode($payload));
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        setcookie(self::READER_COOKIE_NAME, $val, [
            'expires' => time() + (86400 * 30),
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::READER_COOKIE_NAME] = $val;
    }

    /**
     * Clear persistent reader cookie
     */
    public static function clearReaderRememberCookie(): void
    {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        setcookie(self::READER_COOKIE_NAME, '', [
            'expires' => time() - 86400,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        if (isset($_COOKIE[self::READER_COOKIE_NAME])) {
            unset($_COOKIE[self::READER_COOKIE_NAME]);
        }
    }

    /**
     * Automatically restore reader session from persistent cookie
     */
    public static function restoreReaderSessionFromCookie(): bool
    {
        if (!empty($_SESSION['reader_id'])) {
            return true;
        }

        if (empty($_COOKIE[self::READER_COOKIE_NAME])) {
            return false;
        }

        try {
            $raw = base64_decode($_COOKIE[self::READER_COOKIE_NAME], true);
            if (!$raw) return false;
            $data = json_decode($raw, true);
            if (!is_array($data) || empty($data['id']) || empty($data['hash']) || empty($data['expires'])) {
                return false;
            }

            if ($data['expires'] < time()) {
                self::clearReaderRememberCookie();
                return false;
            }

            $db = Database::getInstance();
            $reader = $db->fetch("SELECT * FROM readers WHERE id = :id LIMIT 1", ['id' => (int)$data['id']]);
            if (!$reader) {
                self::clearReaderRememberCookie();
                return false;
            }

            $expectedHash = hash_hmac('sha256', $reader['id'] . '|' . $reader['email'] . '|' . ($reader['password_hash'] ?? ''), self::AUTH_SALT);
            if (!hash_equals($expectedHash, $data['hash'])) {
                self::clearReaderRememberCookie();
                return false;
            }

            $_SESSION['reader_id'] = (int)$reader['id'];
            $_SESSION['reader_name'] = $reader['name'];
            $_SESSION['reader_email'] = $reader['email'];
            $_SESSION['reader_avatar'] = $reader['avatar_url'] ?? null;
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check if public reader is logged in
     */
    public static function readerCheck(): bool
    {
        self::startSession();
        if (!empty($_SESSION['reader_id'])) {
            return true;
        }
        return self::restoreReaderSessionFromCookie();
    }

    /**
     * Get currently logged-in reader
     */
    public static function reader(): ?array
    {
        self::startSession();
        if (!self::readerCheck()) {
            return null;
        }

        if (!array_key_exists('reader_avatar', $_SESSION) && !empty($_SESSION['reader_id'])) {
            $db = Database::getInstance();
            $_SESSION['reader_avatar'] = $db->fetchColumn("SELECT avatar_url FROM readers WHERE id = :id", ['id' => (int)$_SESSION['reader_id']]) ?: null;
        }

        return [
            'id' => (int)$_SESSION['reader_id'],
            'name' => $_SESSION['reader_name'] ?? 'Reader',
            'email' => $_SESSION['reader_email'] ?? '',
            'avatar_url' => $_SESSION['reader_avatar'] ?? null,
        ];
    }

    /**
     * Public Reader Login
     */
    public static function readerLogin(string $email, string $password): bool
    {
        self::startSession();
        $db = Database::getInstance();
        $reader = $db->fetch("SELECT * FROM readers WHERE email = :email LIMIT 1", ['email' => strtolower(trim($email))]);
        if (!$reader || !password_verify($password, $reader['password_hash'])) {
            return false;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['reader_id'] = (int)$reader['id'];
        $_SESSION['reader_name'] = $reader['name'];
        $_SESSION['reader_email'] = $reader['email'];
        $_SESSION['reader_avatar'] = $reader['avatar_url'] ?? null;

        // Persist reader session across browser restarts
        self::setReaderRememberCookie($reader);

        return true;
    }

    /**
     * Public Reader Registration
     */
    public static function readerRegister(string $name, string $email, string $password): array
    {
        self::startSession();
        $db = Database::getInstance();
        $name = trim($name);
        $email = strtolower(trim($email));

        if (mb_strlen($name) < 2 || mb_strlen($name) > 80) {
            return ['success' => false, 'message' => 'Please enter a valid name (2-80 characters).'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please provide a valid email address.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
        }

        $existing = $db->fetch("SELECT id FROM readers WHERE email = :email", ['email' => $email]);
        if ($existing) {
            return ['success' => false, 'message' => 'This email is already registered. Please log in instead.'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->execute(
            "INSERT INTO readers (name, email, password_hash, created_at) VALUES (:name, :email, :hash, NOW())",
            [
                'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'email' => $email,
                'hash' => $passwordHash,
            ]
        );

        $readerId = (int)$db->lastInsertId();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['reader_id'] = $readerId;
        $_SESSION['reader_name'] = $name;
        $_SESSION['reader_email'] = $email;
        $_SESSION['reader_avatar'] = null;

        $newReader = $db->fetch("SELECT * FROM readers WHERE id = :id LIMIT 1", ['id' => $readerId]);
        if ($newReader) {
            self::setReaderRememberCookie($newReader);
        }

        return ['success' => true, 'message' => 'Registration successful!'];
    }

    /**
     * Public Reader Logout
     */
    public static function readerLogout(): void
    {
        self::startSession();
        unset($_SESSION['reader_id'], $_SESSION['reader_name'], $_SESSION['reader_email'], $_SESSION['reader_avatar']);
        self::clearReaderRememberCookie();
    }

    /**
     * Enforce RBAC Authentication Guard
     */
    public static function requireAuth(array $allowedRoles = []): array
    {
        self::startSession();

        if (!self::check()) {
            $redirectUrl = url('admin/login.php?error=' . urlencode('Please log in to access the control panel.'));
            if (!headers_sent()) {
                header('Location: ' . $redirectUrl);
            } else {
                echo "<script>window.location.href=" . json_encode($redirectUrl) . ";</script>";
            }
            exit;
        }

        if (!empty($allowedRoles) && !self::hasRole($allowedRoles)) {
            $redirectUrl = url('admin/dashboard.php?error=' . urlencode('Access Denied: Insufficient editorial privileges.'));
            if (!headers_sent()) {
                header('Location: ' . $redirectUrl);
            } else {
                echo "<script>window.location.href=" . json_encode($redirectUrl) . ";</script>";
            }
            exit;
        }

        return self::user();
    }

    /**
     * Generate CSRF Token for HTML Forms
     */
    public static function generateCsrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF Token from POST request
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        self::startSession();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check if IP is rate limited (max 5 failed attempts in 15 mins)
     */
    private static function isRateLimited(string $ip): bool
    {
        $db = Database::getInstance();
        $fifteenMinsAgo = date('Y-m-d H:i:s', time() - 900);
        $attempts = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM login_attempts WHERE ip_address = :ip AND attempted_at > :t",
            ['ip' => $ip, 't' => $fifteenMinsAgo]
        );

        return $attempts >= 5;
    }

    /**
     * Log failed login attempt
     */
    private static function recordLoginAttempt(string $ip): void
    {
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO login_attempts (ip_address, attempted_at) VALUES (:ip, NOW())",
            ['ip' => $ip]
        );
    }
}

