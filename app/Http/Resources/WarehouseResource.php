<?php

namespace App\Http\Resources;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Warehouse
 *
 * @OA\Schema(
 *     schema="WarehouseResource",
 *     title="Warehouse Resource",
 *     description="Warehouse resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="country_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Central Warehouse"),
 *     @OA\Property(property="location", type="string", example="New York"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="relations",
 *         type="object",
 *         @OA\Property(property="country", ref="#/components/schemas/CountryResource")
 *     )
 * )
 */
class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->name,
            'location' => $this->location,

            $this->mergeWhen(!request()->routeIs('warehouses.index'), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),

            // only show when country relation is loaded
            'country' => $this->whenLoaded('country', fn() => new CountryResource($this->country)),
        ];
    }
}
