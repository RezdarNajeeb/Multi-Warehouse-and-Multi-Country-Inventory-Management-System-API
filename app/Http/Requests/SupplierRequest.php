<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiredOrSometimes = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => "{$requiredOrSometimes}|string|max:255",
            'contact_info' => "{$requiredOrSometimes}|array",
            'address' => "{$requiredOrSometimes}|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            'contact_info.array' => 'The contact information must be a valid object.',
        ];
    }
}
