<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
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
        $id = $this->route('admin');

        return [
            'name' => 'required|string|max:255',
            'email' => 'email|string|max:255|unique:admins,email,' . $id . ',id',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:8',
            'roles.*' => 'nullable'
        ];
    }
}
