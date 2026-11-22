<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Doctor;
use App\Http\Controllers\Patient;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// After login everyone lands here and is sent to the dashboard for their role.
Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->role.'.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---- Patient ----
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::view('/dashboard', 'patient.dashboard')->name('dashboard');
    Route::get('/clinics', [Patient\ClinicController::class, 'index'])->name('clinics.index');
    Route::get('/clinics/{clinic}', [Patient\ClinicController::class, 'show'])->name('clinics.show');
    Route::get('/doctors/{doctor}/slots', [Patient\SlotController::class, 'index'])->name('slots.index');
    Route::get('/appointments', [Patient\AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [Patient\AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/cancel', [Patient\AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

// ---- Doctor ----
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::view('/dashboard', 'doctor.dashboard')->name('dashboard');
    Route::get('/schedule', [Doctor\ScheduleController::class, 'index'])->name('schedule');
    Route::patch('/appointments/{appointment}/status', [Doctor\ScheduleController::class, 'updateStatus'])->name('appointments.status');
});

// ---- Admin ----
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::resource('clinics', Admin\ClinicController::class)->except('show');
    Route::resource('doctors', Admin\DoctorController::class)->except('show');
    Route::resource('slots', Admin\SlotController::class)->only(['index', 'create', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
