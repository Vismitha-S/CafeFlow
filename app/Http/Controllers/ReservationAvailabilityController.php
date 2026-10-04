<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckAvailabilityRequest;
use App\Models\Cafe;
use App\Services\ReservationAvailabilityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class ReservationAvailabilityController extends Controller
{
    use AuthorizesRequests;

    // Check table availability for a specific cafe
    public function index(CheckAvailabilityRequest $request, Cafe $cafe, ReservationAvailabilityService $availabilityService): JsonResponse
    {
        $this->authorize('view', $cafe);

        $validated = $request->validated();
        $startTime = $validated['start_time'] ?? $validated['time'];
        $date = $validated['date'];
        $guestCount = (int) $validated['guests'];

        $details = $availabilityService->getAvailabilityDetails($cafe, $date, $startTime, $guestCount);

        return response()->json($details);
    }
}
