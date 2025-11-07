<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class CalorieUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = request()->calory;

        return [
            'label' => 'required|string|unique:calories,label,' . $id . ',id',
            'min_kcal' => 'required|integer|min:1',
            'max_kcal' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean'
        ];
    }
}
