<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
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
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',

            // Optional: only required if user chooses to add address
            'type' => 'nullable|in:home,work,other',

            // Address fields — required only when type is provided
            'province_id' => 'nullable|required_with:type|exists:provinces,id',
            'city_id'     => 'nullable|required_with:type|exists:cities,id',
            'area_id'     => 'nullable|required_with:type|exists:areas,id',
            'address'     => 'nullable|required_with:type|string|max:255',
            'landmark'    => 'nullable|string|max:255',
        ];
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Please select an address type',
            'type.in' => 'Invalid address type selected',
            'province_id.required' => 'Please select a province',
            'city_id.required' => 'Please select a city',
            'area_id.required' => 'Please select an area',
            'address.required' => 'Please enter your address'
        ];
    }
}
