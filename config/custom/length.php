<?php

return [
    'pagination' => [
        'per_page_max' => 1000,
    ],
    'admin_role' => [
        'name_max' => 50,
    ],
    'admin' => [
        'username_min' => 4,
        'username_max' => 20,
        'password_min' => 6,
        'password_max' => 12,
    ],
    'admin_nav' => [
        'path_max' => 150,
        'icon_max' => 50,
        'name_max' => 50,
        'route_max' => 255,
        'url_max' => 255,
    ],
];
