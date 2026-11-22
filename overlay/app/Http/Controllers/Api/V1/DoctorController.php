<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Clinic $clinic): JsonResponse
    {
        $doctors = $clinic->doctors()->with('user:id,name')->get()->map(fn (Doctor $d) => [
            'id' => $d->id,
            'name' => $d->user->name,
            'specialisation' => $d->specialisation,
            'consultation_fee' => $d->consultation_fee,
        ])->values();

        return response()->json($doctors);
    }

    public function slots(Request $request, Doctor $doctor): JsonResponse
    {
        $request->validate(['date' => ['nullable', 'date']]);
        $date = $request->input('date', now()->toDateString());

        $slots = $doctor->timeSlots()
            ->whereDate('slot_start', $date)
            ->where('is_booked', false)
            ->where('slot_start', '>', now())
            ->orderBy('slot_start')
            ->get(['id', 'slot_start', 'slot_end']);

        return response()->json($slots);
    }
}
