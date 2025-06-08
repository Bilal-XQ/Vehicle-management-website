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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('maintenance_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->date('performed_at');
            $table->unsignedInteger('mileage_at_service');
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('service_provider')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('completed');
            $table->date('next_due_date')->nullable();
            $table->unsignedInteger('next_due_mileage')->nullable();
            $table->json('parts_used')->nullable();
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('vehicle_id');
            $table->index('maintenance_type_id');
            $table->index('performed_by');
            $table->index('performed_at');
            $table->index('status');
            $table->index('priority');
            $table->index('next_due_date');
            $table->index(['vehicle_id', 'performed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
