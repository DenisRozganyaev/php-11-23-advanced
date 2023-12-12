<?php

namespace Core;

use PDO;

class Db
{
    static protected PDO|null $instance = null;

    static public function connect(): PDO
    {
        if (is_null(static::$instance)) {
            $dsn = "mysql:host=mysql_db;dbname=mvc_db";
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];

            static::$instance = new PDO(
                $dsn,
                'root',
                'secret',
                $options
            );
        }

        return static::$instance;
    }
}
