<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\DiscordService;

class EfetivoController extends Controller
{
    /**
     * ✅ Centraliza cargos no config/grr.php em 'cargos'
     * - Se não existir, usa fallback para não quebrar produção.
     */
    private function cargos(): array
    {
        $fromConfig = (array) config('grr.cargos', []);

        if (!empty($fromConfig)) {
            $out = [];
            foreach ($fromConfig as $k => $v) {
                $out[(int) $k] = (string) $v;
            }
            ksort($out);
            return $out;
        }

        return [
            1  => 'Aluno',
            2  => 'Agente de 3º Classe',
            3  => 'Agente de 2º Classe',
            4  => 'Agente de 1º Classe',
            5  => 'Agente Especial',
            6  => 'Inspetor',
            7  => 'Superintendente',
            8  => 'Coordenador',
            9  => 'Vice Diretor',
            10 => 'Diretor',
        ];
    }

    /**
     * ✅ Helper: cria log de auditoria PROFISSIONAL (colunas + detalhes)
     */
    private function audit(Request $request, string $acao, ?User $alvo = null, array $detalhes = []): void
    {
        $actor = auth()->user();

        $rid = (string) ($request->attributes->get('request_id') ?: '');
        if ($rid === '') {
            $rid = (string) Str::uuid();
            $request->attributes->set('request_id', $rid);
        }

        $routeName = $request->route()?->getName();

        $base = [
            'request_id'    => $rid,

            'actor_user_id' => $actor?->id,
            'actor_rg'      => $actor?->rg ?? null,
            'actor_nome'    => $actor?->name ?? null,

            'alvo_user_id'  => $alvo?->id,
            'alvo_rg'       => $alvo?->rg ?? null,
            'alvo_nome'     => $alvo?->name ?? null,
            'alvo_email'    => $alvo?->email ?? null,
            'alvo_nivel'    => $alvo?->nivel ?? null,
            'alvo_cargo'    => $alvo?->cargo ?? null,
            'alvo_status'   => $alvo?->status ?? null,

            'route_name'    => $routeName,
            'method'        => $request->method(),
            'url'           => $request->fullUrl(),
            'ip'            => $request->ip(),
            'user_agent'    => (string) $request->userAgent(),
        ];

        Auditoria::create([
            'request_id'    => $rid,

            'user_id'       => $actor?->id,
            'actor_rg'      => $actor?->rg ?? null,
            'actor_nome'    => $actor?->name ?? null,

            'acao'          => $acao,

            'entidade_tipo' => $alvo ? 'User' : 'Sistema',
            'entidade_id'   => $alvo?->id,

            'alvo_user_id'  => $alvo?->id,
            'alvo_rg'       => $alvo?->rg ?? null,
            'alvo_nome'     => $alvo?->name ?? null,

            'route_name'    => $routeName,
            'method'        => $request->method(),
            'url'           => $request->fullUrl(),

            'ip'            => $request->ip(),
            'user_agent'    => (string) $request->userAgent(),

            'detalhes'      => array_merge($detalhes, $base),
        ]);
    }

    /**
     * ✅ Regras de permissão (centralizadas)
     */
    private function canManageEfetivo(User $actor): bool
    {
        return (int) $actor->nivel >= 9;
    }

    /**
     * ✅ NOVO: Promoções liberadas para nível 8+
     */
    private function canPromoteEfetivo(User $actor): bool
    {
        return (int) $actor->nivel >= 8;
    }

    private function canManageHighRanks(User $actor): bool
    {
        return (int) $actor->nivel >= 10;
    }

    /**
     * ✅ Guard central para ações (9+)
     */
    private function guardManage(Request $request, string $acaoNegada, ?User $alvo = null): void
    {
        $auth = auth()->user();
        if (!$auth) abort(403);

        if (!$this->canManageEfetivo($auth)) {
            $this->audit($request, $acaoNegada, $alvo, [
                'motivo'      => 'nivel_insuficiente',
                'nivel_actor' => (int) $auth->nivel,
            ]);
            abort(403, 'Você não tem permissão para executar esta ação.');
        }
    }

