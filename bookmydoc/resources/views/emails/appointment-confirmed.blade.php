<!DOCTYPE html>
<html lang="en">
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <p>Hello {{ $appointment->patient->user->name }},</p>
    <p>Your appointment is confirmed. Your confirmation PDF is attached.</p>
    <table cellpadding="6" style="border-collapse: collapse;">
        <tr><td><strong>Reference</strong></td><td>{{ $appointment->reference }}</td></tr>
        <tr><td><strong>Doctor</strong></td><td>{{ $appointment->doctor->user->name }} ({{ $appointment->doctor->specialisation }})</td></tr>
        <tr><td><strong>Clinic</strong></td><td>{{ $appointment->clinic->name }}, {{ $appointment->clinic->address }}</td></tr>
        <tr><td><strong>When</strong></td><td>{{ $appointment->scheduled_at->format('l, d F Y \a\t h:i A') }}</td></tr>
    </table>
    <p>Please arrive 10 minutes early. You can cancel from your BookMyDoc account.</p>
    <p>BookMyDoc Clinic Network</p>
</body>
</html>
