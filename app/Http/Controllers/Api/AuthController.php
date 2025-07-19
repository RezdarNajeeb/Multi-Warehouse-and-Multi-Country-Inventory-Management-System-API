<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @OA\Tag(name="Auth", description="Authentication Endpoints")
 */
class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected AuthService $authService)
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Auth"},
     *      summary="Register for a new account",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *      ),
     *      @OA\Response(response=201, description="User registered",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="User registered successfully"),
     *              @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *          )
     *      ),
     *
     *      @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->createdResponse($result);
    }

    /**
     * @OA\Delete(
     *      path="/api/logout",
     *      tags={"Auth"},
     *      summary="Logout the authenticated user",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=204, description="Logged out"),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthorized")
     * )
     */
    public function logout(): Response
    {
        $this->authService->logout();

        return $this->deletedResponse();
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      tags={"Auth"},
     *      summary="Login and retrieve JWT token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *      ),
     *      @OA\Response(response=200, description="Successful login",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="token", type="string", example="jwt.token.here")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Invalid credentials",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid credentials")
     *          )
     *      )
     * )
     *
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());

        return $this->successResponse([
            'token' => $token
        ]);
    }
}
