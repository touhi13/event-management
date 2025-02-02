<?php

return [
    'host'     => 'localhost',
    'database' => 'kuwaithouseclean_event_management',
    'username' => 'kuwaithouseclean_event',
    'password' => 'g5SrdZQpvD,(',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];