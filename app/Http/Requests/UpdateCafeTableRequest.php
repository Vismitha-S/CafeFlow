<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCafeTableRequest extends FormRequest
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
        $cafeTable = $this->route('cafeTable');
        $cafeId = $cafeTable->cafe_id;

        return [
            'table_number' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('cafe_tables')->where(function ($query) use ($cafeId) {
                    return $query->where('cafe_id', $cafeId);
                })->ignore($cafeTable->id)
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['required', 'in:indoor,outdoor'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
