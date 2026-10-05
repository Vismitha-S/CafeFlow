<?php

namespace App\Http\Requests;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    // Authorize whether authenticated user can create reservation
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $cafe = $this->route('cafe');
        if ($cafe instanceof Cafe) {
            // Customer can only reserve active cafes
            if ($user->isCustomer() && $cafe->status !== 'active') {
                return false;
            }
        }

        return $user->can('create', Reservation::class);
    }

    // Normalizes input aliases before validation
    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('time') && ! $this->has('start_time')) {
            $merge['start_time'] = $this->input('time');
        }

        if ($this->has('table_id') && ! $this->has('cafe_table_id')) {
            $merge['cafe_table_id'] = $this->input('table_id');
        }

        if ($this->has('guests') && ! $this->has('guest_count')) {
            $merge['guest_count'] = $this->input('guests');
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    // Get the validation rules that apply to the request
    public function rules(): array
    {
        $minGuests = (int) config('reservations.min_guests', 1);
        $maxGuests = (int) config('reservations.max_guests', 20);

        return [
            'cafe_table_id' => ['required', 'integer', 'exists:cafe_tables,id'],
            'reservation_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'guest_count' => ['required', 'integer', 'min:'.$minGuests, 'max:'.$maxGuests],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    // Custom validator to confirm table belongs to route-bound cafe
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $cafe = $this->route('cafe');
            $cafeId = $cafe instanceof Cafe ? $cafe->id : (int) $cafe;
            $tableId = $this->input('cafe_table_id');

            if ($cafeId && $tableId) {
                $table = CafeTable::find($tableId);
                if ($table && (int) $table->cafe_id !== (int) $cafeId) {
                    $validator->errors()->add('cafe_table_id', 'The selected table does not belong to this cafe.');
                }
            }
        });
    }
}
