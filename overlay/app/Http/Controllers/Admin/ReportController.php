<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function daily(Request $request, ReportService $reports): View
    {
        return view('admin.reports.daily', [
            'report' => $reports->dailyFromRequest($request),
            'clinics' => Clinic::orderBy('name')->get(),
        ]);
    }

    public function export(Request $request, ReportService $reports): StreamedResponse
    {
        $report = $reports->dailyFromRequest($request);

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Time', 'Clinic', 'Doctor', 'Patient', 'UHID', 'Status', 'Reason']);

            foreach ($report['appointments'] as $a) {
                fputcsv($out, [
                    $a->scheduled_at->toDateString(),
                    $a->scheduled_at->format('H:i'),
                    $a->clinic->name,
                    $a->doctor->user->name,
                    $a->patient->user->name,
                    $a->patient->uhid,
                    $a->status,
                    $a->chief_complaint,
                ]);
            }

            fclose($out);
        }, 'daily-bookings-'.$report['date'].'.csv', ['Content-Type' => 'text/csv']);
    }
}
