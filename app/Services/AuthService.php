<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class AuthService
{
    public function __construct(protected UserRepository $users)
    {
        //
    }

    public function register(array $data): array
    {
        $data['password'] = bcrypt($data['password']);
        $user = $this->users->create($data);

        return [
            'user' => $user,
            'token' => JWTAuth::fromUser($user)
        ];
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function login(array $credentials): ?string
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        return $token;
    }
}
