<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken() !== '123') {
            return response()->json(
                [
                    'sucesso' => false,
                    'mensagem' => 'Acesso não autorizado'
                ],
                401
            );
        }

        return $next($request);
    }
}