    /**
     * ✅ NOVO: Guard específico de promoção (8+)
     */
    private function guardPromote(Request $request, string $acaoNegada, ?User $alvo = null): void
    {
        $auth = auth()->user();
        if (!$auth) abort(403);

        if (!$this->canPromoteEfetivo($auth)) {
            $this->audit($request, $acaoNegada, $alvo, [
                'motivo'      => 'nivel_insuficiente',
                'nivel_actor' => (int) $auth->nivel,
            ]);
            abort(403, 'Você não tem permissão para promover.');
        }
    }

    /**
     * ✅ Valida se ator pode alterar alvo/nivel (regra central)
     */
    private function assertPodeAlterarNivel(User $actor, User $alvo, int $novoNivel, Request $request): ?\Illuminate\Http\RedirectResponse
    {
        if ((int) $actor->id === (int) $alvo->id && (int) $novoNivel !== (int) $alvo->nivel) {
            $this->audit($request, 'efetivo_update_negado', $alvo, [
                'motivo' => 'tentou_alterar_proprio_nivel',
                'antes'  => $alvo->only(['nivel']),
                'depois' => ['nivel' => (int) $novoNivel],
            ]);
            return back()->withInput()->with('error', 'Você não pode alterar o seu próprio nível.');
        }

        if ((int) $novoNivel > (int) $actor->nivel) {
            $this->audit($request, 'efetivo_update_negado', $alvo, [
                'motivo'           => 'tentou_definir_nivel_acima_do_proprio',
                'nivel_solicitado' => (int) $novoNivel,
                'nivel_actor'      => (int) $actor->nivel,
            ]);
            return back()->withInput()->with('error', 'Você não pode definir nível acima do seu.');
        }

        if (!$this->canManageHighRanks($actor)) {
            if ((int) $alvo->nivel >= 9 || (int) $novoNivel >= 9) {
                $this->audit($request, 'efetivo_update_negado', $alvo, [
                    'motivo'           => 'tentou_mexer_em_9_ou_10_sem_ser_10',
                    'nivel_alvo_atual' => (int) $alvo->nivel,
                    'nivel_solicitado' => (int) $novoNivel,
                    'nivel_actor'      => (int) $actor->nivel,
                ]);
                return back()->withInput()->with('error', 'Apenas o Diretor (nível 10) pode gerenciar níveis 9/10.');
            }
        }

        return null;
    }

