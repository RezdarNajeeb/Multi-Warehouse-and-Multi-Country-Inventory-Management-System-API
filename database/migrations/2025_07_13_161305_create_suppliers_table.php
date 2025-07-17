<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // we can adjust the length as needed
            $table->json('contact_info');
            $table->string('address')->nullable(); // if we need more than 255 chars then we can use text
            $table->timestamps();

            $table->index('name'); // for fast lookup searches
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
