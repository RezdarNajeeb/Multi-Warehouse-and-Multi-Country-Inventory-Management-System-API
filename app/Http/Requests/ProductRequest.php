<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *      schema="ProductRequest",
 *      title="Product Request",
 *      description="Product request body data",
 *      type="object",
 *      required={"supplier_id","name","sku","price"},
 *      @OA\Property(property="supplier_id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", maxLength=255, example="iPhone 14"),
 *      @OA\Property(property="sku", type="string", maxLength=60, example="IPH-14-128BLK"),
 *      @OA\Property(property="status", type="boolean", example=true),
 *      @OA\Property(property="description", type="string", example="Apple iPhone 14 128GB Black"),
 *      @OA\Property(property="price", type="number", format="float", example=999.99)
 * )
 */
class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiredOrSometimes = $this->routeIs('products.store') ? 'required' : 'sometimes';

        return [
            'supplier_id' => "$requiredOrSometimes|exists:suppliers,id",
            'name' => "$requiredOrSometimes|string|max:255",
            'sku' => [
                $requiredOrSometimes,
                'string',
                'max:50',
                Rule::unique('products')->ignore($this->route('product')?->id)
            ],
            'status' => 'sometimes|boolean',
            'description' => 'sometimes|string|max:65535',
            'price' => "$requiredOrSometimes|numeric|min:0",
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'status.boolean' => 'The status must be true or false.',
        ];
    }
}
