<?php
require_once __DIR__ . '/../vendor/autoload.php';

define('BASE_PATH', '/event-management');

session_start();

$routes = [
    'GET'  => [
        '/'                        => [Controllers\HomeController::class, 'index'],
        '/login'                   => [Controllers\AuthController::class, 'login'],
        '/register'                => [Controllers\AuthController::class, 'register'],
        '/logout'                  => [Controllers\AuthController::class, 'logout'],
        '/events'                  => [
            'controller' => Controllers\EventController::class,
            'action'     => 'index',
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
    ],
    'POST' => [
        '/login'           => [Controllers\AuthController::class, 'login'],
        '/register'        => [Controllers\AuthController::class, 'register'],
        '/events/register' => [
            'controller' => Controllers\AttendeeController::class,
            'action'     => 'register',
            'middleware' => [Middleware\AuthMiddleware::class],
        ],
    ],
];

$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = str_replace(BASE_PATH, '', $path);
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
    http_response_code(404);
    echo '404 Not Found';
}

//g5SrdZQpvD,(