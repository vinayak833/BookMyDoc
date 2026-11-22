<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicController extends Controller
{
    public function index(): View
    {
        return view('admin.clinics.index', [
            'clinics' => Clinic::withCount('doctors')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.clinics.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Clinic::create($this->validated($request));

        return redirect()->route('admin.clinics.index')->with('status', 'Clinic created.');
    }

    public function edit(Clinic $clinic): View
    {
        return view('admin.clinics.edit', ['clinic' => $clinic]);
    }

    public function update(Request $request, Clinic $clinic): RedirectResponse
    {
        $clinic->update($this->validated($request));

        return redirect()->route('admin.clinics.index')->with('status', 'Clinic updated.');
    }

    public function destroy(Clinic $clinic): RedirectResponse
    {
        if ($clinic->doctors()->exists()) {
            return back()->withErrors(['delete' => 'This clinic still has doctors. Move or remove them first, or mark the clinic inactive.']);
        }

        $clinic->delete();

        return redirect()->route('admin.clinics.index')->with('status', 'Clinic deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:15'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
