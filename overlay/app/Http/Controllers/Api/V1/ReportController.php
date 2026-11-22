<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function daily(Request $request, ReportService $reports): JsonResponse
    {
        $report = $reports->dailyFromRequest($request);
        unset($report['appointments']);

        return response()->json($report);
    }
}
