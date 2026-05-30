<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ✅ Bloqueia alteração de name/email para usuário comum.
     * ✅ Libera apenas para nível alto (ex.: Diretor).
     */
    public function rules(): array
    {
        $user = $this->user();
        $nivel = (int)($user->nivel ?? 0);

        // ✅ Ajuste aqui o nível que pode editar Nome/E-mail
        $podeEditarIdentidade = $nivel >= 10; // Diretor+

        // Se não puder editar, não valida nada (e o Controller não deve salvar name/email)
        if (!$podeEditarIdentidade) {
            return [];
        }

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ];
    }
}
