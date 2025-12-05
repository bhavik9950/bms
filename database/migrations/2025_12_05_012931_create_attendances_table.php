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
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->date('attendance_date');
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->tinyInteger('battery_percentage')->nullable();
            $table->boolean('is_wifi_on')->default(false);
            $table->tinyInteger('signal_strength')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'leave', 'checked_in', 'checked_out'])->default('absent');
            $table->timestamp('break_start_time')->nullable();
            $table->timestamp('break_end_time')->nullable();
            $table->decimal('break_duration', 5, 2)->nullable(); // in hours
            $table->text('late_reason')->nullable();
            $table->text('early_checkout_reason')->nullable();
            $table->enum('early_checkout_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->json('location_history')->nullable(); // Store location tracking data
            $table->json('device_info')->nullable(); // Store device information
            $table->timestamps();

            // Indexes for better performance
            $table->index(['staff_id', 'attendance_date']);
            $table->index('status');
            $table->index('check_in_time');
            $table->index('check_out_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
