<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        // data for low stock report and inventory transactions
        Inventory::factory(10)->create([
            'quantity' => 5,
            'min_quantity' => 10,
        ])->each(function ($inventory) {
            InventoryTransaction::factory(3)->create([
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'supplier_id' => $inventory->product->supplier_id,
            ]);
        });
    }
}
