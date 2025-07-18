<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="InventoryTransactionRequest",
 *      title="Inventory Transaction Request",
 *      description="Inventory transaction request body data",
 *      type="object",
 *      required={"product_id","warehouse_id","quantity","transaction_type"},
 *      @OA\Property(property="product_id", type="integer", example=5),
 *      @OA\Property(property="warehouse_id", type="integer", example=2),
 *      @OA\Property(property="supplier_id", type="integer", nullable=true, example=3),
 *      @OA\Property(property="quantity", type="integer", example=50),
 *      @OA\Property(property="transaction_type", type="enum(in,out)", example="in"),
 *      @OA\Property(property="date", type="string", format="date", example="2025-07-01"),
 *      @OA\Property(property="notes", type="string", nullable=true, example="Initial stock")
 * )
 */
class InventoryTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required_if:transaction_type,in|exists:suppliers,id',
            'quantity' => "required|integer|min:1",
            'transaction_type' => "required|in:in,out",
            'date' => "nullable|date|before_or_equal:now",
            'notes' => 'nullable|string|max:500',
        ];
    }
}
