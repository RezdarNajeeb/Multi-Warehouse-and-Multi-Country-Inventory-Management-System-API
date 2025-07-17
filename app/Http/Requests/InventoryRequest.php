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
        $requiredOrSometimes = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'product_id'    => [
                $requiredOrSometimes,
                'exists:products,id',
                Rule::unique('inventories')
                    ->where('warehouse_id', $this->input('warehouse_id'))
                    ->ignore($this->inventory?->id),
            ],
            'warehouse_id'  => [$requiredOrSometimes, 'exists:warehouses,id'],
            'quantity'      => [$requiredOrSometimes, 'integer', 'min:0', 'gte:min_quantity'],
            'min_quantity'  => [$requiredOrSometimes, 'integer', 'min:0', 'lte:quantity'],
        ];
    }
}
