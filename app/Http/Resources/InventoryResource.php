<?php

namespace App\Http\Resources;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MergeValue;

/** @mixin Inventory */
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
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,

            'relations' => $this->when(
                $this->relationLoaded('product') ||
                $this->relationLoaded('warehouse'),
                fn () => new MergeValue([
                    'product'    => $this->whenLoaded('product',
                        fn () => new ProductResource($this->product)),
                    'warehouse'  => $this->whenLoaded('warehouse',
                        fn () => new WarehouseResource($this->warehouse)),
                ])
            )
        ];
    }
}
