<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

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
     *      path="/register",
     *      tags={"Auth"},
     *      summary="Register a new user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *      ),
     *      @OA\Response(response=201, description="User registered", @OA\JsonContent(ref="#/components/schemas/RegisterResource")),
     *      @OA\Response(response=422, description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->createdResponse(new RegisterResource($result));
    }

    /**
     * @OA\Delete(
     *      path="/logout",
     *      tags={"Auth"},
     *      summary="Logout the authenticated user",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Logged out",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Successfully logged out"),
     *              @OA\Property(property="data", type="object", nullable=true, example=null)
     *          )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      )
     * )
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse(null, "Successfully logged out");
    }

    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"Auth"},
     *      summary="Login and retrieve JWT token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *      ),
     *      @OA\Response(response=200, description="Successful login", @OA\JsonContent(ref="#/components/schemas/LoginResource")),
     *      @OA\Response(response=401, description="Invalid credentials",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid credentials")
     *          )
     *      )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());

        if (!$token) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        return $this->successResponse(new LoginResource($token));
    }
}
