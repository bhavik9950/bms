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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('task_type', ['general', 'urgent', 'maintenance', 'customer_service', 'inventory', 'other'])->default('general');
            $table->uuid('assigned_by_id');
            $table->uuid('assigned_to_id');
            $table->foreign('assigned_by_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('assigned_to_id')->references('id')->on('staff')->onDelete('cascade');
            $table->uuid('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('customers')->onDelete('set null');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_geo_fence_enabled')->default(false);
            $table->decimal('max_radius', 8, 2)->nullable(); // in meters
            $table->timestamp('start_date_time')->nullable();
            $table->timestamp('end_date_time')->nullable();
            $table->enum('status', ['new', 'in_progress', 'hold', 'completed', 'cancelled'])->default('new');
            $table->date('for_date');
            $table->json('attachments')->nullable(); // Store file attachments
            $table->json('location_history')->nullable(); // Track location updates
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['assigned_to_id', 'status']);
            $table->index(['assigned_by_id']);
            $table->index('for_date');
            $table->index('status');
            $table->index(['start_date_time', 'end_date_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
