<?php

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained('products')->restrictOnDelete();
            $table->foreignIdFor(Warehouse::class)->constrained('warehouses')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(0); // we can adjust the type if needed
            $table->unsignedInteger('min_quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id']); // only one inventory record per product in a warehouse
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
