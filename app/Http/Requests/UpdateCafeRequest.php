<?php

namespace App\Http\Requests;

use App\Models\Cafe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $cafe = $this->route('cafe');

        $cafeId = null;
        if ($cafe instanceof Cafe) {
            $cafeId = $cafe->id;
        } elseif (is_numeric($cafe)) {
            $cafeId = (int) $cafe;
        } elseif ($this->user()?->cafe) {
            $cafeId = $this->user()->cafe->id;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique('cafes', 'slug')->ignore($cafeId),
            ],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'image_path' => ['nullable', 'string', 'max:2000'],
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
