<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentPdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppointmentPdfController extends Controller
{
    /** Used by both the web route and the API route. */
    public function __invoke(Request $request, Appointment $appointment, AppointmentPdf $pdf): Response
    {
        $user = $request->user();

        $allowed = $user->isAdmin()
            || ($user->isPatient() && $appointment->patient_id === $user->patient?->id)
            || ($user->isDoctor() && $appointment->doctor_id === $user->doctor?->id);

        abort_unless($allowed, 403);
        abort_if($appointment->status === 'Cancelled', 404, 'This appointment was cancelled.');

        return response($pdf->render($appointment), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdf->filename($appointment).'"',
        ]);
    }
}
