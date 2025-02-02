<?php
namespace Middleware;

use Core\Auth;
use Core\Middleware;

class AdminMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!Auth::getInstance()->isAdmin()) {
            header('Location: /dashboard');
            exit;
        }
        return true;
    }
}