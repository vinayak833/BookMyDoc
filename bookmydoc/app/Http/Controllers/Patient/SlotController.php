<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlotController extends Controller
{
    public function index(Request $request, Doctor $doctor): View
    {
        $request->validate(['date' => ['nullable', 'date']]);
        $date = $request->input('date', now()->toDateString());

        $slots = $doctor->timeSlots()
            ->whereDate('slot_start', $date)
            ->where('is_booked', false)
            ->where('slot_start', '>', now())
            ->orderBy('slot_start')
            ->get();

        return view('patient.slots.index', [
            'doctor' => $doctor->load('user', 'clinic'),
            'slots' => $slots,
            'date' => $date,
        ]);
    }
}
