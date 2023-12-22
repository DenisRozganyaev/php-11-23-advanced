<?php
namespace Core\Traits;

use PDO;

trait Queryable
{
    static protected string|null $tableName = null;

    static protected string $query = '';

    private array $commands = [];

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

    static public function all(): array
    {
        return static::select()->get();
    }

    static public function find(int $id): static|false
    {
        $query = db()->prepare("SELECT * FROM " . static::$tableName . " WHERE id = :id");
        $query->bindParam('id', $id);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    static public function findBy(string $column, $value): static|false
    {
        $query = db()->prepare("SELECT * FROM " . static::$tableName . " WHERE $column = :$column");
        $query->bindParam($column, $value);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    /**
     * INSERT INTO table_name
     * (columns...) [placeholders]
     * VALUES
     * (values....) [placeholders]
     * @param array $fields
     * [
     *  'name' => '...',
     *  'content' => '...'
     * ]
     * @return  null|static
     */
    static public function create(array $fields): null|static
    {
        $params = static::prepareQueryParams($fields);
        $query = db()->prepare("INSERT INTO " . static::$tableName . " ($params[keys]) VALUES ($params[placeholders])");

        if (!$query->execute($fields)) {
            return null;
        }

        $query->closeCursor();

        return static::find(db()->lastInsertId());
    }

    static public function destroy(int $id): bool
    {
        $query = db()->prepare("DELETE FROM " . static::$tableName . " WHERE id = :id");
        $query->bindParam('id', $id);
        return $query->execute();
    }

    static protected function prepareQueryParams(array $fields): array
    {
        $keys = array_keys($fields);
        $placeholders = preg_filter('/^/', ':', $keys);

        return [
            'keys' => implode(', ', $keys),
            'placeholders' => implode(', ', $placeholders)
        ];
    }

    static public function __callStatic(string $name, array $args): mixed
    {
        if (in_array($name, ['where'])) {
            $obj = static::select();
            return call_user_func_array([$obj, $name], $args);
        }
    }

    public function __call(string $name, array $args): mixed
    {
        if (in_array($name, ['where'])) {
            return call_user_func_array([$this, $name], $args);
        }
    }

    static protected function resetQuery(): void
    {
        static::$query = '';
    }

    protected function where(string $column, string $operator, $value = null): static
    {
        if ($this->prevent(['group', 'limit', 'order', 'having'])) {
            throw new \Exception(
                static::class .
                ": WHERE can not be after ['group', 'limit', 'order', 'having']"
            );
        }

        $obj = in_array('select', $this->commands) ? $this : static::select();

        if (
            !is_null($value) &&
            !is_bool($value) &&
            !is_numeric($value) &&
            !is_array($value) &&
            !in_array($operator, ['IN', 'NOT IN'])
        ) {
            $value = "'$value'";
        }

        if (is_null($value)) {
            $value = 'NULL';
        }

        if (is_array($value)) {
            $value = array_map(fn($item) => is_string($item) ? "'$item'" : $item, $value);
            $value = '('. implode(', ', $value) .')';
        }

        if (!in_array('where', $obj->commands)) {
            static::$query .= "WHERE";
        }

        static::$query .= " $column $operator $value";
        $this->commands[] = 'where';

        return $obj;
    }

    public function sql(): string
    {
        return static::$query;
    }

    public function get(): array
    {
        return db()->query(static::$query)->fetchALl(PDO::FETCH_CLASS, static::class);
    }

    protected function prevent(array $allowedMethods): bool
    {
        foreach ($allowedMethods as $method) {
            if (in_array($method, $this->commands)) {
                return true;
            }
        }

        return false;
    }
}
