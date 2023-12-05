<?php
use Core\Router;

Router::add(
    'users/{id:\d+}/edit', // users/54/edit => ['id' => 54]
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'edit',
        'method' => 'GET'
    ]
);
