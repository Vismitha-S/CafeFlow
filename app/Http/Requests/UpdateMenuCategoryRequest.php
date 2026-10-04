<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuCategoryRequest extends FormRequest
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
        $category = $this->route('menuCategory');
        $cafeId = $category->cafe_id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('menu_categories')->where(function ($query) use ($cafeId) {
                    return $query->where('cafe_id', $cafeId)->whereNull('deleted_at');
                })->ignore($category->id)
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
