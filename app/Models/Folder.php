<?php

namespace App\Models;

class Folder extends \Core\Model
{
    protected static string|null $tableName = 'folders';

    public string $title, $created_at, $updated_at;
    public int $user_id;
}
