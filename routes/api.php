<?php

\Core\Router::add('api/auth/registration', [
    'controller' => \App\Controllers\AuthController::class,
    'action' => 'signup',
    'method' => 'POST'
]);


\Core\Router::add('api/auth/login', [
    'controller' => \App\Controllers\AuthController::class,
    'action' => 'signin',
    'method' => 'POST'
]);
