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
        User::create([
            'name' => 'Admin',
            'email' => 'admin@bookmydoc.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);

        $indiranagar = Clinic::create([
            'name' => 'BookMyDoc Indiranagar',
            'address' => '100 Feet Road, Indiranagar, Bengaluru 560038',
            'phone' => '08040000001',
        ]);

        $koramangala = Clinic::create([
            'name' => 'BookMyDoc Koramangala',
            'address' => '80 Feet Road, Koramangala, Bengaluru 560034',
            'phone' => '08040000002',
        ]);

        $rao = User::create([
            'name' => 'Dr. Anita Rao',
            'email' => 'dr.rao@bookmydoc.test',
            'password' => 'password',
            'role' => User::ROLE_DOCTOR,
        ]);
        Doctor::create([
            'user_id' => $rao->id,
            'clinic_id' => $indiranagar->id,
            'reg_no' => 'KMC-10001',
            'specialisation' => 'General Medicine',
            'consultation_fee' => 500,
        ]);

        $nair = User::create([
            'name' => 'Dr. Suresh Nair',
            'email' => 'dr.nair@bookmydoc.test',
            'password' => 'password',
            'role' => User::ROLE_DOCTOR,
        ]);
        Doctor::create([
            'user_id' => $nair->id,
            'clinic_id' => $koramangala->id,
            'reg_no' => 'KMC-10002',
            'specialisation' => 'Cardiology',
            'consultation_fee' => 800,
        ]);

        $patient = User::create([
            'name' => 'Demo Patient',
            'email' => 'patient@bookmydoc.test',
            'password' => 'password',
            'role' => User::ROLE_PATIENT,
        ]);
        Patient::create([
            'user_id' => $patient->id,
            'uhid' => 'UH0000000001',
            'gender' => 'M',
            'phone' => '9876543210',
        ]);
    }
}
