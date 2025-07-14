<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
