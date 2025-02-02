<?php

return [
    'defaults'    => [
        'min_password_length'   => 6,
        'password_hash_algo'    => PASSWORD_DEFAULT,
        'password_hash_options' => [],
    ],
    'session'     => [
        'key'             => 'user_id',
        'expire_on_close' => false,
    ],
    'roles'       => [
        'admin' => 1,
        'user'  => 2,
    ],
    'permissions' => [
        'create_event'     => 1,
        'edit_event'       => 2,
        'delete_event'     => 3,
        'view_attendees'   => 4,
        'export_attendees' => 5,
    ],
];