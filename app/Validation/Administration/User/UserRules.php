<?php

namespace App\Validation\Administration\User;

use Illuminate\Validation\Rule;

/**
 * Esta classe centraliza as regras de validação
 * para permitir reutilização entre Livewire e FormRequests.
 *
 * Motivo:
 * - Livewire não utiliza FormRequest nativamente
 * - Evita duplicação de regras entre Livewire e Controllers
 * - Mantém as validações desacopladas da camada HTTP
 *
 * Use esta classe:
 * - Diretamente no Livewire via $this->validate()
 * - Dentro de FormRequests quando o fluxo for HTTP
 */

class UserRules
{
    public static function store(): array
    {
        return [
            'matriculation' => ['nullable','string','max:9','unique:users,matriculation'],
            'cpf' => ['nullable','string','size:14','unique:users,cpf','formato_cpf','cpf'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required','string','email:rfc,dns','max:255','unique:users,email'],
            'occupation_id' => ['nullable', Rule::exists('occupations', 'id')],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after:1950-01-01'],
            'gender_id' => ['nullable', Rule::exists('genders', 'id')],
            'phone_personal' => ['nullable', 'min:14', 'max:15'],
            'phone_work' => ['nullable', 'min:14', 'max:15'],
        ];
    }

    public static function update(?int $userId): array
    {
        return [
            'matriculation' => [ 'nullable','string','max:9',Rule::unique('users', 'matriculation')->ignore($userId),],
            'cpf' => ['nullable','string','formato_cpf','cpf',Rule::unique('users', 'cpf')->ignore($userId)],
            'name' => ['required', 'string', 'max:255'],
            'occupation_id' => ['nullable', Rule::exists('occupations', 'id')],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after:1950-01-01'],
            'gender_id' => ['nullable', Rule::exists('genders', 'id')],
            'phone_personal' => ['nullable', 'min:14', 'max:15'],
            'phone_work' => ['nullable', 'min:14', 'max:15'],
        ];
    }

    public static function permissionUpdate()
    {
        return [
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
