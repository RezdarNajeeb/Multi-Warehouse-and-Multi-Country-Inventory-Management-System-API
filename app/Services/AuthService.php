<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use JWTAuth;

class AuthService
{
    public function __construct(protected UserRepository $users)
    {
        //
    }

    public function register(array $validated): User
    {
        $validated['password'] = bcrypt($validated['password']);
        $validated['email'] = strtolower($validated['email']);

        return $this->users->create($validated);
    }

    public function logout(): void
    {
        auth()->logout();
    }

    /**
     * @throws AuthenticationException
     */
    public function login(array $credentials): string
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }

        return $token;
    }
}
