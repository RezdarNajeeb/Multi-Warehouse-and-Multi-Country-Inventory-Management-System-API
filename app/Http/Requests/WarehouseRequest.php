<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiredOrSometimes = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => $requiredOrSometimes . '|string|max:255',
            'location' => $requiredOrSometimes . '|string|max:120',
            'country_id' => 'required|integer|exists:countries,id',
        ];
    }
}
