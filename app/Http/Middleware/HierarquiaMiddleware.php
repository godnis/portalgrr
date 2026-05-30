<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditoriaLogger;

class HierarquiaMiddleware
{
    public function handle(Request $request, Closure $next, int $nivelMinimo): Response
    {
        // ✅ não autenticado
        if (!auth()->check()) {
            AuditoriaLogger::log(
                'acesso_negado_nao_autenticado',
                null,
                'Sistema',
                null,
                [
                    'motivo'        => 'nao_autenticado',
                    'nivel_minimo'  => (int) $nivelMinimo,
                    'route_name'    => $request->route()?->getName(),
                ],
                $request
            );

            abort(403);
        }

        $user = auth()->user();
        $nivelAtual = (int) ($user->nivel ?? 0);

        // ✅ nível insuficiente
        if ($nivelAtual < (int) $nivelMinimo) {
            AuditoriaLogger::log(
                'acesso_negado_hierarquia',
                (int) $user->id,
                'Sistema',
                null,
                [
                    'motivo'        => 'nivel_insuficiente',
                    'nivel_atual'   => $nivelAtual,
                    'nivel_minimo'  => (int) $nivelMinimo,
                    'route_name'    => $request->route()?->getName(),
                ],
                $request
            );

            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
