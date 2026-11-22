<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\TimeSlot;
use App\Services\SlotGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class SlotController extends Controller
{
    public function index(Request $request): View
    {
        $query = TimeSlot::with(['doctor.user', 'doctor.clinic'])->orderBy('slot_start');

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->integer('doctor_id'));
        }
        if ($request->filled('date')) {
            $query->whereDate('slot_start', $request->input('date'));
        }

        return view('admin.slots.index', [
            'slots' => $query->paginate(25)->withQueryString(),
            'doctors' => Doctor::with('user', 'clinic')->get()->sortBy('user.name'),
        ]);
    }

    public function create(): View
    {
        return view('admin.slots.create', [
            'doctors' => Doctor::with('user', 'clinic')->get()->sortBy('user.name'),
        ]);
    }

    public function store(Request $request, SlotGenerator $generator): RedirectResponse
    {
        $data = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date_from' => ['required', 'date', 'after_or_equal:today'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'duration' => ['required', 'in:10,15,20,30,45,60'],
        ]);

        $from = Carbon::parse($data['date_from'])->startOfDay();
        $to = Carbon::parse($data['date_to'])->startOfDay();

        if ($from->diffInDays($to) > 31) {
            return back()->withInput()->withErrors(['date_to' => 'Please generate at most 31 days at a time.']);
        }

        $count = $generator->generate(
            Doctor::findOrFail($data['doctor_id']),
            $from,
            $to,
            $data['start_time'],
            $data['end_time'],
            (int) $data['duration']
        );

        return redirect()->route('admin.slots.index', ['doctor_id' => $data['doctor_id']])
            ->with('status', "$count new slot(s) created. Existing slots were left unchanged.");
    }

    public function destroy(TimeSlot $slot): RedirectResponse
    {
        if ($slot->is_booked) {
            return back()->withErrors(['delete' => 'A booked slot cannot be deleted. Cancel the appointment first.']);
        }

        $slot->delete();

        return back()->with('status', 'Slot deleted.');
    }
}
