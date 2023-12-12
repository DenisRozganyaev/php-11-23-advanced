<?php

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/config/constants.php';
require_once BASE_DIR . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(BASE_DIR);
$dotenv->load();

class Migration
{
    const SCRIPTS_DIR = __DIR__ . '/scripts/';
    const MIGRATIONS_FILE = '0_migrations';

    protected PDO $db;

    public function __construct()
    {
        $this->db = db();

        try {
            $this->db->beginTransaction();

            $this->createMigrationTable();
            $this->runMigrations();

            if ($this->db->inTransaction()) {
                $this->db->commit();
            }
        } catch (PDOException $exception) {
            d($exception->getMessage(), $exception->getTrace());
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
        }
    }

    protected function runMigrations(): void
    {
        d('---- Fetching migrations ----');

        $migrations = scandir(static::SCRIPTS_DIR);
        $migrations = array_values(array_diff(
            $migrations,
            ['.', '..', static::MIGRATIONS_FILE . '.sql']
        ));

        foreach($migrations as $script) {
            $table = preg_replace('/[\d]+_/i', '', $script);

            // check if migration already was run
            // false =>
            //  - get file
            //  - prepare sql
            //  - run migration
            //  - write current script to migrations table
            // true => skip
        }

        d('---- Migrations done! ----');
    }

    protected function createMigrationTable(): void
    {
        d('---- Prepare migration table query ----');

        $sql = file_get_contents(static::SCRIPTS_DIR . static::MIGRATIONS_FILE . '.sql');
        $query = $this->db->prepare($sql);

        $result = match($query->execute()) {
            true => '- Migration table was created (or already exists)',
            false => '- Failed'
        };

        d($result, '---- Finished migration table query ----');
    }
} new Migration;
