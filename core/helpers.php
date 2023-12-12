<?php

use Core\Config;

function config(string $name): string | null
{
    return Config::get($name);
}


function db(): PDO
{
    return \Core\Db::connect();
}


