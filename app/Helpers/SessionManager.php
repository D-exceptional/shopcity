<?php
namespace App\Helpers;

class SessionManager
{
    /**
     * Start the session safely
     */
    public function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Regenerate session ID securely
     */
    public function regenerate(): void
    {
        if (!isset($_SESSION['_regenerated'])) {
            session_regenerate_id(true);
            $this->store('_regenerated', time());
        }
    }

    /**
     * Log in user by storing user data in session
     */
    public function login(array $user): void
    {
        $this->regenerate();
        $this->store('user', $user);
        $this->store('role', strtolower($user['role'] ?? ''));
        $this->store('login_time', time());
        $this->store('last_activity', time());
        $this->store('_csrf_token', bin2hex(random_bytes(32)));
    }

    /**
     * Validate session lifetime and activity
     */
    public function validate(
        int $absoluteMax = 7200,  // 2 hours max
        int $idleTimeout = 1800   // 30 min inactivity
    ): bool {
        $now = time();

        if (!$this->check()) {
            return false;
        }

        // Absolute lifetime
        if (isset($_SESSION['login_time']) &&
            ($now - $_SESSION['login_time']) > $absoluteMax
        ) {
            $this->destroy();
            return false;
        }

        // Inactivity timeout
        if (isset($_SESSION['last_activity']) &&
            ($now - $_SESSION['last_activity']) > $idleTimeout
        ) {
            $this->destroy();
            return false;
        }

        // Update last activity
        $this->store('last_activity', $now);
        return true;
    }

    /**
     * Destroy entire session
     */
    public function destroy(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Store value in session
     */
    public function store(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve value from session
     */
    public function retrieve(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Destroy a specific key
     */
    public function terminate(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Check if user is logged in
     */
    public function check(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Get user info
     */
    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Get user ID
     */
    public function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    /**
     * Get user role
     */
    public function role(): ?string
    {
        return strtolower($_SESSION['user']['role'] ?? null);
    }

    /**
     * Authorize by role
     */
    public function authorize(string $role): bool
    {
        return $this->check() && $this->role() === strtolower($role);
    }

    /**
     * Get CSRF token
     */
    public function token(): ?string
    {
        return $_SESSION['_csrf_token'] ?? null;
    }

    /**
     * Check if token is set
     */
    public function tokenSet(): bool
    {
        return isset($_SESSION['_csrf_token']);
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrf(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $sessionToken = $this->retrieve('_csrf_token') ?? null;

        if (!is_string($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Redirect to a URL
     */
    public function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }
}
