<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuItemRequest extends FormRequest
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
        $menuItem = $this->route('menuItem');
        $cafeId = $menuItem->cafe_id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'menu_category_id' => [
                'nullable',
                'exists:menu_categories,id',
                \Illuminate\Validation\Rule::exists('menu_categories', 'id')->where('cafe_id', $cafeId)->whereNull('deleted_at')
            ],
            'image_path' => ['nullable', 'string', 'max:255'],
            'is_available' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
