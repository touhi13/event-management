<?php

namespace Controllers;

class HomeController
{
    public function index()
    {
        // Redirect to events page if logged in
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/events');
            exit;
        }

        // Redirect to login page if not logged in
        header('Location: ' . BASE_PATH . '/login');
        exit;
    }
}