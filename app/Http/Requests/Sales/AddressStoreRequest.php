<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class AddressStoreRequest extends FormRequest
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
            'phone' => 'required|string|max:20',
            'type' => 'required|in:home,work,other',

            'province_id' => 'required|exists:provinces,id',
            'city_id'     => 'required|exists:cities,id',
            'area_id'     => 'required|exists:areas,id',
            'address'     => 'required|string|max:255',
            'landmark'    => 'nullable|string|max:255',
        ];
    }
}
