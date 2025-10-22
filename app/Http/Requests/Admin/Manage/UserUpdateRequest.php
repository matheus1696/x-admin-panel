<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'matriculation' => [
                'required',
                'string',
                'max:9',
                Rule::unique('users', 'matriculation')->ignore($this->user->id),
                'regex:/^\d{2}\.\d{3}-\d{2}$/',
            ],
            'cpf' => [
                'required',
                'string',
                'size:14',
                Rule::unique('users', 'cpf')->ignore($this->user->id),
                'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after:1950-01-01'],
            'gender' => ['nullable', 'in:Masculino,Feminino'],
            'phone_personal' => ['nullable', 'string', 'min:14', 'max:15'],
            'phone_work' => ['nullable', 'string', 'min:14', 'max:15'],
            'status' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
