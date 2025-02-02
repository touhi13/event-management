<?php
namespace Controllers;

use Core\Auth;
use Core\Controller;
use Models\User;

class AuthController extends Controller
{
    private User $userModel;
    private Auth $auth;

    public function __construct()
    {
        $this->userModel = new User();
        $this->auth      = Auth::getInstance();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest($_POST, [
                'email'    => 'required|email',
                'password' => 'required|min:6',
            ]);

            if (empty($errors)) {
                // Debug line (remove in production)
                error_log("Login attempt with email: " . $_POST['email']);

                if ($this->auth->attempt($_POST['email'], $_POST['password'])) {
                    $this->redirect('/dashboard');
                } else {
                    $errors['auth'] = 'Invalid email or password';
                }
            }

            $this->render('auth/login', ['errors' => $errors]);
            return;
        }

        $this->render('auth/login');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest($_POST, [
                'username' => 'required|min:3',
                'email'    => 'required|email',
                'password' => 'required|min:6',
            ]);

            if (empty($errors)) {
                // Check if email already exists
                if ($this->userModel->findByEmail($_POST['email'])) {
                    $errors['email'] = 'Email already exists';
                } else {
                    // Hash password before saving
                    $userData             = $_POST;
                    $userData['password'] = $this->auth->hashPassword($_POST['password']);

                    if ($this->userModel->create($userData)) {
                        // Debug line (remove in production)
                        error_log("User registered successfully with email: " . $userData['email']);
                        $this->redirect('/login');
                    } else {
                        $errors['general'] = 'Registration failed';
                    }
                }
            }

            $this->render('auth/register', ['errors' => $errors]);
            return;
        }

        $this->render('auth/register');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('/login');
    }
}
