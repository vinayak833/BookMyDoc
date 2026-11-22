<?php

namespace Tests\Feature;

use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * One test that walks the whole product the way real people use it:
 * patient registers, admin sets up clinic/doctor/slots, patient books and gets
 * an email and a PDF, the doctor sees and completes the visit, the admin reports on it.
 */
class EndToEndJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    public function test_complete_booking_journey_across_all_three_roles(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // 1. A new patient registers (becomes a "patient" automatically), then logs out.
        $this->post('/register', [
            'name' => 'Journey Patient',
            'email' => 'journey@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard', absolute: false));
        $patient = User::where('email', 'journey@example.com')->firstOrFail();
        $this->assertSame('patient', $patient->role);
        $this->post('/logout');

        // 2. The admin creates a clinic, a doctor, and generates slots.
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.clinics.store'), [
            'name' => 'Journey Clinic', 'address' => 'MG Road, Bengaluru', 'phone' => '0801111111', 'is_active' => 1,
        ])->assertSessionHasNoErrors();
        $clinic = Clinic::where('name', 'Journey Clinic')->firstOrFail();

        $this->post(route('admin.doctors.store'), [
            'name' => 'Dr. Journey', 'email' => 'dr.journey@example.com', 'password' => 'password123',
            'clinic_id' => $clinic->id, 'reg_no' => 'KMC-777', 'specialisation' => 'General Medicine', 'consultation_fee' => 500,
        ])->assertSessionHasNoErrors();
        $doctor = Doctor::where('reg_no', 'KMC-777')->firstOrFail();

        $this->post(route('admin.slots.store'), [
            'doctor_id' => $doctor->id, 'date_from' => $tomorrow, 'date_to' => $tomorrow,
            'start_time' => '09:00', 'end_time' => '10:00', 'duration' => 30,
        ])->assertSessionHasNoErrors();
        $this->assertSame(2, TimeSlot::where('doctor_id', $doctor->id)->count());

        // 3. The patient finds the clinic and doctor, sees the slots and books one.
        $this->actingAs($patient)->get(route('patient.clinics.index'))->assertOk()->assertSee('Journey Clinic');
        $this->get(route('patient.clinics.show', $clinic))->assertOk()->assertSee('Dr. Journey');
        $this->get(route('patient.slots.index', ['doctor' => $doctor, 'date' => $tomorrow]))->assertOk()->assertSee('09:00 AM');

        $slot = TimeSlot::where('doctor_id', $doctor->id)->orderBy('slot_start')->firstOrFail();
        $this->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id, 'chief_complaint' => 'Headache'])
            ->assertRedirect(route('patient.appointments.index'));

        $appointment = Appointment::firstOrFail();
        $this->assertSame('Scheduled', $appointment->status);
        Mail::assertSent(AppointmentConfirmed::class, fn ($m) => $m->hasTo('journey@example.com'));

        // 4. The patient sees it in their list and downloads the PDF.
        $this->get(route('patient.appointments.index'))->assertOk()->assertSee('Dr. Journey');
        $pdf = $this->get(route('appointments.pdf', $appointment));
        $pdf->assertOk();
        $this->assertStringStartsWith('%PDF', $pdf->getContent());

        // 5. A second patient cannot take the same slot.
        $other = User::factory()->create(['role' => 'patient']);
        $this->actingAs($other)->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id])
            ->assertSessionHasErrors('slot');
        $this->assertSame(1, Appointment::count());

        // 6. The doctor sees the patient on their schedule and completes the visit.
        $this->actingAs($doctor->user)->get(route('doctor.schedule', ['date' => $tomorrow]))
            ->assertOk()->assertSee('Journey Patient')->assertSee('Headache');
        $this->patch(route('doctor.appointments.status', $appointment), ['status' => 'Completed'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Completed', $appointment->fresh()->status);

        // 7. The admin's daily report reflects it.
        $this->actingAs($admin)->get(route('admin.reports.daily', ['date' => $tomorrow, 'clinic_id' => $clinic->id]))
            ->assertOk()
            ->assertViewHas('report', fn ($r) => $r['totals']['total'] === 1 && $r['totals']['Completed'] === 1);
    }

    public function test_cancel_and_rebook_journey(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $clinic = Clinic::create(['name' => 'Rebook Clinic', 'address' => 'Bengaluru']);
        $doctorUser = User::factory()->create(['role' => 'doctor']);
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id, 'clinic_id' => $clinic->id, 'reg_no' => 'KMC-888',
            'specialisation' => 'ENT', 'consultation_fee' => 400,
        ]);
        $slot = TimeSlot::create([
            'doctor_id' => $doctor->id,
            'slot_start' => Carbon::tomorrow()->setTime(9, 0),
            'slot_end' => Carbon::tomorrow()->setTime(9, 30),
        ]);

        $first = User::factory()->create(['role' => 'patient']);
        $second = User::factory()->create(['role' => 'patient']);

        $this->actingAs($first)->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id]);
        $appointment = Appointment::firstOrFail();

        $this->patch(route('patient.appointments.cancel', $appointment))->assertSessionHasNoErrors();
        $this->get(route('appointments.pdf', $appointment))->assertNotFound();

        $this->actingAs($second)->post(route('patient.appointments.store'), ['time_slot_id' => $slot->id])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Appointment::count());
        $this->actingAs($admin)->get(route('admin.reports.daily', ['date' => Carbon::tomorrow()->toDateString()]))
            ->assertViewHas('report', fn ($r) => $r['totals']['total'] === 2 && $r['totals']['Cancelled'] === 1 && $r['totals']['Scheduled'] === 1);
    }
}
