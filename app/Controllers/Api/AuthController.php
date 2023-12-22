<?php

namespace App\Controllers\Api;

use App\Models\User;
use App\Validators\Auth\AuthValidator;
use App\Validators\Auth\RegisterValidator;
use Core\Controller;
use ReallySimpleJWT\Token;

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
                $expiration = time() + 3600;
                $token = Token::create($user->id, $user->password, $expiration, 'localhost');

                return $this->response(200, compact('token'));
            }
        }

        return $this->response(200, [], $validator->getErrors());
    }
}
