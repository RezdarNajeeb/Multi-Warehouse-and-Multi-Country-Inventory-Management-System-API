<?php

namespace App\Http\Resources;

use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MergeValue;

/**
 * @mixin InventoryTransaction
 *
 * @OA\Schema(
 *     schema="InventoryTransactionResource",
 *     title="Inventory Transaction Resource",
 *     description="Inventory transaction resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=5),
 *     @OA\Property(property="warehouse_id", type="integer", example=2),
 *     @OA\Property(property="supplier_id", type="integer", nullable=true, example=3),
 *     @OA\Property(property="quantity", type="integer", example=50),
 *     @OA\Property(property="transaction_type", type="enum(in,out)", example="in"),
 *     @OA\Property(property="date", type="string", format="date", example="2025-07-01"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Initial stock")
 * )
 */
class InventoryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'supplier_id' => $this->supplier_id,
            'quantity' => $this->quantity,
            'transaction_type' => $this->transaction_type,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'created_by' => $this->created_by,

            'notes' => $this->when($this->notes, fn() => $this->notes),

            'relations' => $this->when(
                $this->relationLoaded('product') ||
                    $this->relationLoaded('warehouse') ||
                    $this->relationLoaded('supplier') ||
                    $this->relationLoaded('createdBy'),
                fn() => new MergeValue([
                    'product'    => $this->whenLoaded('product', fn() => new ProductResource($this->product)),
                    'warehouse'  => $this->whenLoaded('warehouse', fn() => new WarehouseResource($this->warehouse)),
                    'supplier'   => $this->whenLoaded('supplier', fn() => new SupplierResource($this->supplier)),
                    'createdBy'  => $this->whenLoaded('createdBy', fn() => new UserResource($this->createdBy)),
                ])
            )
        ];
    }
}
