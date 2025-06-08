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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_make_id')->constrained()->onDelete('restrict');
            $table->string('model');
            $table->year('year');
            $table->string('license_plate')->unique();
            $table->string('vin')->unique()->nullable();
            $table->enum('status', ['active', 'maintenance', 'retired', 'out_of_service'])->default('active');
            $table->enum('fuel_type', ['gasoline', 'diesel', 'electric', 'hybrid', 'lpg'])->nullable();
            $table->string('color')->nullable();
            $table->unsignedInteger('mileage')->default(0);
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('registration_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->json('specifications')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for frequently queried columns
            $table->index('license_plate');
            $table->index('status');
            $table->index('year');
            $table->index('vehicle_make_id');
            $table->index('fuel_type');
            $table->index('registration_expiry');
            $table->index('insurance_expiry');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
