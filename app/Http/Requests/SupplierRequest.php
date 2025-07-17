<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="SupplierRequest",
 *      title="Supplier Request",
 *      description="Supplier request body data",
 *      type="object",
 *      required={"name","contact_info","address"},
 *      @OA\Property(property="name", type="string", maxLength=255, example="Acme Supplies"),
 *      @OA\Property(property="contact_info", type="object", example={"phone":"+1-555-1234","email":"sales@acme.com"}),
 *      @OA\Property(property="address", type="string", maxLength=255, example="123 Industrial Rd, NY")
 * )
 */
class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiredOrSometimes = $this->routeIs('suppliers.store') ? 'required' : 'sometimes';

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
