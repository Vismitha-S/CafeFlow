<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CheckAvailabilityRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize(): bool
    {
        $cafe = $this->route('cafe');
        if ($cafe instanceof \App\Models\Cafe) {
            $user = $this->user();
            if ($user) {
                return $user->can('view', $cafe);
            }
            return $cafe->status === 'active';
        }

        return true;
    }

    // Normalizes input before validation
    protected function prepareForValidation(): void
    {
        if ($this->has('time') && ! $this->has('start_time')) {
            $this->merge([
                'start_time' => $this->input('time'),
            ]);
        }
    }

    // Get the validation rules that apply to the request
    public function rules(): array
    {
        $minGuests = (int) config('reservations.min_guests', 1);
        $maxGuests = (int) config('reservations.max_guests', 20);

        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['nullable', 'date_format:H:i'],
            'start_time' => ['required', 'date_format:H:i'],
            'guests' => ['required', 'integer', 'min:' . $minGuests, 'max:' . $maxGuests],
        ];
    }

    // Custom validation logic for reservation time and calculated end time
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('date') && $this->filled('start_time')) {
                try {
                    $start = Carbon::createFromFormat('Y-m-d H:i', $this->input('date') . ' ' . $this->input('start_time'));
                    $duration = (int) config('reservations.default_duration_minutes', 90);
                    $end = (clone $start)->addMinutes($duration);

                    if ($end->lte($start)) {
                        $validator->errors()->add('start_time', 'Calculated end time must be after start time.');
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add('date', 'Invalid reservation date or time.');
                }
            }
        });
    }
}
