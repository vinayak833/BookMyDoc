<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Concerns\CreatesClinicData;
use Tests\TestCase;

class DoctorScheduleTest extends TestCase
{
    use RefreshDatabase, CreatesClinicData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_doctor_sees_only_their_own_appointments_for_the_date(): void
    {
        $doctorA = $this->makeDoctor();
        $doctorB = $this->makeDoctor();
        $this->makeAppointment($doctorA, $this->makePatientUser(['name' => 'Alice Patient']));
        $this->makeAppointment($doctorB, $this->makePatientUser(['name' => 'Bob Patient']));

        $this->actingAs($doctorA->user)
            ->get(route('doctor.schedule', ['date' => Carbon::tomorrow()->toDateString()]))
            ->assertOk()
            ->assertSee('Alice Patient')
            ->assertDontSee('Bob Patient');
    }

    public function test_cancelled_appointments_are_not_listed(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = $this->makeAppointment($doctor, $this->makePatientUser(['name' => 'Carol Patient']));
        $appointment->update(['status' => 'Cancelled']);

        $this->actingAs($doctor->user)
            ->get(route('doctor.schedule', ['date' => Carbon::tomorrow()->toDateString()]))
            ->assertDontSee('Carol Patient');
    }

    public function test_doctor_can_mark_an_appointment_completed_or_no_show(): void
    {
        $doctor = $this->makeDoctor();
        $done = $this->makeAppointment($doctor, $this->makePatientUser(), Carbon::tomorrow()->setTime(10, 0));
        $missed = $this->makeAppointment($doctor, $this->makePatientUser(), Carbon::tomorrow()->setTime(11, 0));

        $this->actingAs($doctor->user)->patch(route('doctor.appointments.status', $done), ['status' => 'Completed']);
        $this->actingAs($doctor->user)->patch(route('doctor.appointments.status', $missed), ['status' => 'No-Show']);

        $this->assertSame('Completed', $done->fresh()->status);
        $this->assertSame('No-Show', $missed->fresh()->status);
    }

    public function test_doctor_cannot_update_another_doctors_appointment(): void
    {
        $doctorA = $this->makeDoctor();
        $doctorB = $this->makeDoctor();
        $appointment = $this->makeAppointment($doctorB, $this->makePatientUser());

        $this->actingAs($doctorA->user)
            ->patch(route('doctor.appointments.status', $appointment), ['status' => 'Completed'])
            ->assertForbidden();

        $this->assertSame('Scheduled', $appointment->fresh()->status);
    }

    public function test_status_must_be_a_valid_value(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = $this->makeAppointment($doctor, $this->makePatientUser());

        $this->actingAs($doctor->user)
            ->patch(route('doctor.appointments.status', $appointment), ['status' => 'Cancelled'])
            ->assertSessionHasErrors('status');
    }
}
