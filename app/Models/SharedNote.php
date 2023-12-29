<?php

namespace App\Models;

class SharedNote extends \Core\Model
{
    public static string|null $tableName = 'shared_notes';

    public int $id, $user_id, $note_id;
}
