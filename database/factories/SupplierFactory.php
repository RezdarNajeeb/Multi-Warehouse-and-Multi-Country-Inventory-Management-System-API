<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'contact_info' => [
                'email' => fake()->companyEmail(),
                'phone' => fake()->phoneNumber(),
            ],
            'address' => fake()->address(),
        ];
    }
}
