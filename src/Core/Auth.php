<?php
namespace Core;

class Auth
{
    private static ?Auth $instance = null;
    private array $user;

    private function __construct()
    {
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

    public function login(array $user): void
    {
        $this->user       = $user;
        $_SESSION['user'] = $user;
    }

    public function logout(): void
    {
        unset($this->user);
        unset($_SESSION['user']);
        session_destroy();
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