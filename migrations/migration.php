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

    public function __construct()
    {
        try {
            db()->beginTransaction();

            $this->createMigrationTable();
            $this->runMigrations();

            if (db()->inTransaction()) {
                db()->commit();
            }
        } catch (PDOException $exception) {
            d($exception->getMessage(), $exception->getTrace());
            if (db()->inTransaction()) {
                db()->rollBack();
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

        foreach($migrations as $migration) {
            if (!$this->checkIfMigrationWasRun($migration)) {
                d("- Run '$migration' ...");
                $query = $this->getQueryByFile($migration);

                if ($query->execute()) {
                    $this->logIntoMigrations($migration);
                    d("- '$migration' DONE!");
                }
            } else {
                d("- '$migration' SKIPPED!");
            }
        }
        d('---- Migrations done! ----');
    }

    protected function logIntoMigrations(string $migration): void
    {
        $query = db()->prepare("INSERT INTO migrations (name) VALUES (:name)");
        $query->bindParam('name', $migration);
        $query->execute();
    }

    protected function checkIfMigrationWasRun($migration): bool
    {
        $query = db()->prepare("SELECT id FROM migrations WHERE name = :name");
        $query->bindParam('name', $migration);
        $query->execute();

        return (bool) $query->fetch();
    }
    
    protected function createMigrationTable(): void
    {
        d('---- Prepare migration table query ----');

        $query = $this->getQueryByFile(static::MIGRATIONS_FILE . '.sql');

        $result = match($query->execute()) {
            true => '- Migration table was created (or already exists)',
            false => '- Failed'
        };

        d($result, '---- Finished migration table query ----');
    }

    protected function getQueryByFile(string $migration): PDOStatement
    {
        $sql = file_get_contents(static::SCRIPTS_DIR . $migration);
        return db()->prepare($sql);
    }
} new Migration;