    public function index(Request $request)
    {
        $q = User::query()
            ->orderByDesc('nivel')
            ->orderBy('name');

        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $q->where(function ($w) use ($term) {
                $w->where('name', 'like', "%{$term}%")
                    ->orWhere('rg', 'like', "%{$term}%")
                    ->orWhere('cargo', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('discord', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('nivel')) {
            $q->where('nivel', (int) $request->nivel);
        }

        $perPage = (int) $request->input('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $users = $q->paginate($perPage)->withQueryString();

        $stats = [
            'total'      => User::count(),
            'ativos'     => User::where('status', 'ativo')->count(),
            'suspensos'  => User::where('status', 'suspenso')->count(),
            'desligados' => User::where('status', 'desligado')->count(),
        ];

        $cargos = $this->cargos();

        $this->audit($request, 'efetivo_index_aberto', null, [
            'filtros' => $request->only(['q', 'status', 'nivel', 'per_page']),
        ]);

        return view('efetivo.index', compact('users', 'stats', 'cargos'));
    }

    public function show(Request $request, User $user)
    {
        $this->audit($request, 'efetivo_show_aberto', $user);

        $logs = Auditoria::query()
            ->where('entidade_tipo', 'User')
            ->where('entidade_id', $user->id)
            ->latest()
            ->limit(50)
            ->get();

        $cargos = $this->cargos();

        return view('efetivo.show', compact('user', 'logs', 'cargos'));
    }

    public function create(Request $request)
    {
        $this->guardManage($request, 'efetivo_create_negado');

        $cargos = $this->cargos();
        $this->audit($request, 'efetivo_create_aberto');

        return view('efetivo.create', compact('cargos'));
    }

    public function store(Request $request)
    {
        $this->guardManage($request, 'efetivo_create_negado');

        $auth = auth()->user();

        $data = $request->validate([
            'name'     => 'required|string|min:3|max:120',
            'email'    => 'required|email|max:190|unique:users,email',
            'rg'       => 'required|string|min:1|max:30|unique:users,rg',
            'nivel'    => 'required|integer|min:1|max:10',
            'status'   => ['required', Rule::in(['ativo', 'suspenso', 'desligado'])],
            'password' => 'required|string|min:6|max:120',
        ]);

        if ((int) $data['nivel'] > (int) $auth->nivel) {
            $this->audit($request, 'efetivo_create_negado', null, [
                'motivo'           => 'tentou_criar_nivel_acima_do_proprio',
                'nivel_solicitado' => (int) $data['nivel'],
                'nivel_actor'      => (int) $auth->nivel,
                'email_solicitado' => $data['email'] ?? null,
                'rg_solicitado'    => $data['rg'] ?? null,
            ]);
            return back()->withInput()->with('error', 'Você não pode cadastrar alguém com nível acima do seu.');
        }

        if (!$this->canManageHighRanks($auth) && (int) $data['nivel'] >= 9) {
            $this->audit($request, 'efetivo_create_negado', null, [
                'motivo'           => 'tentou_criar_9_ou_10_sem_ser_10',
                'nivel_solicitado' => (int) $data['nivel'],
                'nivel_actor'      => (int) $auth->nivel,
                'email_solicitado' => $data['email'] ?? null,
                'rg_solicitado'    => $data['rg'] ?? null,
            ]);
            return back()->withInput()->with('error', 'Apenas o Diretor (nível 10) pode cadastrar nível 9/10.');
        }

        $cargos = $this->cargos();
        $cargoAuto = $cargos[(int) $data['nivel']] ?? 'Aluno';

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'rg'       => $data['rg'],
            'nivel'    => (int) $data['nivel'],
            'cargo'    => $cargoAuto,
            'status'   => $data['status'],
            'password' => $data['password'], // cast hashed no User
        ]);

        $this->audit($request, 'efetivo_criado', $user, [
            'criado' => $user->only(['id', 'name', 'email', 'rg', 'nivel', 'cargo', 'status']),
        ]);

        return redirect()->route('efetivo.index')->with('success', 'Oficial cadastrado com sucesso.');
    }

    public function edit(Request $request, User $user)
    {
        $this->guardManage($request, 'efetivo_update_negado', $user);

        $cargos = $this->cargos();
        $this->audit($request, 'efetivo_edit_aberto', $user);

        return view('efetivo.edit', compact('user', 'cargos'));
    }

    public function update(Request $request, User $user)
    {
        $this->guardManage($request, 'efetivo_update_negado', $user);

        if ($user->discord) {
            $discordApp = new DiscordService();
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $user->discord,
                    'color' => 'Green',
                    'user' => [
                        'rg' => $user->rg,
                        'nome' => $user->name,
                        'cargo' => $user->cargo,
                    ],
                    'message' => "Seus dados foram atualizados no portal.
                    
                    **RG:** {$user->rg}
                    **Nome:** {$user->name}
                    **Cargo:** {$user->cargo}",
                ]
            );
        }

        if ($user->motivo_suspensao == 'Solicitar acesso pelo bot.') {
            return back()->with('error', 'O policial deve fazer a solicitação pelo bot para reativar a conta.');
        }

        $auth = auth()->user();

        $data = $request->validate([
            'name'     => 'required|string|min:3|max:120',
            'rg'       => ['required', 'string', 'min:1', 'max:30', Rule::unique('users', 'rg')->ignore($user->id)],
            'nivel'    => 'required|integer|min:1|max:10',
            'status'   => ['required', Rule::in(['ativo', 'suspenso', 'desligado'])],
            'motivo'   => 'nullable|string|max:200',
            'password' => 'nullable|string|min:6|max:120',
        ]);

        if ($resp = $this->assertPodeAlterarNivel($auth, $user, (int) $data['nivel'], $request)) {
            return $resp;
        }

        $mudouStatus = ((string) $data['status'] !== (string) ($user->status ?? ''));
        if ($mudouStatus && in_array($data['status'], ['suspenso', 'desligado'], true)) {
            if (trim((string) ($data['motivo'] ?? '')) === '') {
                $this->audit($request, 'efetivo_update_negado', $user, [
                    'motivo'            => 'faltou_motivo_para_suspender_ou_desligar',
                    'status_solicitado' => $data['status'],
                ]);
                return back()->withInput()->with('error', 'Informe um motivo para suspender/desligar.');
            }
        }

        $cargos = $this->cargos();

        $old = $user->only(['name', 'email', 'rg', 'cargo', 'nivel', 'status']);

        $user->name   = $data['name'];
        $user->rg     = $data['rg'];
        $user->nivel  = (int) $data['nivel'];
        $user->cargo  = $cargos[(int) $data['nivel']] ?? $user->cargo;
        $user->status = $data['status'];

        $senhaAlterada = false;
        if (!empty($data['password'])) {
            $user->password = $data['password']; // cast hashed no User
            $senhaAlterada = true;
        }

        $user->save();

        $this->audit($request, 'efetivo_editado', $user, [
            'antes'          => $old,
            'depois'         => $user->only(['name', 'email', 'rg', 'cargo', 'nivel', 'status']),
            'motivo'         => $data['motivo'] ?? null,
            'senha_alterada' => $senhaAlterada,
        ]);

        return redirect()->route('efetivo.edit', $user->id)->with('success', $senhaAlterada
            ? 'Cadastro e senha atualizados com sucesso.'
            : 'Cadastro atualizado com sucesso.');
    }

