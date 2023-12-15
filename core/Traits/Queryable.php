<?php
namespace Core\Traits;

use PDO;

trait Queryable
{
    static protected string|null $tableName = null;

    static protected string $query = '';

    protected array $commands = [];

    /**
     * @param array $columns (e.g. ['name', 'surname'], ['users.name as u_name']) => SELECT name, surname ....
     * @return static
     */
    static public function select(array $columns = ['*']): static
    {
        static::resetQuery();
        static::$query = "SELECT " . implode(', ', $columns) . " FROM " . static::$tableName . " ";

        $obj = new static;
        $obj->commands[] = 'select';

        return $obj;
    }

    static protected function resetQuery(): void
    {
        static::$query = '';
    }

    public function get(): array
    {
        return db()->query(static::$query)->fetchALl(PDO::FETCH_CLASS, static::class);
    }
}
