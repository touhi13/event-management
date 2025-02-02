<?php
namespace Middleware;

use Core\Auth;
use Core\Middleware;

class GuestMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (Auth::getInstance()->isAuthenticated()) {
            header('Location: /events');
            exit;
        }
        return true;
    }
}
