<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="InventoryTransferRequest",
 *      title="Inventory Transfer Request",
 *      description="Inventory transfer request body data",
 *      type="object",
 *      required={"product_id","source_warehouse_id","destination_warehouse_id","quantity"},
 *      @OA\Property(property="product_id", type="integer", example=5),
 *      @OA\Property(property="source_warehouse_id", type="integer", example=1),
 *      @OA\Property(property="destination_warehouse_id", type="integer", example=2),
 *      @OA\Property(property="quantity", type="integer", example=25),
 *      @OA\Property(property="date", type="string", format="date", nullable=true, example="2025-07-01 00:00:00"),
 *      @OA\Property(property="notes", type="string", nullable=true, example="Rebalancing stock levels")
 * )
 */
class InventoryTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'source_warehouse_id' => 'required|integer|exists:warehouses,id',
            'destination_warehouse_id' => 'required|integer|exists:warehouses,id|different:source_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'date' => 'nullable|date|before_or_equal:now',
            'notes' => 'nullable|string|max:65535',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'The selected product does not exist.',
            'source_warehouse_id.exists' => 'The selected source warehouse does not exist.',
            'destination_warehouse_id.exists' => 'The selected destination warehouse does not exist.',
            'destination_warehouse_id.different' => 'The destination warehouse must be different from the source warehouse.',
            'date.before_or_equal' => 'The date must be today or in the past.',
        ];
    }
}
