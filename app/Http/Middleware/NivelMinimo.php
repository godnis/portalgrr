<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NivelMinimo
{
    public function handle(Request $request, Closure $next, int $nivelMinimo)
    {
        $nivel = (int) (auth()->user()->nivel ?? 0);

        if ($nivel < $nivelMinimo) {
            abort(403, 'Acesso restrito.');
        }

        return $next($request);
    }
}
