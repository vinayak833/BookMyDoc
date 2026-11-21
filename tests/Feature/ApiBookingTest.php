<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesClinicData;
use Tests\TestCase;

class ApiBookingTest extends TestCase
{
    use RefreshDatabase, CreatesClinicData;

    public function test_clinic_doctors_endpoint_lists_the_clinics_doctors(): void
    {
        $clinic = $this->makeClinic();
        $doctor = $this->makeDoctor($clinic);
        $this->makeDoctor(); // a doctor at another clinic

        $this->getJson("/api/v1/clinics/{$clinic->id}/doctors")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $doctor->id);
    }

    public function test_slots_endpoint_returns_only_free_future_slots(): void
    {
        $doctor = $this->makeDoctor();
        $this->makeSlot($doctor, Carbon::tomorrow()->setTime(9, 0));
        $this->makeSlot($doctor, Carbon::tomorrow()->setTime(10, 0), true);

        $this->getJson("/api/v1/doctors/{$doctor->id}/slots?date=".Carbon::tomorrow()->toDateString())
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_patient_can_book_through_the_api(): void
    {
        $slot = $this->makeSlot($this->makeDoctor());
        Sanctum::actingAs($this->makePatientUser());

        $this->postJson('/api/v1/appointments', ['time_slot_id' => $slot->id, 'chief_complaint' => 'Cough'])
            ->assertCreated()
            ->assertJsonPath('status', 'Scheduled');

        $this->assertTrue($slot->fresh()->is_booked);
    }

    public function test_second_booking_of_the_same_slot_returns_409(): void
    {
        $slot = $this->makeSlot($this->makeDoctor());

        Sanctum::actingAs($this->makePatientUser());
        $this->postJson('/api/v1/appointments', ['time_slot_id' => $slot->id])->assertCreated();

        Sanctum::actingAs($this->makePatientUser());
        $this->postJson('/api/v1/appointments', ['time_slot_id' => $slot->id])->assertStatus(409);

        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_only_patients_can_book_and_login_is_required(): void
    {
        $slot = $this->makeSlot($this->makeDoctor());

        $this->postJson('/api/v1/appointments', ['time_slot_id' => $slot->id])->assertUnauthorized();

        Sanctum::actingAs($this->makeDoctor()->user);
        $this->postJson('/api/v1/appointments', ['time_slot_id' => $slot->id])->assertForbidden();
    }

    public function test_booking_requires_a_slot_id(): void
    {
        Sanctum::actingAs($this->makePatientUser());

        $this->postJson('/api/v1/appointments', [])->assertStatus(422);
    }
}
