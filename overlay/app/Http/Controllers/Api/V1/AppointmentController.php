<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BookingException;
use App\Http\Controllers\Controller;
use App\Services\AppointmentNotifier;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request, BookingService $bookings, AppointmentNotifier $notifier): JsonResponse
    {
        $data = $request->validate([
            'time_slot_id' => ['required', 'integer'],
            'chief_complaint' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $appointment = $bookings->book(
                $request->user()->ensurePatient(),
                (int) $data['time_slot_id'],
                $data['chief_complaint'] ?? null
            );
        } catch (BookingException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        $notifier->confirmed($appointment);

        return response()->json($appointment, 201);
    }
}
