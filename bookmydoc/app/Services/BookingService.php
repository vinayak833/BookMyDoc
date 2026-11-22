<?php

namespace App\Services;

use App\Exceptions\BookingException;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Book a slot. The slot row is locked inside a transaction so two people
     * clicking at the same moment cannot both get it. The unique constraints in
     * the database are the second safety net.
     */
    public function book(Patient $patient, int $slotId, ?string $complaint = null): Appointment
    {
        return DB::transaction(function () use ($patient, $slotId, $complaint) {
            $slot = TimeSlot::with('doctor')->whereKey($slotId)->lockForUpdate()->first();

            if (! $slot) {
                throw new BookingException('That time slot does not exist.');
            }
            if ($slot->is_booked) {
                throw new BookingException('Sorry, that slot has just been booked. Please choose another.');
            }
            if ($slot->slot_start->isPast()) {
                throw new BookingException('That slot is in the past.');
            }

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $slot->doctor_id,
                'clinic_id' => $slot->doctor->clinic_id,
                'time_slot_id' => $slot->id,
                'scheduled_at' => $slot->slot_start,
                'status' => 'Scheduled',
                'chief_complaint' => $complaint,
            ]);

            $slot->update(['is_booked' => true]);

            return $appointment;
        });
    }

    /** Cancel an appointment and free its slot so it can be booked again. */
    public function cancel(Appointment $appointment): void
    {
        DB::transaction(function () use ($appointment) {
            $current = Appointment::whereKey($appointment->id)->lockForUpdate()->firstOrFail();

            if ($current->status !== 'Scheduled') {
                throw new BookingException('Only scheduled appointments can be cancelled.');
            }

            if ($current->time_slot_id) {
                TimeSlot::whereKey($current->time_slot_id)->update(['is_booked' => false]);
            }

            $current->update(['status' => 'Cancelled', 'time_slot_id' => null]);
        });
    }
}
