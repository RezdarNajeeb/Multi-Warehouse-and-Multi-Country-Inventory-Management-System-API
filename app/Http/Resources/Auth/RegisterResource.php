<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="RegisterResource",
 *     title="Register Resource",
 *     description="Register response with user data",
 *     type="object",
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource")
 * )
 */
class RegisterResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return (new UserResource($this->resource))->toArray($request);
  }
}
