<?php

namespace App\Validators\Notes;

use App\Models\Folder;
use App\Validators\BaseValidator;
use Enums\SQL;

class UpdateNoteValidator extends BaseValidator
{
    protected array $rules = [
        'title' => '/[\w\d\s\(\)\-]{3,}/i',
        'content' => '/.*$/i',
    ];

    protected array $errors = [
        'title' => 'Title should contain characters, numbers and _-() symbols and has length more than 2 symbols',
    ];

    protected array $skip = ['user_id', 'updated_at', 'pinned', 'completed'];

    public function validateFolderId(array $fields): bool
    {
        if (empty($fields['folder_id'])) {
            return true;
        }

        return Folder::where('id', '=', $fields['folder_id'])
            ->startCondition()
            ->andWhere('user_id', '=', authId())
            ->orWhere('user_id', SQL::IS_OPERATOR->value, SQL::NULL->value)
            ->endCondition()
            ->exists();
    }

    public function validateBooleanValue(array $fields, string $key): bool
    {
        if (empty($fields[$key])) {
            return true;
        }

        return is_bool($fields[$key]);
    }

    public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            $this->validateFolderId($fields),
            $this->validateBooleanValue($fields, 'pinned'),
            $this->validateBooleanValue($fields, 'completed'),
        ];

        return !in_array(false, $result);
    }
}
