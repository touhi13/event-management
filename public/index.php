<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Enable error reporting during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path constant
define('BASE_PATH', ''); // Changed from '/event-management'

session_start();

// Update route handling
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    'GET'  => [
        '/'                        => [Controllers\HomeController::class, 'index'],
        '/login'                   => [
            'controller' => Controllers\AuthController::class,
            'action'     => 'login',
            'middleware' => [Middleware\GuestMiddleware::class],
        ],
        '/register'                => [
            'controller' => Controllers\AuthController::class,
            'action'     => 'register',
            'middleware' => [Middleware\GuestMiddleware::class],
        ],
        '/logout'                  => [Controllers\AuthController::class, 'logout'],
        '/dashboard'               => [
            'controller' => Controllers\DashboardController::class,
            'action'     => 'index',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events'                  => [
            'controller' => Controllers\EventController::class,
            'action'     => 'index',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/create'           => [
            'controller' => Controllers\EventController::class,
            'action'     => 'create',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/edit'             => [
            'controller' => Controllers\EventController::class,
            'action'     => 'edit',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/view'             => [
            'controller' => Controllers\EventController::class,
            'action'     => 'view',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/admin/events'            => [
            'controller' => Controllers\EventController::class,
            'action'     => 'index',
            'middleware' => [
                Middleware\AuthMiddleware::class,
                Middleware\AdminMiddleware::class,
            ],
        ],
        '/events/register'         => [
            'controller' => Controllers\AttendeeController::class,
            'action'     => 'register',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/attendees'        => [
            'controller' => Controllers\AttendeeController::class,
            'action'     => 'list',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/attendees/export' => [
            'controller' => Controllers\AttendeeController::class,
            'action'     => 'exportAttendees',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/api/events'              => [
            'controller' => Controllers\EventController::class,
            'action'     => 'api',
        ],
    ],
    'POST' => [
        '/login'           => [
            'controller' => Controllers\AuthController::class,
            'action'     => 'login',
            'middleware' => [Middleware\GuestMiddleware::class],
        ],
        '/register'        => [
            'controller' => Controllers\AuthController::class,
            'action'     => 'register',
            'middleware' => [Middleware\GuestMiddleware::class],
        ],
        '/events/create'   => [
            'controller' => Controllers\EventController::class,
            'action'     => 'store',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/edit'     => [
            'controller' => Controllers\EventController::class,
            'action'     => 'update',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/delete'   => [
            'controller' => Controllers\EventController::class,
            'action'     => 'delete',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/register' => [
            'controller' => Controllers\AttendeeController::class,
            'action'     => 'register',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/store'    => [
            'controller' => Controllers\EventController::class,
            'action'     => 'store',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
        '/events/update'   => [
            'controller' => Controllers\EventController::class,
            'action'     => 'update',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
    ],
];

$method = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$method][$path])) {
    $route = $routes[$method][$path];

    // Handle route with middleware
    if (is_array($route) && isset($route['middleware'])) {
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            if (!$middleware->handle()) {
                exit;
            }
        }
        $controller = new $route['controller']();
        $action     = $route['action'];
    } else {
        // Handle simple route
        [$controllerClass, $action] = $route;
        $controller                 = new $controllerClass();
    }

    $controller->$action();
} else {
    header("HTTP/1.0 404 Not Found");
    require_once __DIR__ . '/../templates/errors/404.php';
}

//g5SrdZQpvD,(
