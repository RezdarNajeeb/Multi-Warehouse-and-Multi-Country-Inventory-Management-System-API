<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowStockReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'productName' => $this->product->name ?? null,
            'SKU' => $this->product->sku ?? null,
            'currentQuantity' => $this->quantity,
            'minQuantity' => $this->min_quantity,
            'warehouseLocation' => $this->warehouse->location ?? null,
            'country' => $this->warehouse->country->name ?? null,
            'supplierContactInfo' => $this->product->supplier->contact_info ?? null,
        ];
    }
}
