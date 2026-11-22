<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403, 'No doctor profile is linked to this account.');

        $request->validate(['date' => ['nullable', 'date']]);
        $date = $request->input('date', now()->toDateString());

        $appointments = $doctor->appointments()
            ->with('patient.user')
            ->where('status', '!=', 'Cancelled')
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();

        return view('doctor.schedule.index', compact('appointments', 'date', 'doctor'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $data = $request->validate(['status' => ['required', 'in:Completed,No-Show']]);

        if ($appointment->status !== 'Scheduled') {
            return back()->withErrors(['status' => 'Only scheduled appointments can be updated.']);
        }

        $appointment->update(['status' => $data['status']]);

        return back()->with('status', 'Appointment marked '.$data['status'].'.');
    }
}
