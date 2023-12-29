<?php

namespace App\Validators\Notes;

use App\Models\Folder;
use App\Models\Note;
use Enums\SQL;

class UpdateNoteValidator extends Base
{
    public function __construct(protected Note $note){}

    public function validateTitle(array $fields): bool
    {
        if (!isset($fields['title'])) {
            return true;
        }

        $result = preg_match('/[\w\d\s\(\)\-]{3,}/i', $fields['title']);

        if (!$result) {
            $this->setError('title', 'Title should contain characters, numbers and _-() symbols and has length more than 2 symbols');
        }

        return $result && $this->checkTitleOnDuplication(
                $fields['title'],
                $fields['folder_id'] ?? $this->note->folder_id,
                $this->note->user_id
            );
    }

    public function validate(array $fields = []): bool
    {
        return !in_array(
            false,
            [
                parent::validate($fields),
                $this->validateFolderId($fields, false),
                $this->validateTitle($fields),
                $this->validateBooleanValue($fields, 'pinned'),
                $this->validateBooleanValue($fields, 'completed'),
            ]
        );
    }
}
