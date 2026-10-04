<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenuCategoryRequest extends FormRequest
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
        $cafeId = $cafe instanceof \App\Models\Cafe ? $cafe->id : (int) $cafe;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('menu_categories')->where(function ($query) use ($cafeId) {
                    return $query->where('cafe_id', $cafeId)->whereNull('deleted_at');
                })
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
