<?php
use Core\Router;

Router::add(
    'users',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'index',
        'method' => 'GET'
    ]
);

Router::add(
    'users/{id:\d+}',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'show',
        'method' => 'GET'
    ]
);

Router::add(
    'users/{id:\d+}/edit', // users/54/edit => ['id' => 54]
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'edit',
        'method' => 'GET'
    ]
);

Router::add(
    'users/{id:\d+}/update',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'update',
        'method' => 'POST'
    ]
);


Router::add(
    'posts/{post_id:\d+}/comment/{comment_id:\d+}',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'show',
        'method' => 'GET'
    ]
);
