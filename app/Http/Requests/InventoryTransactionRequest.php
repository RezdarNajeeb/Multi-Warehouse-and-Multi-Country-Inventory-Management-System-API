<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'date' => "required|date|before_or_equal:now",
            'notes' => 'nullable|string|max:500',
        ];
    }
}
