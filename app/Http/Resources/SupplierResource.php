<?php

namespace App\Http\Resources;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Supplier
 *
 * @OA\Schema(
 *     schema="SupplierResource",
 *     title="Supplier Resource",
 *     description="Supplier resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Acme Supplies"),
 *     @OA\Property(property="contact_info", type="object", example={"phone":"+1-555-1234","email":"sales@acme.com"}),
 *     @OA\Property(property="address", type="string", example="123 Industrial Rd, NY"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact_info' => $this->contact_info,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
