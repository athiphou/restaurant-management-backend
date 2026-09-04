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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('restaurant_branches')->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained('staff_positions')->onDelete('restrict');
            $table->date('hire_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('position_id');
            $table->dropColumn(['hire_date', 'salary']);
        });
    }
};
