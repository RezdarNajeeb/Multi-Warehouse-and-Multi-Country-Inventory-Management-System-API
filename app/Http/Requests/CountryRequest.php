<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="CountryRequest",
 *      title="Country Request",
 *      description="Country request body data",
 *      type="object",
 *      required={"name", "code"},
 *      @OA\Property(property="name", type="string", minLength=4, maxLength=100, example="United States"),
 *      @OA\Property(property="code", type="string", description="2/3-letter or 3-digit country code", example="US")
 * )
 */
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
                'regex:/^([A-Z]{2,3}|\d{3})$/',
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
