<?php

namespace Core;

class Router
{
    static protected array $routes = [], $params = [];

    static protected array $convertTypes = [
        'd' => 'int',
        '.' => 'string'
    ];

    static public function add(string $route, array $params): void
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z_]+):([^}]+)}/', '(?P<$1>$2)', $route);
        $route = "/^$route$/i";

        static::$routes[$route] = $params;
    }

    static public function dispatch(string $uri): void
    {
        $uri = static::removeQueryVariables($uri);
        $uri = trim($uri, '/');

        if (static::match($uri)) {
            dd('done');
        }
    }

    static protected function match(string $uri): bool
    {
        foreach(static::$routes as $route => $params) {
            if (preg_match($route, $uri, $matches)) {
                static::$params = static::buildParams($route, $matches, $params);
                return true;
            }
        }

        throw new \Exception("Route [$uri] not found", 404);
    }

    static protected function buildParams(string $route, array $matches, array $params): array
    {
        preg_match_all('/\(\?P<[\w]+>(\\\\)?([\w\.][\+]*)\)/', $route, $types);
        dd($types);

        return $params;
    }

    static protected function removeQueryVariables(string $uri): string
    {
        return preg_replace('/([\w\/\-]+)\?([\w\/=\d%*&\?]+)/i', '$1', $uri);
    }
}
