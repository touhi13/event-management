<?php
namespace Core;

use Models\User;

class Auth
{
    private static ?Auth $instance = null;
    private array $user;
    private User $userModel;

    private function __construct()
    {
        $this->userModel = new User();
        if (isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->userModel->findByEmail($email);

        // Debug line (remove in production)
        error_log("Login attempt - Email: $email, User found: " . ($user ? 'Yes' : 'No'));

        if ($user && password_verify($password, $user['password'])) {
            // Debug line (remove in production)
            error_log("Password verification successful");

            $this->login($user);
            return true;
        }

        // Debug line (remove in production)
        error_log("Login failed - Password verification failed");

        return false;
    }

    public function hashPassword(string $password): string
    {
        // Using PASSWORD_DEFAULT with cost of 12 for better security
        $options = ['cost' => 12];
        return password_hash($password, PASSWORD_DEFAULT, $options);
    }

    public function login(array $user): void
    {
        $this->user       = $user;
        $_SESSION['user'] = $user;

        // Debug line (remove in production)
        error_log("User logged in - ID: " . $user['id']);
    }

    public function logout(): void
    {
        unset($this->user);
        unset($_SESSION['user']);
        session_destroy();

        // Debug line (remove in production)
        error_log("User logged out");
    }

    public function isAuthenticated(): bool
    {
        return isset($this->user);
    }

    public function isAdmin(): bool
    {
        return $this->isAuthenticated() && ($this->user['is_admin'] ?? false);
    }

    public function getUser(): ?array
    {
        return $this->user ?? null;
    }

    public function getUserId(): ?int
    {
        return $this->user['id'] ?? null;
    }
}
