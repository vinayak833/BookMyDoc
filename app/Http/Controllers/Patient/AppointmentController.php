<?php

namespace App\Http\Controllers\Patient;

use App\Exceptions\BookingException;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private BookingService $bookings)
    {
    }

    public function index(Request $request): View
    {
        $patient = $request->user()->ensurePatient();

        return view('patient.appointments.index', [
            'appointments' => Appointment::with(['doctor.user', 'clinic'])
                ->where('patient_id', $patient->id)
                ->orderByDesc('scheduled_at')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'time_slot_id' => ['required', 'integer'],
            'chief_complaint' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->bookings->book(
                $request->user()->ensurePatient(),
                (int) $data['time_slot_id'],
                $data['chief_complaint'] ?? null
            );
        } catch (BookingException $e) {
            return back()->withInput()->withErrors(['slot' => $e->getMessage()]);
        }

        return redirect()->route('patient.appointments.index')->with('status', 'Appointment booked.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->patient_id === $request->user()->ensurePatient()->id, 403);

        try {
            $this->bookings->cancel($appointment);
        } catch (BookingException $e) {
            return back()->withErrors(['cancel' => $e->getMessage()]);
        }

        return back()->with('status', 'Appointment cancelled.');
    }
}
