<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_addons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('food_id')
                ->constrained('foods')
                ->cascadeOnDelete();

            $table->string('name');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('max_items')->nullable();
            $table->boolean('is_available')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_addons');
    }
};