<?php

use App\Models\User;

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/Config/constants.php';
require_once BASE_DIR .'/vendor/autoload.php';

try {
    $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(BASE_DIR);
    $dotenv->load();

    $users = User::select()->get();
    foreach($users as $user) {
       d($user->getUserInfo());
    }
    dd();
    // TODO - include env variables
    if (!preg_match('/assets/i', $_SERVER['REQUEST_URI'])) {
        \Core\Router::dispatch($_SERVER['REQUEST_URI']);
    }
} catch (PDOException $exception) {
    dd("PDOException", $exception);
} catch (Exception $exception) {
    dd("Exception", $exception);
}
