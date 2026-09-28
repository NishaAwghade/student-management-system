<?php
/**
 * Session
 * Small helper around PHP sessions used for authentication,
 * role-based access control and flash messages.
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    public static function role(): ?string
    {
        return self::get('role');
    }

    /** Redirect to login unless the logged-in user has the required role. */
    public static function requireRole(string $role): void
    {
        self::start();
        if (!self::isLoggedIn() || self::role() !== $role) {
            header('Location: ' . BASE_URL . 'index.php?err=auth');
            exit;
        }
    }

    // ---- CSRF protection ----
    /** Returns the current CSRF token, generating one if it doesn't exist yet. */
    public static function csrfToken(): string
    {
        if (!self::has('csrf_token')) {
            self::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return self::get('csrf_token');
    }

    // ---- Flash messages (one-time messages shown after redirect) ----
    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    public static function getFlash(string $type): ?string
    {
        if (!empty($_SESSION['flash'][$type])) {
            $msg = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $msg;
        }
        return null;
    }
}
