<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Concerns\CreatesClinicData;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase, CreatesClinicData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_patient_can_book_an_available_slot(): void
    {
        $doctor = $this->makeDoctor();
        $slot = $this->makeSlot($doctor);
        $patient = $this->makePatientUser();

        $this->actingAs($patient)
            ->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id, 'chief_complaint' => 'Fever'])
            ->assertRedirect(route('patient.appointments.index'));

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->patient->id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $doctor->clinic_id,
            'time_slot_id' => $slot->id,
            'status' => 'Scheduled',
            'chief_complaint' => 'Fever',
        ]);
        $this->assertTrue($slot->fresh()->is_booked);
    }

    public function test_patient_without_a_patient_row_can_still_book(): void
    {
        $slot = $this->makeSlot($this->makeDoctor());
        $user = \App\Models\User::factory()->create(['role' => 'patient']);

        $this->actingAs($user)->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id]);

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('patients', 1);
    }

    public function test_a_taken_slot_cannot_be_booked_again(): void
    {
        $slot = $this->makeSlot($this->makeDoctor());
        $first = $this->makePatientUser();
        $second = $this->makePatientUser();

        app(BookingService::class)->book($first->patient, $slot->id);

        $this->actingAs($second)
            ->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id])
            ->assertSessionHasErrors('slot');

        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_a_past_slot_cannot_be_booked(): void
    {
        $slot = $this->makeSlot($this->makeDoctor(), Carbon::yesterday()->setTime(10, 0));
        $patient = $this->makePatientUser();

        $this->actingAs($patient)
            ->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id])
            ->assertSessionHasErrors('slot');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_cancelling_frees_the_slot_so_it_can_be_rebooked(): void
    {
        $slot = $this->makeSlot($this->makeDoctor());
        $first = $this->makePatientUser();
        $second = $this->makePatientUser();

        $appointment = app(BookingService::class)->book($first->patient, $slot->id);

        $this->actingAs($first)
            ->patch(route('patient.appointments.cancel', $appointment))
            ->assertSessionHasNoErrors();

        $this->assertSame('Cancelled', $appointment->fresh()->status);
        $this->assertFalse($slot->fresh()->is_booked);

        $this->actingAs($second)
            ->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Appointment::count());
        $this->assertTrue($slot->fresh()->is_booked);
    }

    public function test_patient_cannot_cancel_someone_elses_appointment(): void
    {
        $appointment = $this->makeAppointment($this->makeDoctor(), $this->makePatientUser());
        $stranger = $this->makePatientUser();

        $this->actingAs($stranger)
            ->patch(route('patient.appointments.cancel', $appointment))
            ->assertForbidden();

        $this->assertSame('Scheduled', $appointment->fresh()->status);
    }

    public function test_patient_pages_load(): void
    {
        $clinic = $this->makeClinic();
        $doctor = $this->makeDoctor($clinic);
        $this->makeSlot($doctor);
        $patient = $this->makePatientUser();

        $this->actingAs($patient)->get(route('patient.clinics.index'))->assertOk()->assertSee($clinic->name);
        $this->actingAs($patient)->get(route('patient.clinics.show', $clinic))->assertOk()->assertSee($doctor->user->name);
        $this->actingAs($patient)
            ->get(route('patient.slots.index', ['doctor' => $doctor, 'date' => Carbon::tomorrow()->toDateString()]))
            ->assertOk()
            ->assertSee('10:00 AM');
        $this->actingAs($patient)->get(route('patient.appointments.index'))->assertOk();
    }

    public function test_inactive_clinics_are_hidden_from_patients(): void
    {
        $clinic = $this->makeClinic(['is_active' => false]);

        $this->actingAs($this->makePatientUser())
            ->get(route('patient.clinics.show', $clinic))
            ->assertNotFound();
    }
}
