<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'source_warehouse_id' => 'required|integer|exists:warehouses,id|different:destination_warehouse_id',
            'destination_warehouse_id' => 'required|integer|exists:warehouses,id|different:source_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ];
    }
}
