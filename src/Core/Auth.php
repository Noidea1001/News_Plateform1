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

        // Configure security cookie settings before session start
        $cookieParams = [
            'lifetime' => 0,
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
    }

    /**
     * Generate or verify unique client session fingerprint (IP + User-Agent)
     */
    private static function verifyFingerprint(): void
    {
        $currentFingerprint = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'));

        if (!isset($_SESSION['_fingerprint'])) {
            $_SESSION['_fingerprint'] = $currentFingerprint;
        } elseif ($_SESSION['_fingerprint'] !== $currentFingerprint) {
            // Potential session hijacking detected: destroy session & reset
            session_unset();
            session_destroy();
            session_start();
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

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
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

