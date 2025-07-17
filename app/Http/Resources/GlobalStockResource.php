<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GlobalStockResource extends JsonResource
{
    /**
     * @mixin Inventory, Product
     *
     * @OA\Schema(
     *     schema="GlobalStockResource",
     *     title="Global Stock Resource",
     *     description="Global stock resource representation",
     *     @OA\Property(property="product_id", type="integer", example=5),
     *     @OA\Property(property="name", type="string", example="Product Name"),
     *     @OA\Property(property="sku", type="string", example="1234567890"),
     *     @OA\Property(property="total_stock", type="integer", example=150),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id'   => $this->product_id,
            'name'         => $this->product->name ?? null,
            'sku'          => $this->product->sku ?? null,
            'total_stock'  => $this->total_stock,
        ];
    }
}
