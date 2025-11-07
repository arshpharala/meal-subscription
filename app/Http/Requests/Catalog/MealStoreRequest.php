<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class MealStoreRequest extends FormRequest
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
        return [
            'name'              => 'required|string|max:200|unique:meals,name',
            'tagline'           => 'required|string',
            'slug'              => 'required|string|max:100|unique:meals,slug',
            'position'          => 'nullable|integer',
            'is_active'         => 'nullable|boolean',
            'sample_menu_file'  => 'nullable|file|mimes:pdf|max:4096'
        ];
    }
}
