<?php
namespace Middleware;

use Core\Auth;
use Core\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!Auth::getInstance()->isAuthenticated()) {
            header('Location: /login');
            exit;
        }
        return true;
    }
}