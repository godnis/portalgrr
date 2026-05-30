<?php

namespace App\Http\Middleware;

use App\Models\RhPermission;
use Closure;
use Illuminate\Http\Request;

class RhEditPermission
{
    /**
     * Middleware de edição por seção do RH
     * Uso nas rotas:
     *  - ->middleware('rh.edit:hierarquia')
     *  - ->middleware('rh.edit:controle_saida')
     */
    public function handle(Request $request, Closure $next, string $section)
    {
        abort_unless(auth()->check(), 401, 'Acesso restrito.');

        $user = auth()->user();
        $userId = (int) ($user->id ?? 0);
        abort_unless($userId > 0, 401, 'Acesso restrito.');

        // ✅ Nível 9+ sempre pode editar
        $nivel = (int) ($user->nivel ?? 0);
        if ($nivel >= 9) {
            return $next($request);
        }

        // ✅ Permissão do usuário (1:1)
        $perm = RhPermission::where('user_id', $userId)->first();

        // ✅ Seção -> campo no banco (can_hierarquia / can_controle_saida)
        $field = RhPermission::fieldFor($section);
        abort_unless($field, 500, 'Seção inválida.');

        // ✅ Sem registro de permissão = não pode
        if (!$perm) {
            abort(403, 'Você não tem permissão para editar este módulo.');
        }

        $allowed = (bool) ($perm->{$field} ?? false);

        abort_unless($allowed, 403, 'Você não tem permissão para editar este módulo.');

        return $next($request);
    }
}
