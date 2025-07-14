<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');
        $requiredOrSometimes = $isCreate ? 'required' : 'sometimes';

        return [
            'name' => "{$requiredOrSometimes}|string|max:255",
            'sku' => "{$requiredOrSometimes}|string|max:60|unique:products,sku".
                ($isCreate ? '' : ",{$this->route('product')?->id}"),
            'status' => 'sometimes|boolean',
            'description' => 'sometimes|string|max:65535',
            'price' => "{$requiredOrSometimes}|numeric|min:0",
        ];


    }
}