    /**
     * ✅ PROMOÇÃO RÁPIDA (INDIVIDUAL) — 8+
     */
    public function promover(Request $request, User $user)
    {
        $this->guardPromote($request, 'efetivo_promocao_negada', $user);

        $auth = auth()->user();

        if ((int) $user->id === (int) $auth->id) {
            $this->audit($request, 'efetivo_promocao_negada', $user, [
                'motivo' => 'tentou_promover_a_si_mesmo',
            ]);
            return back()->with('error', 'Você não pode promover a si mesmo.');
        }

        $data = $request->validate([
            'motivo' => 'required|string|min:3|max:200',
        ]);

        if ((string) $user->status !== 'ativo') {
            $this->audit($request, 'efetivo_promocao_negada', $user, [
                'motivo'      => 'alvo_nao_ativo',
                'status_alvo' => (string) $user->status,
            ]);
            return back()->with('error', 'Apenas oficiais ATIVOS podem ser promovidos.');
        }

        $novoNivel = min(10, ((int) $user->nivel) + 1);

        if ($novoNivel === (int) $user->nivel) {
            return back()->with('error', 'Este oficial já está no nível máximo.');
        }

        if ($resp = $this->assertPodeAlterarNivel($auth, $user, $novoNivel, $request)) {
            return $resp;
        }

        $cargos = $this->cargos();
        $antes = $user->only(['nivel', 'cargo', 'status']);

        $user->nivel = $novoNivel;
        $user->cargo = $cargos[$novoNivel] ?? $user->cargo;
        $user->save();

        $this->audit($request, 'efetivo_promovido', $user, [
            'antes'  => $antes,
            'depois' => $user->only(['nivel', 'cargo', 'status']),
            'motivo' => $data['motivo'],
        ]);

        return back()->with('success', 'Oficial promovido com sucesso.');
    }

