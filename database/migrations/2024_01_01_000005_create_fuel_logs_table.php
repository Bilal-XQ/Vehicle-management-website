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
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('logged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('fueled_at');
            $table->unsignedInteger('mileage');
            $table->decimal('liters', 8, 2);
            $table->decimal('cost_per_liter', 8, 3);
            $table->decimal('total_cost', 10, 2);
            $table->string('fuel_station')->nullable();
            $table->boolean('full_tank')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('vehicle_id');
            $table->index('logged_by');
            $table->index('fueled_at');
            $table->index(['vehicle_id', 'fueled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
