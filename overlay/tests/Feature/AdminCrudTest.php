<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Concerns\CreatesClinicData;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase, CreatesClinicData;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_update_and_delete_a_clinic(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.clinics.store'), ['name' => 'Whitefield Clinic', 'address' => 'ITPL Road', 'phone' => '0801234567', 'is_active' => 1])
            ->assertRedirect(route('admin.clinics.index'));
        $this->assertDatabaseHas('clinics', ['name' => 'Whitefield Clinic', 'is_active' => 1]);

        $clinic = \App\Models\Clinic::where('name', 'Whitefield Clinic')->first();

        $this->actingAs($this->admin)
            ->put(route('admin.clinics.update', $clinic), ['name' => 'Whitefield Clinic', 'address' => 'ITPL Road', 'is_active' => 0])
            ->assertRedirect(route('admin.clinics.index'));
        $this->assertFalse($clinic->fresh()->is_active);

        $this->actingAs($this->admin)->delete(route('admin.clinics.destroy', $clinic));
        $this->assertDatabaseMissing('clinics', ['id' => $clinic->id]);
    }

    public function test_clinic_validation_requires_a_name(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.clinics.store'), ['address' => 'Somewhere'])
            ->assertSessionHasErrors('name');
    }

    public function test_patient_cannot_manage_clinics(): void
    {
        $this->actingAs($this->makePatientUser())
            ->post(route('admin.clinics.store'), ['name' => 'X', 'address' => 'Y'])
            ->assertForbidden();
    }

    public function test_clinic_with_doctors_cannot_be_deleted(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($this->admin)
            ->delete(route('admin.clinics.destroy', $doctor->clinic))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('clinics', ['id' => $doctor->clinic_id]);
    }

    public function test_admin_can_create_a_doctor_with_a_login(): void
    {
        $clinic = $this->makeClinic();

        $this->actingAs($this->admin)->post(route('admin.doctors.store'), [
            'name' => 'Dr. New Doctor',
            'email' => 'new.doctor@example.com',
            'password' => 'password123',
            'clinic_id' => $clinic->id,
            'reg_no' => 'KMC-99999',
            'specialisation' => 'Dermatology',
            'consultation_fee' => 600,
        ])->assertRedirect(route('admin.doctors.index'));

        $this->assertDatabaseHas('users', ['email' => 'new.doctor@example.com', 'role' => 'doctor']);
        $this->assertDatabaseHas('doctors', ['reg_no' => 'KMC-99999', 'clinic_id' => $clinic->id]);
    }

    public function test_doctor_reg_no_must_be_unique(): void
    {
        $existing = $this->makeDoctor();

        $this->actingAs($this->admin)->post(route('admin.doctors.store'), [
            'name' => 'Dr. Copy',
            'email' => 'copy@example.com',
            'password' => 'password123',
            'clinic_id' => $existing->clinic_id,
            'reg_no' => $existing->reg_no,
            'specialisation' => 'ENT',
            'consultation_fee' => 400,
        ])->assertSessionHasErrors('reg_no');
    }

    public function test_admin_can_update_a_doctor(): void
    {
        $doctor = $this->makeDoctor();
        $other = $this->makeClinic();

        $this->actingAs($this->admin)->put(route('admin.doctors.update', $doctor), [
            'name' => 'Dr. Renamed',
            'email' => $doctor->user->email,
            'clinic_id' => $other->id,
            'reg_no' => $doctor->reg_no,
            'specialisation' => 'Cardiology',
            'consultation_fee' => 750,
        ])->assertRedirect(route('admin.doctors.index'));

        $this->assertSame('Dr. Renamed', $doctor->user->fresh()->name);
        $this->assertSame($other->id, $doctor->fresh()->clinic_id);
    }

    public function test_admin_can_generate_slots_without_duplicates(): void
    {
        $doctor = $this->makeDoctor();
        $payload = [
            'doctor_id' => $doctor->id,
            'date_from' => Carbon::tomorrow()->toDateString(),
            'date_to' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'duration' => 30,
        ];

        $this->actingAs($this->admin)->post(route('admin.slots.store'), $payload)->assertSessionHasNoErrors();
        $this->assertSame(4, TimeSlot::where('doctor_id', $doctor->id)->count());

        $this->actingAs($this->admin)->post(route('admin.slots.store'), $payload);
        $this->assertSame(4, TimeSlot::where('doctor_id', $doctor->id)->count());
    }

    public function test_slot_end_time_must_be_after_start_time(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($this->admin)->post(route('admin.slots.store'), [
            'doctor_id' => $doctor->id,
            'date_from' => Carbon::tomorrow()->toDateString(),
            'date_to' => Carbon::tomorrow()->toDateString(),
            'start_time' => '12:00',
            'end_time' => '09:00',
            'duration' => 30,
        ])->assertSessionHasErrors('end_time');
    }

    public function test_booked_slot_cannot_be_deleted_but_free_slot_can(): void
    {
        $doctor = $this->makeDoctor();
        $booked = $this->makeSlot($doctor, Carbon::tomorrow()->setTime(9, 0), true);
        $free = $this->makeSlot($doctor, Carbon::tomorrow()->setTime(10, 0));

        $this->actingAs($this->admin)->delete(route('admin.slots.destroy', $booked))->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('time_slots', ['id' => $booked->id]);

        $this->actingAs($this->admin)->delete(route('admin.slots.destroy', $free));
        $this->assertDatabaseMissing('time_slots', ['id' => $free->id]);
    }

    public function test_admin_pages_load(): void
    {
        $this->makeDoctor();

        foreach (['admin.clinics.index', 'admin.clinics.create', 'admin.doctors.index', 'admin.doctors.create', 'admin.slots.index', 'admin.slots.create'] as $name) {
            $this->actingAs($this->admin)->get(route($name))->assertOk();
        }
    }
}
