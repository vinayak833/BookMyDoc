<?php

namespace Tests\Concerns;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Carbon;

trait CreatesClinicData
{
    protected function makeClinic(array $overrides = []): Clinic
    {
        return Clinic::create(array_merge([
            'name' => 'Clinic '.uniqid(),
            'address' => 'Bengaluru',
            'is_active' => true,
        ], $overrides));
    }

    protected function makeDoctor(?Clinic $clinic = null, array $userOverrides = []): Doctor
    {
        $clinic ??= $this->makeClinic();
        $user = User::factory()->create(array_merge(['role' => 'doctor'], $userOverrides));

        return Doctor::create([
            'user_id' => $user->id,
            'clinic_id' => $clinic->id,
            'reg_no' => 'REG-'.$user->id,
            'specialisation' => 'General Medicine',
            'consultation_fee' => 500,
        ]);
    }

    protected function makePatientUser(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge(['role' => 'patient'], $overrides));
        $user->ensurePatient();

        return $user;
    }

    protected function makeSlot(Doctor $doctor, ?Carbon $start = null, bool $booked = false): TimeSlot
    {
        $start ??= Carbon::tomorrow()->setTime(10, 0);

        return TimeSlot::create([
            'doctor_id' => $doctor->id,
            'slot_start' => $start,
            'slot_end' => $start->copy()->addMinutes(30),
            'is_booked' => $booked,
        ]);
    }

    protected function makeAppointment(Doctor $doctor, User $patientUser, ?Carbon $when = null): Appointment
    {
        $slot = $this->makeSlot($doctor, $when, true);

        return Appointment::create([
            'patient_id' => $patientUser->ensurePatient()->id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $doctor->clinic_id,
            'time_slot_id' => $slot->id,
            'scheduled_at' => $slot->slot_start,
            'status' => 'Scheduled',
        ]);
    }
}
