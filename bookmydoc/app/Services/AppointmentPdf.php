<?php

namespace App\Services;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentPdf
{
    /** Render the appointment confirmation as PDF bytes. Nothing is stored on disk. */
    public function render(Appointment $appointment): string
    {
        $appointment->loadMissing(['clinic', 'doctor.user', 'patient.user']);

        return Pdf::loadView('pdf.appointment', ['appointment' => $appointment])
            ->setPaper('a4')
            ->output();
    }

    public function filename(Appointment $appointment): string
    {
        return 'appointment-'.$appointment->reference.'.pdf';
    }
}
