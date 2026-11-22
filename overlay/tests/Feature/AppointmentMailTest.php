<?php

namespace Tests\Feature;

use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentConfirmed;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesClinicData;
use Tests\TestCase;

class AppointmentMailTest extends TestCase
{
    use RefreshDatabase, CreatesClinicData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_booking_sends_a_confirmation_email_to_the_patient(): void
    {
        Mail::fake();
        $slot = $this->makeSlot($this->makeDoctor());
        $patient = $this->makePatientUser(['email' => 'pat@example.com']);

        $this->actingAs($patient)->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id]);

        Mail::assertSent(AppointmentConfirmed::class, fn ($mail) => $mail->hasTo('pat@example.com'));
    }

    public function test_booking_through_the_api_also_sends_the_email(): void
    {
        Mail::fake();
        $slot = $this->makeSlot($this->makeDoctor());
        Sanctum::actingAs($this->makePatientUser(['email' => 'api@example.com']));

        $this->postJson('/api/v1/appointments', ['time_slot_id' => $slot->id])->assertCreated();

        Mail::assertSent(AppointmentConfirmed::class, fn ($mail) => $mail->hasTo('api@example.com'));
    }

    public function test_cancelling_sends_a_cancellation_email(): void
    {
        Mail::fake();
        $slot = $this->makeSlot($this->makeDoctor());
        $patient = $this->makePatientUser(['email' => 'cancel@example.com']);
        $appointment = app(BookingService::class)->book($patient->patient, $slot->id);

        $this->actingAs($patient)->patch(route('patient.appointments.cancel', $appointment));

        Mail::assertSent(AppointmentCancelled::class, fn ($mail) => $mail->hasTo('cancel@example.com'));
    }

    public function test_a_failed_booking_sends_no_email(): void
    {
        Mail::fake();
        $slot = $this->makeSlot($this->makeDoctor(), null, true); // already booked

        $this->actingAs($this->makePatientUser())->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id]);

        Mail::assertNothingSent();
    }

    public function test_confirmation_email_contains_the_details_and_the_pdf(): void
    {
        $appointment = $this->makeAppointment($this->makeDoctor(), $this->makePatientUser());

        $mail = new AppointmentConfirmed($appointment);

        $mail->assertSeeInHtml($appointment->reference);
        $mail->assertSeeInHtml($appointment->clinic->name);
        $this->assertCount(1, $mail->attachments());
    }
}
