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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::user()->id)],
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
            'matriculation.unique' => 'Esta matrÃ­cula estÃ¡ cadastrada.',
            'cpf.unique' => 'Este CPF jÃ¡ estÃ¡ em uso.',
            'cpf.cpf' => 'O CPF nÃ£o Ã© vÃ¡lido.',
            'cpf.formato_cpf' => 'Formato invÃ¡lido.',
            'name.required' => 'O nome Ã© obrigatÃ³rio.',
            'birth_date.before_or_equal' => 'A data de nascimento nÃ£o pode ser futura.',
            'birth_date.after' => 'A data de nascimento deve ser apÃ³s 01/01/1950.',
            'gender_id.integer' => 'O campo gÃªnero deve ser um nÃºmero inteiro.',
            'gender_id.exists' => 'O gÃªnero selecionado Ã© invÃ¡lido.',
            'occupation_id.integer' => 'O campo ocupaÃ§Ã£o deve ser um nÃºmero inteiro.',
            'occupation_id.exists' => 'A ocupaÃ§Ã£o selecionada Ã© invÃ¡lida.',
        ];
    }
}
