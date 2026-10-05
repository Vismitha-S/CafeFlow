<?php

namespace App\Http\Requests;

use App\Models\Cafe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCafeTableRequest extends FormRequest
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
        $cafeId = $cafe instanceof Cafe ? $cafe->id : (int) $cafe;

        return [
            'table_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cafe_tables')->where(function ($query) use ($cafeId) {
                    return $query->where('cafe_id', $cafeId);
                }),
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['required', 'in:indoor,outdoor'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
