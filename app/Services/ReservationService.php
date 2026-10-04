<?php

namespace App\Services;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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

    // Validate that the reservation date is not in the past
    public function validateReservationDateNotPast(string $date): void
    {
        $reservationDate = Carbon::parse($date)->startOfDay();
        $today = Carbon::today();

        if ($reservationDate->lt($today)) {
            throw ValidationException::withMessages([
                'reservation_date' => ['Reservation date cannot be in the past.'],
            ]);
        }
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
    public function createReservation(User|array $userOrData, ?Cafe $cafe = null, array $data = []): Reservation
    {
        if ($userOrData instanceof User) {
            $user = $userOrData;
            $cafeModel = $cafe;
            $bookingData = $data;
            $isCustomerSubmission = true;
        } else {
            $bookingData = $userOrData;
            $cafeModel = $cafe ?? Cafe::findOrFail($bookingData['cafe_id']);
            $user = isset($bookingData['user_id']) ? User::find($bookingData['user_id']) : null;
            $isCustomerSubmission = false;
        }

        return DB::transaction(function () use ($user, $cafeModel, $bookingData, $isCustomerSubmission) {
            // Confirm cafe is active
            $this->validateCafeIsActive($cafeModel);

            // Confirm reservation date is not in the past
            $this->validateReservationDateNotPast($bookingData['reservation_date']);

            // Lock table row to prevent race conditions during table assignment
            $table = CafeTable::where('id', $bookingData['cafe_table_id'])->lockForUpdate()->first();
            if (! $table || $table->trashed()) {
                throw ValidationException::withMessages([
                    'cafe_table_id' => ['The selected table does not exist.'],
                ]);
            }

            // Confirm table is active and belongs to cafe
            $this->validateTableIsActive($table);
            $this->validateTableBelongsToCafe($cafeModel, $table);
            $this->validateTableCapacity($table, (int) $bookingData['guest_count']);

            // Calculate end time
            $duration = $bookingData['duration_minutes'] ?? (int) config('reservations.default_duration_minutes', 90);
            $startTime = Carbon::parse($bookingData['start_time'])->format('H:i');
            $endTime = $this->calculateEndTime($startTime, $duration);

            // Validate cafe opening hours
            $this->validateCafeOpeningHours($cafeModel, $bookingData['reservation_date'], $startTime, $endTime);

            // Row-level lock on existing reservations for this table to prevent double-booking
            $blockingStatuses = config('reservations.blocking_statuses', ['pending', 'confirmed']);
            $hasConflict = Reservation::query()
                ->where('cafe_table_id', $table->id)
                ->whereDate('reservation_date', $bookingData['reservation_date'])
                ->whereIn('status', $blockingStatuses)
                ->where('start_time', '<', $endTime . ':00')
                ->where('end_time', '>', $startTime . ':00')
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'table' => ['This table is no longer available for the selected time.'],
                ]);
            }

            // Snapshot dynamic cafe pricing at the exact moment of booking
            $reservationFee = $this->getCafeReservationFee($cafeModel);
            $cancellationPenalty = $this->getCafeCancellationPenaltyPercentage($cafeModel);

            // Determine status: always pending for customer submissions
            $status = $isCustomerSubmission ? 'pending' : ($bookingData['status'] ?? 'pending');
            $userId = $isCustomerSubmission ? $user->id : (int) ($bookingData['user_id'] ?? $user?->id);

            return Reservation::create([
                'cafe_id' => $cafeModel->id,
                'cafe_table_id' => $table->id,
                'user_id' => $userId,
                'reservation_date' => $bookingData['reservation_date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'guest_count' => (int) $bookingData['guest_count'],
                'status' => $status,
                'reservation_fee' => $reservationFee,
                'cancellation_penalty_percentage' => $cancellationPenalty,
                'notes' => $bookingData['notes'] ?? null,
            ]);
        });
    }

    // Get reservations for the user based on role, avoiding N+1 queries
    public function getReservationsForUser(User $user): array|Collection
    {
        if ($user->isAdmin()) {
            return Reservation::with(['cafe', 'cafeTable', 'user'])
                ->orderBy('reservation_date', 'desc')
                ->orderBy('start_time', 'desc')
                ->get();
        }

        if ($user->isOwner()) {
            return Reservation::with(['cafe', 'cafeTable', 'user'])
                ->whereHas('cafe', fn ($q) => $q->where('owner_id', $user->id))
                ->orderBy('reservation_date', 'desc')
                ->orderBy('start_time', 'desc')
                ->get();
        }

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i');

        $upcoming = Reservation::with(['cafe', 'cafeTable', 'user'])
            ->where('user_id', $user->id)
            ->where(function ($query) use ($today, $nowTime) {
                $query->whereDate('reservation_date', '>', $today)
                    ->orWhere(function ($q) use ($today, $nowTime) {
                        $q->whereDate('reservation_date', $today)
                            ->where('end_time', '>=', $nowTime);
                    });
            })
            ->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $past = Reservation::with(['cafe', 'cafeTable', 'user'])
            ->where('user_id', $user->id)
            ->where(function ($query) use ($today, $nowTime) {
                $query->whereDate('reservation_date', '<', $today)
                    ->orWhere(function ($q) use ($today, $nowTime) {
                        $q->whereDate('reservation_date', $today)
                            ->where('end_time', '<', $nowTime);
                    });
            })
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $all = Reservation::with(['cafe', 'cafeTable', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return [
            'upcoming' => $upcoming,
            'past' => $past,
            'all' => $all,
        ];
    }
}
