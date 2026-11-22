<!DOCTYPE html>
<html lang="en">
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <p>Hello {{ $appointment->patient->user->name }},</p>
    <p>Your appointment {{ $appointment->reference }} with {{ $appointment->doctor->user->name }} at {{ $appointment->clinic->name }} on {{ $appointment->scheduled_at->format('d F Y, h:i A') }} has been cancelled.</p>
    <p>You can book a new appointment any time from your BookMyDoc account.</p>
    <p>BookMyDoc Clinic Network</p>
</body>
</html>
