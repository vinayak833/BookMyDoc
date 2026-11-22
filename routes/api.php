<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClinicController;
use App\Http\Controllers\Api\V1\DoctorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/clinics', [ClinicController::class, 'index']);
    Route::get('/clinics/{clinic}/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{doctor}/slots', [DoctorController::class, 'slots']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::post('/appointments', [AppointmentController::class, 'store'])->middleware('role:patient');
    });
});
