<?php

namespace App\Http\Controllers;

use App\Models\RhPermission;
use App\Models\User;
use App\Services\AuditoriaLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RhPermissionController extends Controller
{
    private function requireNivel9(): void
    {
        abort_unless(auth()->check(), 403, 'Acesso restrito.');
        $nivel = (int) (auth()->user()->nivel ?? 0);
        abort_unless($nivel >= 9, 403, 'Acesso restrito (nível 9+).');
    }

    public function index(Request $request)
    {
        $this->requireNivel9();

        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('rg', 'like', "%{$q}%");

                    if (ctype_digit($q)) {
                        $sub->orWhere('id', (int) $q);
                    }
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $perms = RhPermission::query()
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->keyBy('user_id');

        return view('rh.permissions', compact('users', 'perms', 'q'));
    }

    public function update(Request $request, User $user)
    {
        $this->requireNivel9();

        // ✅ AGORA SÓ 2 CAMPOS (porque sua blade só envia esses 2)
        $data = $request->validate([
            'can_hierarquia'     => ['required', 'in:0,1'],
            'can_controle_saida' => ['required', 'in:0,1'],
        ]);

        $payload = [
            'user_id'             => (int) $user->id,
            'can_hierarquia'      => ((int) $data['can_hierarquia'] === 1),
            'can_controle_saida'  => ((int) $data['can_controle_saida'] === 1),
            'updated_by'          => (int) auth()->id(),
        ];

        $perm = RhPermission::firstOrNew(['user_id' => $user->id]);

        $before = $perm->exists ? $perm->only([
            'can_hierarquia',
            'can_controle_saida',
            'updated_by',
        ]) : null;

        $perm->forceFill($payload);
        $perm->save();

        $fresh = RhPermission::where('user_id', $user->id)->first();

        Log::info('RH perms saved (2 campos)', [
            'target_user_id' => $user->id,
            'payload'        => $payload,
            'db_after'       => $fresh?->only([
                'can_hierarquia',
                'can_controle_saida',
                'updated_by',
            ]),
        ]);

        if (class_exists(AuditoriaLogger::class)) {
            AuditoriaLogger::log('rh_permissoes_atualizadas', [
                'actor_id'         => auth()->id(),
                'target_user_id'   => $user->id,
                'target_user_nome' => $user->name ?? null,
                'before'           => $before,
                'after'            => $fresh?->only([
                    'can_hierarquia',
                    'can_controle_saida',
                    'updated_by',
                ]),
            ]);
        }

        return back()->with('success', 'Permissões do RH atualizadas com sucesso.');
    }
}
