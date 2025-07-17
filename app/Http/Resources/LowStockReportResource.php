<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="LowStockReportResource",
 *     title="Low Stock Report Resource",
 *     description="Low stock report representation",
 *     @OA\Property(property="productName", type="string", example="iPhone 14"),
 *     @OA\Property(property="SKU", type="string", example="IPH-14-128BLK"),
 *     @OA\Property(property="currentQuantity", type="integer", example=10),
 *     @OA\Property(property="minQuantity", type="integer", example=20),
 *     @OA\Property(property="warehouseLocation", type="string", example="New York"),
 *     @OA\Property(property="country", type="string", example="United States"),
 *     @OA\Property(property="supplierContactInfo", type="object", example={"phone":"+1-555-1234","email":"sales@acme.com"})
 * )
 */
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
            'product_name' => $this->product->name ?? null,
            'sku' => $this->product->sku ?? null,
            'current_quantity' => $this->quantity,
            'min_quantity' => $this->min_quantity,
            'warehouse_location' => $this->warehouse->location ?? null,
            'country' => $this->warehouse->country->name ?? null,
            'supplier_contact_info' => $this->product->supplier->contact_info ?? null,
        ];
    }
}
