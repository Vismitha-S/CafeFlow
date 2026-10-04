<?php

namespace App\Services;

use App\Models\Cafe;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ReservationAvailabilityService
{
    // Calculate reservation end time based on start time and duration
    public function calculateEndTime(string $startTime, ?int $durationMinutes = null): string
    {
        $duration = $durationMinutes ?? (int) config('reservations.default_duration_minutes', 90);
        $start = Carbon::parse($startTime);
        $end = (clone $start)->addMinutes($duration);

        return $end->format('H:i');
    }

    // Validate that the cafe is currently active
    public function validateCafeStatus(Cafe $cafe): void
    {
        if ($cafe->status !== 'active') {
            throw ValidationException::withMessages([
                'cafe' => ['Cafe is currently inactive.'],
            ]);
        }
    }

    // Validate reservation request against cafe operating hours
    public function validateCafeHours(Cafe $cafe, string $date, string $startTime, string $endTime): void
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = (int) $carbonDate->isoWeekday(); // 1 (Monday) to 7 (Sunday)

        $hour = $cafe->hours()->where('day_of_week', $dayOfWeek)->first();

        // Reject if no hours configured or marked as closed
        if (! $hour || $hour->is_closed || empty($hour->opens_at) || empty($hour->closes_at)) {
            throw ValidationException::withMessages([
                'date' => ['The cafe is closed on the selected date.'],
            ]);
        }

        $opensAt = Carbon::parse($hour->opens_at)->format('H:i');
        $closesAt = Carbon::parse($hour->closes_at)->format('H:i');
        $startTimeFormatted = Carbon::parse($startTime)->format('H:i');
        $endTimeFormatted = Carbon::parse($endTime)->format('H:i');

        // Reject if request is before opening time
        if ($startTimeFormatted < $opensAt) {
            throw ValidationException::withMessages([
                'time' => ['The requested start time is before the cafe opens.'],
            ]);
        }

        // Reject if start time is at/after closing time or end time extends past closing time
        if ($startTimeFormatted >= $closesAt || $endTimeFormatted > $closesAt) {
            throw ValidationException::withMessages([
                'time' => ['The requested reservation time extends past the cafe closing time.'],
            ]);
        }
    }

    // Get collection of available active tables with capacity for the requested date and time
    public function getAvailableTables(Cafe $cafe, string $date, string $startTime, int $guestCount, ?int $durationMinutes = null): Collection
    {
        $startTimeFormatted = Carbon::parse($startTime)->format('H:i');
        $endTimeFormatted = $this->calculateEndTime($startTimeFormatted, $durationMinutes);

        $this->validateCafeStatus($cafe);
        $this->validateCafeHours($cafe, $date, $startTimeFormatted, $endTimeFormatted);

        $blockingStatuses = config('reservations.blocking_statuses', ['pending', 'confirmed']);

        // Find tables that have overlapping blocking reservations
        // Interval overlap: existing.start_time < requested.end_time AND existing.end_time > requested.start_time
        $conflictingTableIds = Reservation::query()
            ->where('cafe_id', $cafe->id)
            ->whereDate('reservation_date', $date)
            ->whereIn('status', $blockingStatuses)
            ->where('start_time', '<', $endTimeFormatted . ':00')
            ->where('end_time', '>', $startTimeFormatted . ':00')
            ->pluck('cafe_table_id');

        return $cafe->tables()
            ->where('status', 'active')
            ->where('capacity', '>=', $guestCount)
            ->whereNotIn('id', $conflictingTableIds)
            ->orderBy('capacity', 'asc')
            ->orderBy('table_number', 'asc')
            ->get();
    }

    // Get formatted availability details array suitable for JSON API responses
    public function getAvailabilityDetails(Cafe $cafe, string $date, string $startTime, int $guestCount, ?int $durationMinutes = null): array
    {
        $duration = $durationMinutes ?? (int) config('reservations.default_duration_minutes', 90);
        $startTimeFormatted = Carbon::parse($startTime)->format('H:i');
        $endTimeFormatted = $this->calculateEndTime($startTimeFormatted, $duration);

        $tables = $this->getAvailableTables($cafe, $date, $startTimeFormatted, $guestCount, $duration);

        return [
            'cafe' => [
                'id' => $cafe->id,
                'name' => $cafe->name,
                'slug' => $cafe->slug,
            ],
            'requested_date' => $date,
            'requested_start_time' => $startTimeFormatted,
            'requested_end_time' => $endTimeFormatted,
            'guest_count' => $guestCount,
            'reservation_duration' => $duration,
            'available_tables' => $tables->map(fn ($table) => [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'location' => $table->location,
            ])->values()->all(),
        ];
    }
}
