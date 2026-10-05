<?php

namespace App\Http\Requests;

use App\Models\Cafe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $cafeId = $cafe instanceof Cafe ? $cafe->id : (int) $cafe;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('menu_categories')->where(function ($query) use ($cafeId) {
                    return $query->where('cafe_id', $cafeId)->whereNull('deleted_at');
                }),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
