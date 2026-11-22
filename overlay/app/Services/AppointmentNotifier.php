<?php

namespace App\Services;

use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentNotifier
{
    public function confirmed(Appointment $appointment): void
    {
        $this->send($appointment, new AppointmentConfirmed($appointment));
    }

    public function cancelled(Appointment $appointment): void
    {
        $this->send($appointment, new AppointmentCancelled($appointment));
    }

    /**
     * A mail failure (bad SMTP settings, network) must never undo or block a booking,
     * so errors are logged instead of thrown.
     */
    private function send(Appointment $appointment, Mailable $mailable): void
    {
        $email = $appointment->patient?->user?->email;

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::warning('Appointment email failed: '.$e->getMessage(), ['appointment' => $appointment->id]);
        }
    }
}
