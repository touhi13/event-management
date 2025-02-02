<?php

return [
    'name'     => 'Event Management System',
    'url'      => 'http://localhost',
    'timezone' => 'UTC',
    'debug'    => true,
    'session'  => [
        'lifetime' => 120,
        'path'     => '/',
        'domain'   => null,
        'secure'   => false,
        'httponly' => true,
    ],
    'mail'     => [
        'host'       => 'smtp.mailtrap.io',
        'port'       => 2525,
        'username'   => null,
        'password'   => null,
        'encryption' => 'tls',
        'from'       => [
            'address' => 'noreply@example.com',
            'name'    => 'Event Management System',
        ],
    ],
];