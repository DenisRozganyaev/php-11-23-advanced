<?php

namespace App\Validators\Folders;

use App\Validators\BaseValidator;

class CreateFolderValidator extends BaseValidator
{
    protected array $rules = [
        'title' => '/[\w\d\s\(\)\-]{3,}/i'
    ];

    protected array $errors = [
        'title' => 'Title should contain characters, numbers and _-() symbols and has length more than 2 symbols'
    ];

    protected array $skip = ['user_id'];

    protected function checkOnDuplicateTitle(null|int $userId, string $title): bool
    {
//        dd(
//            Folder::where('user_id', '=', $userId)
//                ->andWhere('title', '=', $title)
//                ->get()
//        );
    }

    public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),

        ];

        return !in_array(false, $result);
    }
}
