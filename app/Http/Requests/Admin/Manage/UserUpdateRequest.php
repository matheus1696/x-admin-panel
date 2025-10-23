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
            'matriculation' => [ 'nullable','string','max:9',Rule::unique('users', 'matriculation')->ignore($this->user->id),],
            'cpf' => ['nullable','string','formato_cpf','cpf',Rule::unique('users', 'cpf')->ignore($this->user->id),],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after:1950-01-01'],
            'gender' => ['nullable', 'in:Masculino,Feminino'],
            'phone_personal' => ['nullable', 'celular_com_ddd', 'min:14', 'max:15'],
            'phone_work' => ['nullable', 'celular_com_ddd', 'min:14', 'max:15'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'matriculation.required' => 'A matrícula é obrigatória.',
            'matriculation.unique' => 'Esta matrícula está cadastrada.',

            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está em uso.',
            'cpf.cpf' => 'O CPF não é válido.',
            'cpf.formato_cpf' => 'Formato inválido.',

            'name.required' => 'O nome é obrigatório.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode ser futura.',
            'birth_date.after' => 'A data de nascimento deve ser após 01/01/1950.',
        ];
    }
}
