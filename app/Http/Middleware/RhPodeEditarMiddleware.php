<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RhPodeEditarMiddleware
{
    public function handle(Request $request, Closure $next, string $section)
    {
        $user = auth()->user();
        abort_unless($user, 401);

        // Nível 9+ sempre pode (admin RH)
        $nivel = (int)($user->nivel ?? 0);
        if ($nivel >= 9) {
            return $next($request);
        }

        // Permissão específica por seção
        abort_unless($user->podeEditarRh($section), 403, 'Sem permissão de edição.');

        return $next($request);
    }
}
