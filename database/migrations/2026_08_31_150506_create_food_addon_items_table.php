<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_addon_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('addon_id')
                ->constrained('food_addons')
                ->cascadeOnDelete();

            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->boolean('is_available')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_addon_items');
    }
};