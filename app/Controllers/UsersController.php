<?php

namespace App\Controllers;

use App\Models\User;
use App\Validators\Users\AuthValidator;
use App\Validators\Users\RegisterValidator;
use Core\Controller;

class AuthController extends Controller
{
    public function signup(): array
    {
        $data = requestBody();
        $validator = new RegisterValidator();

        if ($validator->validate($data)) {
            $id = User::create([
                ...$data,
                'password' => password_hash($data['password'], PASSWORD_BCRYPT)
            ]);

            return $this->response(
                200,
                User::find($id)->toArray()
            );
        }

        return $this->response(200, [], $validator->getErrors());
    }

    public function signin(): array
    {
        $data = requestBody();
        $validator = new AuthValidator();

        if ($validator->validate($data)) {
            $user = User::findBy('email', $data['email']);
            if (password_verify($data['password'], $user->password)) {
                $token = random_bytes(32);

                return $this->response(200, compact('token'));
            }
        }

        return $this->response(200, [], $validator->getErrors());
    }
}