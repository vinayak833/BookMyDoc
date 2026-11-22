<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public const STATUSES = ['Scheduled', 'Completed', 'Cancelled', 'No-Show'];

    /** Validate the request filters and build the report. */
    public function dailyFromRequest(Request $request): array
    {
        $data = $request->validate([
            'date' => ['nullable', 'date'],
            'clinic_id' => ['nullable', 'exists:clinics,id'],
            'basis' => ['nullable', 'in:scheduled,created'],
        ]);

        return $this->dailyBookings(
            Carbon::parse($data['date'] ?? now()->toDateString()),
            isset($data['clinic_id']) ? (int) $data['clinic_id'] : null,
            $data['basis'] ?? 'scheduled'
        );
    }

    /**
     * Bookings for one day, optionally for one clinic.
     * basis "scheduled": appointments happening on that day.
     * basis "created":   bookings that were made on that day.
     */
    public function dailyBookings(Carbon $date, ?int $clinicId = null, string $basis = 'scheduled'): array
    {
        $column = $basis === 'created' ? 'created_at' : 'scheduled_at';

        $appointments = Appointment::with(['clinic', 'doctor.user', 'patient.user'])
            ->whereDate($column, $date->toDateString())
            ->when($clinicId, fn ($q) => $q->where('clinic_id', $clinicId))
            ->orderBy('scheduled_at')
            ->get();

        $clinics = Clinic::when($clinicId, fn ($q) => $q->whereKey($clinicId))
            ->orderBy('name')
            ->get()
            ->map(fn (Clinic $c) => ['name' => $c->name] + $this->tally($appointments->where('clinic_id', $c->id)))
            ->values();

        $doctors = $appointments->groupBy('doctor_id')
            ->map(fn (Collection $items) => [
                'name' => $items->first()->doctor->user->name,
                'clinic' => $items->first()->clinic->name,
            ] + $this->tally($items))
            ->sortBy('name')
            ->values();

        return [
            'date' => $date->toDateString(),
            'basis' => $basis,
            'clinic_id' => $clinicId,
            'totals' => $this->tally($appointments),
            'clinics' => $clinics,
            'doctors' => $doctors,
            'appointments' => $appointments,
        ];
    }

    private function tally(Collection $items): array
    {
        $row = ['total' => $items->count()];

        foreach (self::STATUSES as $status) {
            $row[$status] = $items->where('status', $status)->count();
        }

        return $row;
    }
}
