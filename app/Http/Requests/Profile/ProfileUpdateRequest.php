<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'matriculation' => [ 'nullable','string','max:9',Rule::unique('users', 'matriculation')->ignore(Auth::user()->id),],
            'cpf' => ['nullable','string','formato_cpf','cpf',Rule::unique('users', 'cpf')->ignore(Auth::user()->id),],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after:1950-01-01'],
            'phone_personal' => ['nullable', 'celular_com_ddd', 'min:14', 'max:15'],
            'phone_work' => ['nullable', 'celular_com_ddd', 'min:14', 'max:15'],
            'gender_id' => ['nullable', Rule::exists('genders', 'id')],
            'occupation_id' => ['nullable', Rule::exists('occupations', 'id')],
        ];
    }
    
    public function messages(): array
    {
        return [
            'matriculation.unique' => 'Esta matrícula está cadastrada.',
            'cpf.unique' => 'Este CPF já está em uso.',
            'cpf.cpf' => 'O CPF não é válido.',
            'cpf.formato_cpf' => 'Formato inválido.',
            'name.required' => 'O nome é obrigatório.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode ser futura.',
            'birth_date.after' => 'A data de nascimento deve ser após 01/01/1950.',
            'gender_id.integer' => 'O campo gênero deve ser um número inteiro.',
            'gender_id.exists' => 'O gênero selecionado é inválido.',
            'occupation_id.integer' => 'O campo ocupação deve ser um número inteiro.',
            'occupation_id.exists' => 'A ocupação selecionada é inválida.',
        ];
    }
}
