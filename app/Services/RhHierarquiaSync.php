<?php

namespace App\Services;

use App\Models\RhHierarquiaRecord;
use App\Models\User;

class RhHierarquiaSync
{
    /**
     * Sincroniza SOMENTE: nome, rg e cargo
     * Mantém todo o resto manual.
     */
    public static function syncFromUser(User $user): void
    {
        $nome  = (string) ($user->name ?? '');
        $cargo = (string) ($user->cargo ?? '');
        $rg    = (string) ($user->rg ?? '');

        if ($nome === '' && $cargo === '' && $rg === '') {
            return;
        }

        $row = RhHierarquiaRecord::firstOrNew(['user_id' => $user->id]);

        // Atualiza SOMENTE as 3 infos
        if ($nome !== '')  $row->nome  = $nome;
        if ($cargo !== '') $row->cargo = $cargo;

        // Aqui você pode decidir:
        // - guardar RG na hierarquia (se tiver coluna)
        // - ou só exibir vindo do user (como no blade já está)
        // Vou manter só exibindo do user por enquanto.

        $row->updated_by = auth()->id() ?? $row->updated_by;
        $row->save();
    }
}
