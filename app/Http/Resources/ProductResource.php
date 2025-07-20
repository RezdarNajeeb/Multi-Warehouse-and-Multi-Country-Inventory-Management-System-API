<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 *
 * @OA\Schema(
 *     schema="ProductResource",
 *     title="Product Resource",
 *     description="Product resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="iPhone 14"),
 *     @OA\Property(property="sku", type="string", example="IPH-14-128BLK"),
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="description", type="string", example="Apple iPhone 14 128GB Black"),
 *     @OA\Property(property="price", type="number", format="float", example=999.99),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="supplier", ref="#/components/schemas/SupplierResource"),
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'status' => $this->status,
            'price' => $this->price,

            $this->mergeWhen(!request()->routeIs('products.index'), [
                'description' => $this->description,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),

            'supplier' => $this->whenLoaded('supplier', fn() => new SupplierResource($this->supplier)),
        ];
    }
}
