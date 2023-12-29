<?php

namespace App\Validators\Notes;

class CreateNotesValidator extends Base
{
    protected array $rules = [
        'title' => '/[\w\d\s\(\)\-]{3,}/i',
        'content' => '/.*$/i',
        'folder_id' => '/\d+/i'
    ];

    protected array $errors = [
        'title' => 'Title should contain characters, numbers and _-() symbols and has length more than 2 symbols',
        'folder_id' => 'folder id should be exists in request and has type int'
    ];

    public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            $this->validateFolderId($fields['folder_id']),
            !$this->checkTitleOnDuplication(
                $fields['title'],
                $fields['folder_id'],
                $fields['user_id']
            ),
            $this->validateBooleanValue($fields, 'pinned'),
            $this->validateBooleanValue($fields, 'completed'),
        ];

        return !in_array(false, $result);
    }
}
