<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\View\View;

class ClinicController extends Controller
{
    public function index(): View
    {
        return view('patient.clinics.index', [
            'clinics' => Clinic::where('is_active', true)->withCount('doctors')->orderBy('name')->get(),
        ]);
    }

    public function show(Clinic $clinic): View
    {
        abort_unless($clinic->is_active, 404);

        return view('patient.clinics.show', [
            'clinic' => $clinic,
            'doctors' => $clinic->doctors()->with('user')->get()->sortBy('user.name'),
        ]);
    }
}
