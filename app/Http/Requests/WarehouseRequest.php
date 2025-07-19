<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *      schema="WarehouseRequest",
 *      title="Warehouse Request",
 *      description="Warehouse request body data",
 *      type="object",
 *      required={"name","location","country_id"},
 *      @OA\Property(property="name", type="string", maxLength=255, example="Central Warehouse"),
 *      @OA\Property(property="location", type="string", maxLength=120, example="New York"),
 *      @OA\Property(property="country_id", type="integer", example=1)
 * )
 */
class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiredOrSometimes = $this->routeIs('warehouses.store') ? 'required' : 'sometimes';

        return [
            'name' => "$requiredOrSometimes|string|max:50",
            'location' => "$requiredOrSometimes|string|max:65535",
            'country_id' => "$requiredOrSometimes|integer|exists:countries,id",
        ];
    }

    public function messages(): array
    {
        return [
            'country_id.exists' => 'The selected country does not exist.',
        ];
    }
}
