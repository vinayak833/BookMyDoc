<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlotConstraintTest extends TestCase
{
    use RefreshDatabase;

    private function makeDoctor(): Doctor
    {
        $clinic = Clinic::create(['name' => 'Test Clinic', 'address' => 'Bengaluru']);
        $user = User::factory()->create(['role' => 'doctor']);

        return Doctor::create([
            'user_id' => $user->id,
            'clinic_id' => $clinic->id,
            'reg_no' => 'REG-'.$user->id,
            'specialisation' => 'General Medicine',
            'consultation_fee' => 500,
        ]);
    }

    private function makePatient(): Patient
    {
        $user = User::factory()->create(['role' => 'patient']);

        return Patient::create([
            'user_id' => $user->id,
            'uhid' => 'UH'.str_pad((string) $user->id, 10, '0', STR_PAD_LEFT),
        ]);
    }

    public function test_a_doctor_cannot_have_two_slots_at_the_same_start_time(): void
    {
        $doctor = $this->makeDoctor();
        $data = [
            'doctor_id' => $doctor->id,
            'slot_start' => '2027-01-15 10:00:00',
            'slot_end' => '2027-01-15 10:30:00',
        ];

        TimeSlot::create($data);

        $this->expectException(QueryException::class);
        TimeSlot::create($data);
    }

    public function test_two_different_doctors_can_share_a_start_time(): void
    {
        $a = $this->makeDoctor();
        $b = $this->makeDoctor();

        TimeSlot::create(['doctor_id' => $a->id, 'slot_start' => '2027-01-15 10:00:00', 'slot_end' => '2027-01-15 10:30:00']);
        TimeSlot::create(['doctor_id' => $b->id, 'slot_start' => '2027-01-15 10:00:00', 'slot_end' => '2027-01-15 10:30:00']);

        $this->assertDatabaseCount('time_slots', 2);
    }

    public function test_a_slot_cannot_be_booked_twice(): void
    {
        $doctor = $this->makeDoctor();
        $slot = TimeSlot::create([
            'doctor_id' => $doctor->id,
            'slot_start' => '2027-01-15 10:00:00',
            'slot_end' => '2027-01-15 10:30:00',
        ]);

        $row = fn (Patient $p) => [
            'patient_id' => $p->id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $doctor->clinic_id,
            'time_slot_id' => $slot->id,
        ];

        Appointment::create($row($this->makePatient()));

        $this->expectException(QueryException::class);
        Appointment::create($row($this->makePatient()));
    }
}
