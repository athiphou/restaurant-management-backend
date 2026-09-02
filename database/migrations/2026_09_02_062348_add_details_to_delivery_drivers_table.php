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
        Schema::table('delivery_drivers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('restaurant_branches')->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('license_plate')->nullable();
            $table->enum('status', ['available', 'busy', 'offline'])->default('offline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_drivers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn(['phone', 'vehicle_type', 'license_plate', 'status']);
        });
    }
};