<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo logins (password for all: "password"):
     *   admin@bookmydoc.test, dr.rao@bookmydoc.test, dr.nair@bookmydoc.test, patient@bookmydoc.test
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@bookmydoc.test'], [
            'name' => 'Admin',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);

        $indiranagar = Clinic::updateOrCreate(['phone' => '08040000001'], [
            'name' => 'BookMyDoc Indiranagar',
            'address' => '100 Feet Road, Indiranagar, Bengaluru 560038',
        ]);

        $koramangala = Clinic::updateOrCreate(['phone' => '08040000002'], [
            'name' => 'BookMyDoc Koramangala',
            'address' => '80 Feet Road, Koramangala, Bengaluru 560034',
        ]);

        $rao = User::updateOrCreate(['email' => 'dr.rao@bookmydoc.test'], [
            'name' => 'Dr. Anita Rao',
            'password' => 'password',
            'role' => User::ROLE_DOCTOR,
        ]);
        Doctor::updateOrCreate(['user_id' => $rao->id], [
            'clinic_id' => $indiranagar->id,
            'reg_no' => 'KMC-10001',
            'specialisation' => 'General Medicine',
            'consultation_fee' => 500,
        ]);

        $nair = User::updateOrCreate(['email' => 'dr.nair@bookmydoc.test'], [
            'name' => 'Dr. Suresh Nair',
            'password' => 'password',
            'role' => User::ROLE_DOCTOR,
        ]);
        Doctor::updateOrCreate(['user_id' => $nair->id], [
            'clinic_id' => $koramangala->id,
            'reg_no' => 'KMC-10002',
            'specialisation' => 'Cardiology',
            'consultation_fee' => 800,
        ]);

        $patient = User::updateOrCreate(['email' => 'patient@bookmydoc.test'], [
            'name' => 'Demo Patient',
            'password' => 'password',
            'role' => User::ROLE_PATIENT,
        ]);
        Patient::updateOrCreate(['user_id' => $patient->id], [
            'uhid' => 'UH0000000001',
            'gender' => 'M',
            'phone' => '9876543210',
        ]);
    }
}
