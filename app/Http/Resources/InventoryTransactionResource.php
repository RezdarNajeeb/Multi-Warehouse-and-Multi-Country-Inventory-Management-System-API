<?php

namespace App\Http\Resources;

use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MergeValue;

/** @mixin InventoryTransaction */
class InventoryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'productId' => $this->product_id,
            'warehouseId' => $this->warehouse_id,
            'supplierId' => $this->supplier_id,
            'quantity' => $this->quantity,
            'transactionType' => $this->transaction_type,
            'date' => $this->date,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,

            'createdBy' => $this->created_by,

            'notes' => $this->when($this->notes, fn () => $this->notes),

            'relations' => $this->when(
                $this->relationLoaded('product') ||
                $this->relationLoaded('warehouse') ||
                $this->relationLoaded('supplier') ||
                $this->relationLoaded('createdBy'),
                fn () => new MergeValue([
                    'product'    => $this->whenLoaded('product', fn () => new ProductResource($this->product)),
                    'warehouse'  => $this->whenLoaded('warehouse', fn () => new WarehouseResource($this->warehouse)),
                    'supplier'   => $this->whenLoaded('supplier', fn () => new SupplierResource($this->supplier)),
                    'createdBy'  => $this->whenLoaded('createdBy', fn () => new UserResource($this->createdBy)),
                ])
            )
        ];
    }
}
