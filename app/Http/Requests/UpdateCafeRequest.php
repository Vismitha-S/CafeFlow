<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCafeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cafeId = $this->route('cafe') ? $this->route('cafe')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:cafes,slug,' . $cafeId],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'reservation_fee' => ['required', 'numeric', 'min:0'],
            'cancellation_penalty_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
            'owner_id' => ['sometimes', 'exists:users,id'], // Handled safely by controller/service

            'hours' => ['sometimes', 'array'],
            'hours.*.day_of_week' => ['required_with:hours', 'integer', 'between:1,7'],
            'hours.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.closes_at' => ['nullable', 'date_format:H:i', 'after:hours.*.opens_at'],
            'hours.*.is_closed' => ['boolean'],
        ];
    }
}
