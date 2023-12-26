<?php

namespace App\Validators\Notes;

use App\Models\Folder;
use App\Validators\BaseValidator;
use Enums\SQL;

class CreateNotesValidator extends BaseValidator
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

    protected array $skip = ['user_id', 'updated_at'];

    public function validateFolderId(int $id): bool
    {
        return Folder::where('id', '=', $id)
            ->startCondition()
            ->andWhere('user_id', '=', authId())
            ->orWhere('user_id', SQL::IS_OPERATOR->value, SQL::NULL->value)
            ->endCondition()
            ->exists();
    }

    public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            $this->validateFolderId($fields['folder_id'])
        ];

        return !in_array(false, $result);
    }
}
