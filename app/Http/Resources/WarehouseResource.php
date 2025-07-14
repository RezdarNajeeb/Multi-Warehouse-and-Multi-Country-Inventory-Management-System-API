<?php

namespace App\Http\Resources;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Warehouse */
class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'countryId'  => $this->country_id,
            'name'       => $this->name,
            'location'   => $this->location,
            'createdAt'  => $this->created_at,
            'updatedAt'  => $this->updated_at,

            // only show when country relation is loaded
            'relations' => $this->whenLoaded('country', function () {
                return ['country' => new CountryResource($this->country)];
            }),
        ];
    }
}
