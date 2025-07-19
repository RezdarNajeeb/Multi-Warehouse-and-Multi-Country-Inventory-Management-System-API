<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *      schema="InventoryRequest",
 *      title="Inventory Request",
 *      description="Inventory request body data",
 *      type="object",
 *      required={"product_id","warehouse_id","quantity","min_quantity"},
 *      @OA\Property(property="product_id", type="integer", example=5),
 *      @OA\Property(property="warehouse_id", type="integer", example=2),
 *      @OA\Property(property="quantity", type="integer", example=150),
 *      @OA\Property(property="min_quantity", type="integer", example=20)
 * )
 */
class InventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiredOrSometimes = $this->routeIs('inventories.store') ? 'required' : 'sometimes';

        return [
            'product_id' => "$requiredOrSometimes|integer|exists:products,id",
            'warehouse_id' => "$requiredOrSometimes|integer|exists:warehouses,id",
            'quantity' => [$requiredOrSometimes, 'integer', 'min:0'],
            'min_quantity' => [$requiredOrSometimes, 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'The selected product does not exist.',
            'warehouse_id.exists' => 'The selected warehouse does not exist.',
        ];
    }
}
