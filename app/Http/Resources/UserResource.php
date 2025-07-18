<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class UserResource extends JsonResource
{

    /**
     * @OA\Schema(
     *     schema="UserResource",
     *     title="User Resource",
     *     description="User resource representation",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Rezdar"),
     *     @OA\Property(property="email", type="string", example="rezdar@example.com"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     *
     * @mixin User
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
