<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => $quantity = fake()->numberBetween(2, 100),
            'min_quantity' => fake()->numberBetween(1, $quantity - 1),
        ];
    }
}
