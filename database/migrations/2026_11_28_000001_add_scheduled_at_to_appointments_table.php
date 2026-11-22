<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Keeps the appointment time even after a cancellation releases the slot.
            $table->dateTime('scheduled_at')->nullable()->after('time_slot_id');
            $table->index(['doctor_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'scheduled_at']);
            $table->dropColumn('scheduled_at');
        });
    }
};
