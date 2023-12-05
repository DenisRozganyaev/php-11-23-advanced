<?php

namespace Core;

class Router
{
    static protected array $routes = [], $params = [];

    static public function add(string $route, array $params): void
    {
        // TODO - prepare route by regular ex
        static::$routes[$route] = $params;
    }

    static public function dispatch(string $uri): void
    {
    }
}
