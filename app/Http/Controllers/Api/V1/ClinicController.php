<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\JsonResponse;

class ClinicController extends Controller
{
    public function index(): JsonResponse
    {
        $clinics = Clinic::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'phone']);

        return response()->json($clinics);
    }
}
