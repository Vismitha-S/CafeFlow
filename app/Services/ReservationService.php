<?php

namespace App\Services;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(
        protected ReservationAvailabilityService $availabilityService
    ) {}

    // Calculate reservation end time using start time and duration
    public function calculateEndTime(string $startTime, ?int $durationMinutes = null): string
    {
        return $this->availabilityService->calculateEndTime($startTime, $durationMinutes);
    }

    // Validate that the requested table belongs to the specified cafe
    public function validateTableBelongsToCafe(Cafe $cafe, CafeTable $table): void
    {
        if ((int) $table->cafe_id !== (int) $cafe->id) {
            throw ValidationException::withMessages([
                'cafe_table_id' => ['The selected table does not belong to this cafe.'],
            ]);
        }
    }

    // Validate that the table has sufficient capacity for the requested guests
    public function validateTableCapacity(CafeTable $table, int $guestCount): void
    {
        if ($table->capacity < $guestCount) {
            throw ValidationException::withMessages([
                'guest_count' => ['Table capacity is insufficient for the requested guest count.'],
            ]);
        }
    }

    // Validate that the cafe is currently active
    public function validateCafeIsActive(Cafe $cafe): void
    {
        $this->availabilityService->validateCafeStatus($cafe);
    }

    // Validate that the table is active and not inactive
    public function validateTableIsActive(CafeTable $table): void
    {
        if ($table->status !== 'active') {
            throw ValidationException::withMessages([
                'cafe_table_id' => ['The selected table is currently inactive.'],
            ]);
        }
    }

    // Validate that the reservation time is within the cafe's opening hours
    public function validateCafeOpeningHours(Cafe $cafe, string $date, string $startTime, string $endTime): void
    {
        $this->availabilityService->validateCafeHours($cafe, $date, $startTime, $endTime);
    }

    // Check if a specific table is available for the given date and time range
    public function isTableAvailable(CafeTable $table, string $date, string $startTime, string $endTime): bool
    {
        $startTimeFormatted = Carbon::parse($startTime)->format('H:i:s');
        $endTimeFormatted = Carbon::parse($endTime)->format('H:i:s');
        $blockingStatuses = config('reservations.blocking_statuses', ['pending', 'confirmed']);

        return ! Reservation::query()
            ->where('cafe_table_id', $table->id)
            ->whereDate('reservation_date', $date)
            ->whereIn('status', $blockingStatuses)
            ->where('start_time', '<', $endTimeFormatted)
            ->where('end_time', '>', $startTimeFormatted)
            ->exists();
    }

    // Read the current reservation fee from the cafe model
    public function getCafeReservationFee(Cafe $cafe): float
    {
        return (float) ($cafe->reservation_fee ?? 0.00);
    }

    // Read the current cancellation penalty percentage from the cafe model
    public function getCafeCancellationPenaltyPercentage(Cafe $cafe): float
    {
        return (float) ($cafe->cancellation_penalty_percentage ?? 0.00);
    }

    // Concurrency-safe reservation creation within a database transaction and row locking
    public function createReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $cafe = Cafe::findOrFail($data['cafe_id']);

            // Lock table row to prevent race conditions during table assignment
            $table = CafeTable::where('id', $data['cafe_table_id'])->lockForUpdate()->firstOrFail();

            $this->validateCafeIsActive($cafe);
            $this->validateTableIsActive($table);
            $this->validateTableBelongsToCafe($cafe, $table);
            $this->validateTableCapacity($table, (int) $data['guest_count']);

            $duration = $data['duration_minutes'] ?? (int) config('reservations.default_duration_minutes', 90);
            $startTime = Carbon::parse($data['start_time'])->format('H:i');
            $endTime = $this->calculateEndTime($startTime, $duration);

            $this->validateCafeOpeningHours($cafe, $data['reservation_date'], $startTime, $endTime);

            // Row-level lock on existing reservations for this table to prevent double-booking
            $blockingStatuses = config('reservations.blocking_statuses', ['pending', 'confirmed']);
            $hasConflict = Reservation::query()
                ->where('cafe_table_id', $table->id)
                ->whereDate('reservation_date', $data['reservation_date'])
                ->whereIn('status', $blockingStatuses)
                ->where('start_time', '<', $endTime . ':00')
                ->where('end_time', '>', $startTime . ':00')
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'table' => ['The selected table is no longer available for the requested time.'],
                ]);
            }

            // Snapshot dynamic cafe pricing at the exact moment of booking
            $reservationFee = $this->getCafeReservationFee($cafe);
            $cancellationPenalty = $this->getCafeCancellationPenaltyPercentage($cafe);

            return Reservation::create([
                'cafe_id' => $cafe->id,
                'cafe_table_id' => $table->id,
                'user_id' => $data['user_id'],
                'reservation_date' => $data['reservation_date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'guest_count' => (int) $data['guest_count'],
                'status' => $data['status'] ?? 'pending',
                'reservation_fee' => $reservationFee,
                'cancellation_penalty_percentage' => $cancellationPenalty,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
