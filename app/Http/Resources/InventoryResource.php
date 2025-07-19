<?php

namespace App\Http\Resources;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MergeValue;

/**
 * @mixin Inventory
 *
 * @OA\Schema(
 *     schema="InventoryResource",
 *     title="Inventory Resource",
 *     description="Inventory resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=5),
 *     @OA\Property(property="warehouse_id", type="integer", example=2),
 *     @OA\Property(property="quantity", type="integer", example=150),
 *     @OA\Property(property="min_quantity", type="integer", example=20),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *          property="relations",
 *          type="object",
 *          @OA\Property(property="product", ref="#/components/schemas/ProductResource"),
 *          @OA\Property(property="warehouse", ref="#/components/schemas/WarehouseResource")
 *     )
 * )
 */
class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity' => $this->quantity,
            'min_quantity' => $this->min_quantity,

            $this->mergeWhen(request()->routeIs('inventories.show'), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),

            'relations' => $this->when(
                $this->relationLoaded('product') ||
                    $this->relationLoaded('warehouse'),
                fn() => new MergeValue([
                    'product'    => $this->whenLoaded(
                        'product',
                        fn() => new ProductResource($this->product)
                    ),
                    'warehouse'  => $this->whenLoaded(
                        'warehouse',
                        fn() => new WarehouseResource($this->warehouse)
                    ),
                ])
            )
        ];
    }
}
