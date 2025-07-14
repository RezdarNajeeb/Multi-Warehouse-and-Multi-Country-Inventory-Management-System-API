<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained('products')->restrictOnDelete();
            $table->foreignIdFor(Warehouse::class)->constrained('warehouses')->restrictOnDelete();
            $table->foreignIdFor(Supplier::class)->nullable()->constrained('suppliers')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->enum('transaction_type', ['in', 'out']);
            $table->timestamp('date'); // the date of the transaction
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable(); // can be useful for reversing transactions or adding context
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
