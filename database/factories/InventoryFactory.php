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
            'quantity' => fake()->numberBetween(0, 1000),
            'min_quantity' => fake()->numberBetween(5, 50),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn() => [
            'quantity' => $this->faker->numberBetween(0, 4),
            'minimum_quantity' => $this->faker->numberBetween(10, 20),
        ]);
    }

    public function highStock(): static
    {
        return $this->state(fn() => [
            'quantity' => $this->faker->numberBetween(100, 500),
            'minimum_quantity' => $this->faker->numberBetween(5, 20),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn() => [
            'quantity' => 0,
            'minimum_quantity' => $this->faker->numberBetween(5, 20),
        ]);
    }
}
