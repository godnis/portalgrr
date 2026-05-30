<?php

namespace App\Http\Middleware;

use App\Models\Auditoria;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioAtivo
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ Se não tem login, segue
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $status = strtolower((string)($user->status ?? 'ativo'));

        // ✅ bloqueia suspenso/desligado
        if (in_array($status, ['suspenso', 'desligado'], true)) {

            // ✅ auditoria: bloqueio de acesso por status (antes de deslogar)
            try {
                Auditoria::create([
                    'user_id'       => $user->id,
                    'acao'          => 'acesso_bloqueado_status',
                    'entidade_tipo' => 'User',
                    'entidade_id'   => $user->id,
                    'ip'            => $request->ip(),
                    'user_agent'    => (string) $request->userAgent(),
                    'detalhes'      => [
                        'status'  => $status,
                        'rota'    => $request->path(),
                        'metodo'  => $request->method(),
                        'url'     => $request->fullUrl(),
                    ],
                ]);
            } catch (\Throwable $e) {
                // se auditoria falhar, NÃO deixa quebrar o acesso
            }

            // ✅ desloga e mata sessão
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // ✅ mensagem amigável (vai aparecer no seu bloco de erros do login)
            $msg = $status === 'suspenso'
                ? 'Você não pode acessar o sistema pois está suspenso.'
                : 'Você não pode acessar o sistema pois está desligado.';

            return redirect()
                ->route('login')
                ->withErrors(['email' => $msg]);
        }

        return $next($request);
    }
}
