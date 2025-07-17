<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="LoginResource",
 *     title="Login Resource",
 *     description="Login response with JWT token",
 *     type="object",
 *     @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
 * )
 */
class LoginResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'token' => $this->resource,
    ];
  }
}
