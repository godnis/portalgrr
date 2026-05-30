<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Services\AuditoriaLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuditoriaController extends Controller
{
    /**
     * ✅ Lê os grupos do config/grr.php em grr.auditoria.grupos
     * Formato esperado:
     *  'Efetivo' => [ 'efetivo_index_aberto' => 'Acesso...', ... ],
     *  'Tickets' => [ ... ],
     */
    private function configGroups(): array
    {
        $cfg = config('grr.auditoria.grupos', []);
        if (!is_array($cfg)) return [];

        $out = [];

        foreach ($cfg as $groupLabel => $items) {
            if (!is_array($items)) continue;

            $clean = [];
            foreach ($items as $code => $label) {
                // aceita lista simples também (['acao1','acao2'])
                if (is_int($code)) {
                    $code  = (string) $label;
                    $label = null;
                }

                $code = trim((string) $code);
                if ($code === '') continue;

                $lbl = trim((string) ($label ?? ''));
                if ($lbl === '') {
                    $lbl = ucwords(str_replace('_', ' ', $code));
                }

                $clean[$code] = $lbl;
            }

            if (!empty($clean)) {
                $out[(string) $groupLabel] = $clean;
            }
        }

        return $out;
    }

    /**
     * ✅ Monta grupos/labels para a VIEW com base:
     * - Config grr.auditoria.grupos (labels bonitos)
     * - Ações reais existentes no banco (distinct)
     * - Tudo que não estiver no config cai em "Outros"
     *
     * Retorno:
     * [
     *   'efetivo' => ['label'=>'Efetivo', 'items'=>['acao'=>'Label', ...]],
     *   'outros'  => ['label'=>'Outros',  'items'=>[...]]
     * ]
     */
    private function buildGroupsForView(): array
    {
        $configGroups = $this->configGroups();

        $acoesDb = Auditoria::query()
            ->select('acao')
            ->whereNotNull('acao')
            ->distinct()
            ->orderBy('acao')
            ->pluck('acao')
            ->map(fn ($v) => (string) $v)
            ->values()
            ->all();

        $acoesDbSet = array_flip($acoesDb);

        // labels vindos do config (flat)
        $labels = [];
        foreach ($configGroups as $gLabel => $map) {
            foreach ($map as $code => $lbl) {
                $labels[(string) $code] = (string) $lbl;
            }
        }

        // completa labels para tudo que existir no banco
        foreach ($acoesDb as $code) {
            if (!isset($labels[$code])) {
                $labels[$code] = ucwords(str_replace('_', ' ', $code));
            }
        }

        // monta grupos filtrando apenas ações que existem no banco
        $groups = [];

        foreach ($configGroups as $groupLabel => $map) {
            $gid = Str::slug((string) $groupLabel, '_');
            if ($gid === '') $gid = 'grupo';

            $items = [];
            foreach ($map as $code => $lbl) {
                $code = (string) $code;
                if (!isset($acoesDbSet[$code])) continue; // só mostra se existe no banco
                $items[$code] = $labels[$code] ?? (string) $lbl;
            }

            if (!empty($items)) {
                // ordena por label
                uasort($items, fn ($a, $b) => strcmp((string) $a, (string) $b));
                $groups[$gid] = [
                    'label' => (string) $groupLabel,
                    'items' => $items,
                ];
            }
        }

        // "Outros" = ações do banco não mapeadas em nenhum grupo do config
        $mapped = [];
        foreach ($groups as $g) {
            foreach (array_keys($g['items']) as $c) $mapped[$c] = true;
        }

        $outros = [];
        foreach ($acoesDb as $code) {
            if (!isset($mapped[$code])) {
                $outros[$code] = $labels[$code] ?? ucwords(str_replace('_', ' ', $code));
            }
        }
        if (!empty($outros)) {
            uasort($outros, fn ($a, $b) => strcmp((string) $a, (string) $b));
            $groups['outros'] = [
                'label' => 'Outros',
                'items' => $outros,
            ];
        }

        return [$groups, $labels];
    }

    public function index(Request $request)
    {
        // ✅ TTL real do desbloqueio
        $unlockedUntil = $request->session()->get('audit_unlocked_until');
        $unlocked = false;
        $unlockedUntilTs = null;

        if ($unlockedUntil) {
            try {
                $until = Carbon::parse($unlockedUntil);
                $unlocked = now()->lt($until);
                $unlockedUntilTs = $until->timestamp;
            } catch (\Throwable $e) {
                $unlocked = false;
                $unlockedUntilTs = null;
            }
        }

        if (!$unlocked) {
            $request->session()->forget(['audit_unlocked_until']);
        }

        // ✅ Puxa e já limpa (auto-abrir só 1 vez)
        $openAfter = $request->session()->pull('audit_open_after');

        $q = Auditoria::query()
            ->with('user')
            ->latest();

        // ✅ filtro ação
        if ($request->filled('acao')) {
            $q->where('acao', (string) $request->acao);
        }

        /**
         * ✅ filtro por RG (ator e alvo)
         * - Ator: relacionamento user.rg (user_id do log)
         * - Ator/Alvo: detalhes->actor_rg / detalhes->alvo_rg
         */
        if ($request->filled('rg')) {
            $rg = trim((string) $request->rg);

            $q->where(function ($w) use ($rg) {
                $w->whereHas('user', function ($u) use ($rg) {
                    $u->where('rg', $rg);
                });

                $w->orWhere('detalhes->actor_rg', $rg);
                $w->orWhere('detalhes->alvo_rg', $rg);
            });
        }

        // ✅ filtro entidade_tipo
        if ($request->filled('entidade_tipo')) {
            $q->where('entidade_tipo', (string) $request->entidade_tipo);
        }

        // ✅ período
        if ($request->filled('data_inicio')) {
            $q->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $q->whereDate('created_at', '<=', $request->data_fim);
        }

        $perPage = (int) $request->input('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 30, 50, 100], true) ? $perPage : 25;

        $auditorias = $q->paginate($perPage)->withQueryString();

        // ✅ monta grupos/labels via config + DB
        [$groups, $acaoLabel] = $this->buildGroupsForView();

        // ✅ entidades para dropdown (para o filtro)
        $entidades = Auditoria::query()
            ->select('entidade_tipo')
            ->whereNotNull('entidade_tipo')
            ->distinct()
            ->orderBy('entidade_tipo')
            ->pluck('entidade_tipo')
            ->values()
            ->all();

        return view('auditoria.index', compact(
            'auditorias',
            'acaoLabel',
            'groups',
            'entidades',
            'unlocked',
            'openAfter',
            'unlockedUntilTs'
        ));
    }

    public function revelar(Request $request)
    {
        // ✅ senha hash no .env (AUDITORIA_SENHA_HASH)
        $senhaHash = (string) config('auditoria.senha_hash', '');
        $ttl = (int) config('auditoria.ttl_minutes', 10);
        $ttl = $ttl > 0 ? $ttl : 10;

        if (trim($senhaHash) === '') {
            return back()->with('error', 'Senha HASH da auditoria não configurada no .env.');
        }

        $data = $request->validate([
            'senha'      => 'required|string|min:1|max:150',
            'open_after' => 'nullable|string|max:80',
            'log_id'     => 'nullable|integer|exists:auditorias,id',
        ]);

        $senhaDigitada = (string) $data['senha'];
        $ok = Hash::check($senhaDigitada, $senhaHash);

        // ✅ registra tentativa
        AuditoriaLogger::log(
            $ok ? 'auditoria_unlock_sucesso' : 'auditoria_unlock_falha',
            auth()->id(),
            'Auditoria',
            $data['log_id'] ?? null,
            [
                'requested_log_id' => $data['log_id'] ?? null,
                'open_after'       => $data['open_after'] ?? null,
                'actor_user_id'    => auth()->id(),
                'actor_rg'         => auth()->user()?->rg ?? null,
                'actor_nome'       => auth()->user()?->name ?? null,
            ],
            $request
        );

        if (!$ok) {
            return back()->with('error', 'Senha inválida.');
        }

        // ✅ TTL real
        $request->session()->put('audit_unlocked_until', now()->addMinutes($ttl)->toDateTimeString());
        $request->session()->put('audit_open_after', $data['open_after'] ?? null);

        return redirect()
            ->route('auditoria.index', $request->query())
            ->with('success', "Auditoria desbloqueada por {$ttl} minuto(s).");
    }

    public function travar(Request $request)
    {
        AuditoriaLogger::log(
            'auditoria_lock',
            auth()->id(),
            'Auditoria',
            null,
            [
                'actor_user_id' => auth()->id(),
                'actor_rg'      => auth()->user()?->rg ?? null,
                'actor_nome'    => auth()->user()?->name ?? null,
            ],
            $request
        );

        $request->session()->forget(['audit_unlocked_until', 'audit_open_after']);

        return back()->with('success', 'Auditoria travada.');
    }
}
