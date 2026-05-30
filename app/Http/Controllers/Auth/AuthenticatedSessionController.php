<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditoriaLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        /**
         * ✅ 1) Tenta autenticar
         * - Se falhar, o LoginRequest dispara o evento Failed
         * - O listener LogFailedLogin já registra a auditoria
         */
        $request->authenticate();

        $user = Auth::user();

        /**
         * ✅ 2) BLOQUEIO POR STATUS (suspenso / desligado)
         * - Aqui SIM é responsabilidade do controller
         * - Listener NÃO cobre esse cenário
         */
        if (Schema::hasColumn('users', 'status')) {
            $status = strtolower((string) ($user->status ?? 'ativo'));

            if (in_array($status, ['suspenso', 'desligado'], true)) {

                AuditoriaLogger::log(
                    'login_bloqueado',
                    $user->id,
                    'User',
                    $user->id,
                    [
                        'motivo'       => 'status_bloqueado',
                        'status'       => $status,
                        'alvo_user_id' => $user->id,
                        'alvo_rg'      => $user->rg,
                        'alvo_nome'    => $user->name,
                        'alvo_email'   => $user->email,
                    ],
                    $request
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $msg = $status === 'suspenso'
                    ? 'Você não pode logar pois está suspenso!'
                    : 'Você não pode logar pois está desligado!';

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => $msg]);
            }
        }

        /**
         * ✅ 3) BLOQUEIO POR ATIVO = false
         */
        if (Schema::hasColumn('users', 'ativo')) {
            if (!(bool) $user->ativo) {

                AuditoriaLogger::log(
                    'login_bloqueado',
                    $user->id,
                    'User',
                    $user->id,
                    [
                        'motivo'       => 'usuario_inativo',
                        'ativo'        => false,
                        'alvo_user_id' => $user->id,
                        'alvo_rg'      => $user->rg,
                        'alvo_nome'    => $user->name,
                        'alvo_email'   => $user->email,
                    ],
                    $request
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Você não pode logar pois está inativo!']);
            }
        }

        /**
         * ✅ 4) LOGIN OK
         * - NÃO registra auditoria aqui
         * - O evento Login será disparado automaticamente
         * - Listener LogLogin já registra
         */
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        /**
         * ✅ Logout
         * - NÃO registra auditoria aqui
         * - Listener LogLogout cuida disso
         */
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