    /**
     * ✅ PROMOÇÃO EM MASSA — 8+
     */
    public function promoverMassa(Request $request)
    {
        $this->guardPromote($request, 'efetivo_promocao_massa_negada');

        $auth = auth()->user();

        $data = $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
            'modo'   => ['required', Rule::in(['up', 'set'])],
            'nivel'  => 'nullable|integer|min:1|max:10',
            'motivo' => 'required|string|min:3|max:200',
        ]);

        if ($data['modo'] === 'set' && empty($data['nivel'])) {
            return back()->with('error', 'Informe o nível para “Definir nível”.');
        }

        $cargos = $this->cargos();
        $ids = array_values(array_unique(array_map('intval', $data['ids'])));

        $ok = 0;
        $skipped = 0;

        DB::transaction(function () use ($request, $auth, $data, $cargos, $ids, &$ok, &$skipped) {
            $users = User::query()->whereIn('id', $ids)->lockForUpdate()->get();

            foreach ($users as $u) {
                if ((string) $u->status !== 'ativo') {
                    $skipped++;
                    continue;
                }

                $novoNivel = ((string) $data['modo'] === 'set')
                    ? (int) $data['nivel']
                    : min(10, ((int) $u->nivel) + 1);

                if ($novoNivel === (int) $u->nivel) {
                    $skipped++;
                    continue;
                }
                if ((int) $novoNivel > (int) $auth->nivel) {
                    $skipped++;
                    continue;
                }

                if (!$this->canManageHighRanks($auth) && (((int) $u->nivel >= 9) || ((int) $novoNivel >= 9))) {
                    $skipped++;
                    continue;
                }

                if ((int) $u->id === (int) $auth->id) {
                    $skipped++;
                    continue;
                }

                $antes = $u->only(['nivel', 'cargo', 'status']);

                $u->nivel = $novoNivel;
                $u->cargo = $cargos[$novoNivel] ?? $u->cargo;
                $u->save();

                $this->audit($request, 'efetivo_promovido_massa', $u, [
                    'antes'  => $antes,
                    'depois' => $u->only(['nivel', 'cargo', 'status']),
                    'motivo' => $data['motivo'],
                    'modo'   => (string) $data['modo'],
                ]);

                $ok++;
            }
        });

        $this->audit($request, 'efetivo_promocao_massa_executada', null, [
            'modo'         => (string) $data['modo'],
            'nivel_set'    => $data['modo'] === 'set' ? (int) $data['nivel'] : null,
            'motivo'       => (string) $data['motivo'],
            'ids_enviados' => $ids,
            'promovidos'   => $ok,
            'ignorados'    => $skipped,
        ]);

        return back()->with('success', "Promoção em massa concluída: {$ok} promovidos, {$skipped} ignorados.");
    }

    public function suspender(Request $request, User $user)
    {
        $this->guardManage($request, 'efetivo_suspender_negado', $user);

        $data = $request->validate([
            'motivo' => 'required|string|min:3|max:200',
        ]);

        $old = $user->only(['status']);

        $user->status = 'suspenso';
        $user->save();

        $this->audit($request, 'efetivo_suspenso', $user, [
            'antes'  => $old,
            'depois' => $user->only(['status']),
            'motivo' => $data['motivo'],
        ]);

        return back()->with('success', 'Oficial suspenso.');
    }

    public function reativar(Request $request, User $user)
    {
        $this->guardManage($request, 'efetivo_reativar_negado', $user);

        $old = $user->only(['status']);

        if ($user->motivo_suspensao == 'Solicitar acesso pelo bot.') {
            return back()->with('error', 'O policial deve fazer a solicitação pelo bot para reativar a conta.');
        }

        $user->status = 'ativo';
        $user->save();

        $this->audit($request, 'efetivo_reativado', $user, [
            'antes'  => $old,
            'depois' => $user->only(['status']),
        ]);

        return back()->with('success', 'Oficial reativado.');
    }

    public function destroy(Request $request, User $user)
    {
        $auth = auth()->user();
        if (!$auth) abort(403);

        if (!$this->canManageHighRanks($auth)) {
            $this->audit($request, 'efetivo_destroy_negado', $user, [
                'motivo'      => 'nivel_insuficiente',
                'nivel_actor' => (int) $auth->nivel,
            ]);
            abort(403, 'Apenas Diretor (nível 10) pode remover definitivamente.');
        }

        $data = $request->validate([
            'motivo' => 'required|string|min:3|max:200',
        ]);

        $this->audit($request, 'efetivo_removido', $user, [
            'motivo'   => $data['motivo'],
            'snapshot' => $user->only(['id', 'name', 'email', 'rg', 'cargo', 'nivel', 'status']),
        ]);

        $user->delete();

        return redirect()->route('efetivo.index')->with('success', 'Oficial removido.');
    }
}
