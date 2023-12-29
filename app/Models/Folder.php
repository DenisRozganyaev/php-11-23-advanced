<?php

namespace App\Models;

class Folder extends \Core\Model
{
    public static string|null $tableName = 'folders';

    public string $title, $created_at, $updated_at;
    public int|null $user_id;
}
