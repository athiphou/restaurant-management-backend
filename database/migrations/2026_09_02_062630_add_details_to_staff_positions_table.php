<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff_positions', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->decimal('base_salary', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_positions', function (Blueprint $table) {
            $table->dropColumn(['title', 'base_salary']);
        });
    }
};
