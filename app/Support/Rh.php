<?php

namespace App\Support;

use App\Models\RhPermission;
use App\Models\User;

class Rh
{
    /**
     * Verifica se o usuário pode EDITAR uma seção do RH
     */
    public static function canEdit(User $user, string $section): bool
    {
        // 🔒 Nível 9+ sempre pode
        $nivel = (int)($user->nivel ?? 0);
        if ($nivel >= 9) {
            return true;
        }

        // Campo correspondente à seção
        $field = RhPermission::fieldFor($section);
        if (!$field) {
            return false;
        }

        // Permissão individual
        $perm = RhPermission::where('user_id', $user->id)->first();

        return (bool)($perm?->{$field} ?? false);
    }
}
