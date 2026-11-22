<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesClinicData;
use Tests\TestCase;

class AppointmentPdfTest extends TestCase
{
    use RefreshDatabase, CreatesClinicData;

    private function assertIsPdf($response): void
    {
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_patient_can_download_their_appointment_pdf(): void
    {
        $patient = $this->makePatientUser();
        $appointment = $this->makeAppointment($this->makeDoctor(), $patient);

        $response = $this->actingAs($patient)->get(route('appointments.pdf', $appointment));

        $this->assertIsPdf($response);
        $this->assertStringContainsString($appointment->reference.'.pdf', $response->headers->get('content-disposition'));
    }

    public function test_doctor_and_admin_can_download_it_too(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = $this->makeAppointment($doctor, $this->makePatientUser());

        $this->assertIsPdf($this->actingAs($doctor->user)->get(route('appointments.pdf', $appointment)));
        $this->assertIsPdf($this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('appointments.pdf', $appointment)));
    }

    public function test_other_patients_and_other_doctors_are_forbidden(): void
    {
        $appointment = $this->makeAppointment($this->makeDoctor(), $this->makePatientUser());

        $this->actingAs($this->makePatientUser())->get(route('appointments.pdf', $appointment))->assertForbidden();
        $this->actingAs($this->makeDoctor()->user)->get(route('appointments.pdf', $appointment))->assertForbidden();
    }

    public function test_guests_are_sent_to_login(): void
    {
        $appointment = $this->makeAppointment($this->makeDoctor(), $this->makePatientUser());

        $this->get(route('appointments.pdf', $appointment))->assertRedirect('/login');
    }

    public function test_cancelled_appointments_have_no_pdf(): void
    {
        $patient = $this->makePatientUser();
        $appointment = $this->makeAppointment($this->makeDoctor(), $patient);
        $appointment->update(['status' => 'Cancelled']);

        $this->actingAs($patient)->get(route('appointments.pdf', $appointment))->assertNotFound();
    }

    public function test_pdf_is_available_through_the_api(): void
    {
        $patient = $this->makePatientUser();
        $appointment = $this->makeAppointment($this->makeDoctor(), $patient);

        Sanctum::actingAs($patient);

        $this->assertIsPdf($this->get("/api/v1/appointments/{$appointment->id}/pdf"));
    }
}
