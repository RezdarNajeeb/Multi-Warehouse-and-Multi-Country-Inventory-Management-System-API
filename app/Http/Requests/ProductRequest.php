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
 *      required={"name","sku","price"},
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
}
