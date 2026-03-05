<?php

namespace App\Services\Administration\Supplier;

use Illuminate\Support\Facades\Http;
use Throwable;

class ViaCepService
{
    /**
     * @return array{status: 'success'|'not_found'|'error'|'invalid', data?: array<string, mixed>}
     */
    public function lookup(string $zipcode): array
    {
        $digits = preg_replace('/\D/', '', $zipcode);

        if (strlen((string) $digits) !== 8) {
            return ['status' => 'invalid'];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(6)
                ->get("https://viacep.com.br/ws/{$digits}/json/");
        } catch (Throwable) {
            return ['status' => 'error'];
        }

        if (! $response->successful()) {
            return ['status' => 'error'];
        }

        $data = $response->json();

        if (! is_array($data)) {
            return ['status' => 'error'];
        }

        if (($data['erro'] ?? false) === true) {
            return ['status' => 'not_found'];
        }

        return [
            'status' => 'success',
            'data' => [
                'cep' => (string) ($data['cep'] ?? ''),
                'logradouro' => (string) ($data['logradouro'] ?? ''),
                'bairro' => (string) ($data['bairro'] ?? ''),
                'localidade' => (string) ($data['localidade'] ?? ''),
                'uf' => (string) ($data['uf'] ?? ''),
            ],
        ];
    }
}
