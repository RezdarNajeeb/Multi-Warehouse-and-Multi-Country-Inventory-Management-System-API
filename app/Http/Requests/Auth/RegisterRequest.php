<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     title="Register Request",
 *     description="Register request body data",
 *     type="object",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="Rezdar"),
 *     @OA\Property(property="email", type="string", format="email", example="rezdar@gmail.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:254|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }
}
