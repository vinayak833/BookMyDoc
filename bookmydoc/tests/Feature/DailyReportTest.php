<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesClinicData;
use Tests\TestCase;

class DailyReportTest extends TestCase
{
    use RefreshDatabase, CreatesClinicData;

    private User $admin;
    private string $tomorrow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->tomorrow = Carbon::tomorrow()->toDateString();
    }

    public function test_report_counts_bookings_by_status(): void
    {
        $doctor = $this->makeDoctor();
        $scheduled = $this->makeAppointment($doctor, $this->makePatientUser(), Carbon::tomorrow()->setTime(9, 0));
        $completed = $this->makeAppointment($doctor, $this->makePatientUser(), Carbon::tomorrow()->setTime(10, 0));
        $cancelled = $this->makeAppointment($doctor, $this->makePatientUser(), Carbon::tomorrow()->setTime(11, 0));
        $completed->update(['status' => 'Completed']);
        $cancelled->update(['status' => 'Cancelled']);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.daily', ['date' => $this->tomorrow]))
            ->assertOk()
            ->assertViewHas('report', fn ($r) => $r['totals']['total'] === 3
                && $r['totals']['Scheduled'] === 1
                && $r['totals']['Completed'] === 1
                && $r['totals']['Cancelled'] === 1
                && $r['totals']['No-Show'] === 0);
    }

    public function test_report_groups_by_clinic_and_doctor_and_can_filter_by_clinic(): void
    {
        $doctorA = $this->makeDoctor();
        $doctorB = $this->makeDoctor();
        $this->makeAppointment($doctorA, $this->makePatientUser());
        $this->makeAppointment($doctorB, $this->makePatientUser());

        $all = $this->actingAs($this->admin)->get(route('admin.reports.daily', ['date' => $this->tomorrow]));
        $all->assertViewHas('report', fn ($r) => $r['totals']['total'] === 2 && count($r['clinics']) === 2 && count($r['doctors']) === 2);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.daily', ['date' => $this->tomorrow, 'clinic_id' => $doctorA->clinic_id]))
            ->assertViewHas('report', fn ($r) => $r['totals']['total'] === 1 && count($r['clinics']) === 1);
    }

    public function test_report_can_count_by_the_day_the_booking_was_made(): void
    {
        $this->makeAppointment($this->makeDoctor(), $this->makePatientUser()); // made today, happens tomorrow

        $this->actingAs($this->admin)
            ->get(route('admin.reports.daily', ['date' => now()->toDateString(), 'basis' => 'created']))
            ->assertViewHas('report', fn ($r) => $r['totals']['total'] === 1);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.daily', ['date' => now()->toDateString(), 'basis' => 'scheduled']))
            ->assertViewHas('report', fn ($r) => $r['totals']['total'] === 0);
    }

    public function test_empty_day_shows_zeroes(): void
    {
        $this->makeClinic();

        $this->actingAs($this->admin)
            ->get(route('admin.reports.daily', ['date' => $this->tomorrow]))
            ->assertOk()
            ->assertViewHas('report', fn ($r) => $r['totals']['total'] === 0);
    }

    public function test_only_admins_can_see_the_report(): void
    {
        $this->actingAs($this->makePatientUser())->get(route('admin.reports.daily'))->assertForbidden();
        $this->actingAs($this->makeDoctor()->user)->get(route('admin.reports.daily'))->assertForbidden();
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports.daily', ['basis' => 'nonsense']))
            ->assertSessionHasErrors('basis');
    }

    public function test_csv_export_lists_the_appointments(): void
    {
        $this->makeAppointment($this->makeDoctor(), $this->makePatientUser(['name' => 'Alice Patient']));

        $response = $this->actingAs($this->admin)->get(route('admin.reports.daily.export', ['date' => $this->tomorrow]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Date,Time,Clinic,Doctor,Patient,UHID,Status,Reason', $csv);
        $this->assertStringContainsString('Alice Patient', $csv);
    }

    public function test_report_is_available_as_json_for_admin_tokens(): void
    {
        $this->makeAppointment($this->makeDoctor(), $this->makePatientUser());

        Sanctum::actingAs($this->admin);
        $this->getJson('/api/v1/admin/reports/daily-bookings?date='.$this->tomorrow)
            ->assertOk()
            ->assertJsonPath('totals.total', 1)
            ->assertJsonStructure(['date', 'basis', 'totals', 'clinics', 'doctors']);

        Sanctum::actingAs($this->makePatientUser());
        $this->getJson('/api/v1/admin/reports/daily-bookings')->assertForbidden();
    }
}
