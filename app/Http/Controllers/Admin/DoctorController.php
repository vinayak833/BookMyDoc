<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        return view('admin.doctors.index', [
            'doctors' => Doctor::with(['user', 'clinic'])->get()->sortBy('user.name'),
        ]);
    }

    public function create(): View
    {
        return view('admin.doctors.create', ['clinics' => Clinic::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'clinic_id' => ['required', 'exists:clinics,id'],
            'reg_no' => ['required', 'string', 'max:20', 'unique:doctors,reg_no'],
            'specialisation' => ['required', 'string', 'max:100'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => User::ROLE_DOCTOR,
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'clinic_id' => $data['clinic_id'],
                'reg_no' => $data['reg_no'],
                'specialisation' => $data['specialisation'],
                'consultation_fee' => $data['consultation_fee'],
            ]);
        });

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor created.');
    }

    public function edit(Doctor $doctor): View
    {
        return view('admin.doctors.edit', [
            'doctor' => $doctor->load('user'),
            'clinics' => Clinic::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($doctor->user_id)],
            'password' => ['nullable', 'string', 'min:8'],
            'clinic_id' => ['required', 'exists:clinics,id'],
            'reg_no' => ['required', 'string', 'max:20', Rule::unique('doctors', 'reg_no')->ignore($doctor->id)],
            'specialisation' => ['required', 'string', 'max:100'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $doctor) {
            $userData = ['name' => $data['name'], 'email' => $data['email']];
            if (! empty($data['password'])) {
                $userData['password'] = $data['password'];
            }
            $doctor->user->update($userData);

            $doctor->update([
                'clinic_id' => $data['clinic_id'],
                'reg_no' => $data['reg_no'],
                'specialisation' => $data['specialisation'],
                'consultation_fee' => $data['consultation_fee'],
            ]);
        });

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor updated.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->appointments()->exists()) {
            return back()->withErrors(['delete' => 'This doctor has appointments and cannot be deleted.']);
        }

        // Deleting the user cascades to the doctor row and the doctor's slots.
        $doctor->user->delete();

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor deleted.');
    }
}
