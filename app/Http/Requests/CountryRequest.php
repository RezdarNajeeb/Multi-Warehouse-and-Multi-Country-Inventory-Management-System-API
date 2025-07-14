<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'name' => ($isCreate ? 'required' : 'sometimes') . '|string|min:4|max:100',

            'code' => [
                $isCreate ? 'required' : 'sometimes',
                'regex:/^([a-zA-Z]{2,3}|\d{3})$/',
                'unique:countries,code' . ($isCreate ? '' : ',' . $this->route('country')->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'The country code must be a valid 2 or 3 letter code or a 3 digit number.',
        ];
    }
}
