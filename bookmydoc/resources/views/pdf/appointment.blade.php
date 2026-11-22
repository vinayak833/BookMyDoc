<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Appointment {{ $appointment->reference }}</title>
    <style>
        @page { margin: 36px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 20px; margin: 0; color: #1d4ed8; }
        .sub { color: #6b7280; margin-top: 2px; }
        .banner { border-bottom: 3px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 18px; }
        table.details { width: 100%; border-collapse: collapse; }
        table.details td { padding: 8px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        table.details td.label { width: 32%; color: #6b7280; }
        .ref { font-size: 16px; font-weight: bold; }
        .status { display: inline-block; padding: 2px 8px; border: 1px solid #1d4ed8; color: #1d4ed8; }
        .note { margin-top: 22px; padding: 10px; background: #f3f4f6; }
        .footer { margin-top: 30px; color: #9ca3af; font-size: 10px; }
    </style>
</head>
<body>
    <div class="banner">
        <h1>BookMyDoc Clinic Network</h1>
        <div class="sub">Appointment confirmation</div>
    </div>

    <table class="details">
        <tr><td class="label">Reference</td><td class="ref">{{ $appointment->reference }}</td></tr>
        <tr><td class="label">Status</td><td><span class="status">{{ $appointment->status }}</span></td></tr>
        <tr><td class="label">Date</td><td>{{ $appointment->scheduled_at->format('l, d F Y') }}</td></tr>
        <tr><td class="label">Time</td><td>{{ $appointment->scheduled_at->format('h:i A') }}</td></tr>
        <tr><td class="label">Doctor</td><td>{{ $appointment->doctor->user->name }} ({{ $appointment->doctor->specialisation }})</td></tr>
        <tr><td class="label">Clinic</td><td>{{ $appointment->clinic->name }}<br>{{ $appointment->clinic->address }}@if ($appointment->clinic->phone)<br>Phone: {{ $appointment->clinic->phone }}@endif</td></tr>
        <tr><td class="label">Patient</td><td>{{ $appointment->patient->user->name }} (UHID {{ $appointment->patient->uhid }})</td></tr>
        <tr><td class="label">Reason for visit</td><td>{{ $appointment->chief_complaint ?: 'Not specified' }}</td></tr>
        <tr><td class="label">Consultation fee</td><td>INR {{ number_format($appointment->doctor->consultation_fee, 2) }} (payable at the clinic)</td></tr>
    </table>

    <div class="note">
        Please arrive 10 minutes early and bring this confirmation. To cancel or reschedule, log in to your BookMyDoc account.
    </div>

    <div class="footer">Generated on {{ now()->format('d M Y, h:i A') }}</div>
</body>
</html>
