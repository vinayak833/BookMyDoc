<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Services\AppointmentPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
        $appointment->loadMissing(['clinic', 'doctor.user', 'patient.user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Appointment confirmed: '.$this->appointment->reference);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.appointment-confirmed');
    }

    public function attachments(): array
    {
        $pdf = app(AppointmentPdf::class);

        return [
            Attachment::fromData(fn () => $pdf->render($this->appointment), $pdf->filename($this->appointment))
                ->withMime('application/pdf'),
        ];
    }
}
