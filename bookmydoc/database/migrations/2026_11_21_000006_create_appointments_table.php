<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();

            // Unique: one slot can hold only one appointment (conflict prevention).
            // Nullable so a cancelled appointment can release its slot by setting this to NULL.
            $table->foreignId('time_slot_id')->nullable()->unique()->constrained()->nullOnDelete();

            $table->enum('status', ['Scheduled', 'Completed', 'Cancelled', 'No-Show'])->default('Scheduled');
            $table->text('chief_complaint')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'created_at']); // for the daily bookings report
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
