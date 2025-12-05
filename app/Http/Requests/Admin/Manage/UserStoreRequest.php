<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
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
            'matriculation' => ['nullable','string','max:9','unique:users,matriculation'],
            'cpf' => ['nullable','string','size:14','unique:users,cpf','formato_cpf','cpf'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required','string','email:rfc,dns','max:255','unique:users,email'],
            'occupation_id' => ['nullable', Rule::exists('occupations', 'id')],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after:1950-01-01'],
            'gender_id' => ['nullable', Rule::exists('genders', 'id')],
            'phone_personal' => ['nullable', 'celular_com_ddd', 'min:14', 'max:15'],
            'phone_work' => ['nullable', 'celular_com_ddd', 'min:14', 'max:15'],
        ];
    }

    public function messages(): array
    {
        return [
            'matriculation.required' => 'A matrícula é obrigatória.',
            'matriculation.unique' => 'Esta matrícula já está cadastrada.',

            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está em uso.',
            'cpf.cpf' => 'O CPF não é válido.',
            'cpf.formato_cpf' => 'Formato inválido. Use o padrão 000.000.000-00.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',

            'name.required' => 'O nome é obrigatório.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode ser futura.',
            'birth_date.after' => 'A data de nascimento deve ser após 01/01/1950.',
        ];
    }
}
