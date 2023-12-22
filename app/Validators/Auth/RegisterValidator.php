<?php

namespace App\Validators\Auth;

class RegisterValidator extends Base
{
    protected array $rules = [
        'email' => '/^[a-zA-Z0-9.!#$%&\'*+\/\?^_`{|}~-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i',
        'password' => '/[a-zA-Z0-9.!#$%&\'*+\/\?^_`{|}~-]{8,}/',
    ];

    protected array $errors = [
        'email' => 'Email is incorrect',
        'password' => 'Password is incorrect. Minimum length 8 symbols'
    ];

    public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            !$this->checkEmailOnExists($fields['email'])
        ];

        return !in_array(false, $result);
    }
}
