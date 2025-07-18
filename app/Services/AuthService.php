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

    public function register(array $validated): array
    {
        $validated['password'] = bcrypt($validated['password']);
        $user = $this->users->create($validated);

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
