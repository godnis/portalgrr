<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoAcesso;
use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class SolicitacaoAcessoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PÚBLICO — Enviar solicitação
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'      => ['required', 'string', 'max:80'],
            'sobrenome' => ['required', 'string', 'max:80'],
            'rg'        => ['required', 'string', 'max:30'],
        ]);

        $nome      = $this->capName($data['nome']);
        $sobrenome = $this->capName($data['sobrenome']);
        $rg        = $this->normRg($data['rg']);

        if (!$nome || !$sobrenome) {
            return back()->withErrors([
                'nome' => 'Informe nome e sobrenome válidos.'
            ])->withInput();
        }

        if (!$rg) {
            return back()->withErrors([
                'rg' => 'RG inválido.'
            ])->withInput();
        }

        $email = "{$nome}.{$sobrenome}@grr.com";

        if (User::query()->where('email', $email)->exists()) {
            return back()->withErrors([
                'email' => 'Já existe um usuário com este e-mail institucional.'
            ])->withInput();
        }

        if (User::query()->where('rg', $rg)->exists()) {
            return back()->withErrors([
                'rg' => 'Este RG já está cadastrado no sistema.'
            ])->withInput();
        }

        if (SolicitacaoAcesso::query()->where('rg', $rg)->exists()) {
            return back()->withErrors([
                'rg' => 'Já existe uma solicitação registrada para este RG.'
            ])->withInput();
        }

        $recentIp = SolicitacaoAcesso::query()
            ->where('ip', (string) $request->ip())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($recentIp) {
            return back()->withErrors([
                'nome' => 'Aguarde alguns minutos antes de enviar nova solicitação.'
            ])->withInput();
        }

        try {
            SolicitacaoAcesso::create([
                'nome'         => $nome,
                'sobrenome'    => $sobrenome,
                'rg'           => $rg,
                'email'        => $email,
                'status'       => 'pendente',
                'motivo'       => null,
                'ip'           => (string) $request->ip(),
                'user_agent'   => Str::limit((string) $request->userAgent(), 250, ''),
                'decidido_por' => null,
                'decidido_em'  => null,
            ]);
        } catch (QueryException $e) {
            return back()->withErrors([
                'nome' => 'Já existe uma solicitação semelhante cadastrada. Verifique os dados ou aguarde aprovação.'
            ])->withInput();
        }

        return back()->with('status', '✅ Solicitação enviada! Aguarde a análise.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN / LIBERADOS — Listagem
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $this->ensureCanAccessModulo();

        $status = trim((string) $request->get('status', 'pendente'));
        $qRaw   = trim((string) $request->get('q', ''));

        $query = SolicitacaoAcesso::query();

        $statusLower = mb_strtolower($status);
        $isTodos = ($status === '' || in_array($statusLower, ['todos', 'todas', 'all'], true));

        if (!$isTodos) {
            $query->where('status', $status);
        }

        if ($qRaw !== '') {
            $q = $qRaw;
            $qRg = $this->normRg($qRaw);

            $query->where(function ($w) use ($q, $qRg) {
                $w->whereRaw("nome || ' ' || sobrenome LIKE ?", ["%{$q}%"])
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('discord', 'like', "%{$q}%")
                    ->orWhere('rg', 'like', '%' . ($qRg ?: $q) . '%');
            });
        }

        $solicitacoes = $query
            ->orderByRaw("CASE status WHEN 'pendente' THEN 0 WHEN 'aprovado' THEN 1 WHEN 'reprovado' THEN 2 ELSE 9 END")
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $q = $qRaw;

        $delegaveis = collect();
        $usuariosComAcessoSolicitacoes = collect();

        if ($this->userCanManageModulo()) {
            $delegaveis = User::query()
                ->select(['id', 'name', 'rg', 'cargo', 'nivel'])
                ->where(function ($q) {
                    $q->where('ativo', true)
                        ->orWhere('status', 'ativo');
                })
                ->orderByDesc('nivel')
                ->orderBy('name')
                ->get();

            $accessIds = $this->getModuloAccessUserIds();

            if (!empty($accessIds)) {
                $usuariosComAcessoSolicitacoes = User::query()
                    ->select(['id', 'name', 'rg', 'cargo', 'nivel'])
                    ->whereIn('id', $accessIds)
                    ->orderByDesc('nivel')
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('admin.solicitacoes.index', compact(
            'solicitacoes',
            'status',
            'q',
            'delegaveis',
            'usuariosComAcessoSolicitacoes'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN / LIBERADOS — Visualizar
    |--------------------------------------------------------------------------
    */
    public function show(SolicitacaoAcesso $solicitacao)
    {
        $this->ensureCanAccessModulo();

        return view('admin.solicitacoes.show', compact('solicitacao'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN — Tela de edição
    |--------------------------------------------------------------------------
    */
    public function edit(SolicitacaoAcesso $solicitacao)
    {
        $this->ensureCanManageModulo();

        return view('admin.solicitacoes.edit', compact('solicitacao'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN — Atualizar
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, SolicitacaoAcesso $solicitacao)
    {
        $this->ensureCanManageModulo();

        if ($solicitacao->status !== 'pendente') {
            return back()->withErrors([
                'status' => 'Só é possível editar solicitações pendentes.'
            ]);
        }

        $data = $request->validate([
            'nome'      => ['required', 'string', 'max:80'],
            'sobrenome' => ['required', 'string', 'max:80'],
            'rg'        => ['required', 'string', 'max:30'],
        ]);

        $nome      = $this->capName($data['nome']);
        $sobrenome = $this->capName($data['sobrenome']);
        $rg        = $this->normRg($data['rg']);

        if (!$nome || !$sobrenome) {
            return back()->withErrors([
                'nome' => 'Informe nome e sobrenome válidos.'
            ])->withInput();
        }

        if (!$rg) {
            return back()->withErrors([
                'rg' => 'RG inválido.'
            ])->withInput();
        }

        $email = "{$nome}.{$sobrenome}@grr.com";

        if (
            User::query()
            ->where('email', $email)
            ->exists()
        ) {
            return back()->withErrors([
                'email' => 'Já existe um usuário com este e-mail institucional.'
            ])->withInput();
        }

        if (
            User::query()
            ->where('rg', $rg)
            ->exists()
        ) {
            return back()->withErrors([
                'rg' => 'Já existe um usuário com este RG.'
            ])->withInput();
        }

        if (
            SolicitacaoAcesso::query()
            ->where('rg', $rg)
            ->where('id', '!=', $solicitacao->id)
            ->exists()
        ) {
            return back()->withErrors([
                'rg' => 'Já existe uma solicitação com este RG.'
            ])->withInput();
        }

        if (
            SolicitacaoAcesso::query()
            ->where('email', $email)
            ->where('id', '!=', $solicitacao->id)
            ->exists()
        ) {
            return back()->withErrors([
                'email' => 'Já existe uma solicitação com este e-mail.'
            ])->withInput();
        }

        $solicitacao->update([
            'nome'      => $nome,
            'sobrenome' => $sobrenome,
            'rg'        => $rg,
            'email'     => $email,
        ]);

        return back()->with('status', '✅ Solicitação atualizada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN / LIBERADOS — Aprovar
    |--------------------------------------------------------------------------
    */
    public function aprovar(Request $request, SolicitacaoAcesso $solicitacao)
    {
        $this->ensureCanDecideSolicitacoes();

        if ($solicitacao->status !== 'pendente') {
            return back()->withErrors([
                'status' => 'Essa solicitação não está pendente.'
            ]);
        }

        if (User::query()->where('email', $solicitacao->email)->exists()) {
            return back()->withErrors([
                'email' => 'Já existe usuário com este e-mail.'
            ]);
        }

        if (User::query()->where('rg', $solicitacao->rg)->exists()) {
            return back()->withErrors([
                'rg' => 'Já existe usuário com este RG.'
            ]);
        }

        if (!$solicitacao->discord) {
            return back()->withErrors([
                'discord' => 'O Discord não pode estar em branco.'
            ]);
        }

        if (User::query()->where('discord', $solicitacao->discord)->exists()) {
            return back()->withErrors([
                'discord' => 'Já existe usuário com este Discord.'
            ]);
        }

        $nivelInicial = 1;
        $cargos = (array) config('grr.cargos', []);
        $senhaInicial = $this->generatePassword(12);

        DB::transaction(function () use ($solicitacao, $nivelInicial, $cargos, $senhaInicial) {
            User::create([
                'name'     => trim($solicitacao->nome . ' ' . $solicitacao->sobrenome),
                'email'    => $solicitacao->email,
                'discord'  => $solicitacao->discord,
                'password' => Hash::make($senhaInicial),
                'rg'       => $solicitacao->rg,
                'nivel'    => $nivelInicial,
                'cargo'    => $cargos[$nivelInicial] ?? 'Aluno',
                'status'   => 'ativo',
                'ativo'    => true,
            ]);

            $solicitacao->update([
                'status'       => 'aprovado',
                'motivo'       => null,
                'decidido_por' => (int) (auth()->id() ?? 0) ?: null,
                'decidido_em'  => now(),
            ]);
        });

        $login = $solicitacao->email;

        if ($solicitacao->discord) {
            $discord = new DiscordService();
            $discord->enviar(
                '/solicitacao-aprovar',
                [
                    'userId' => $solicitacao->discord,
                    'solicitacao' => $solicitacao->id,
                    'passaporte' => $solicitacao->rg,
                    'nome' => $solicitacao->nome,
                    'sobrenome' => $solicitacao->sobrenome,
                    'status' => 'aprovado',
                    'login' => $login,
                    'senha' => $senhaInicial,
                ]
            );
        }

        $copyMessage =
            ":closed_lock_with_key: **Acesso ao Sistema Interno — GRR**

Seu acesso ao sistema foi criado com sucesso.

**Login:** {$login}
**Senha inicial:** {$senhaInicial}

:warning: Recomendamos que altere sua senha no primeiro acesso.

Para isso, acesse:
**Meu Perfil → Segurança → Atualizar Senha**

:globe_with_meridians: **Acessar o Sistema:**
[Nosso Portal](https://www.gruporrbc.com.br/login)

:tv: **Vídeo Explicativo do Sistema:**
[Vídeo Explicativo](https://www.youtube.com/watch?v=l6C81u-8XmE)

Fique atento ao vídeo com a explicação completa sobre o funcionamento do painel.

Em caso de dúvidas, estamos à disposição.";

        $approved = session()->get('approved_creds', []);
        $approved[$solicitacao->id] = [
            'email' => $login,
            'senha' => $senhaInicial,
            // 'msg'   => $copyMessage,
        ];
        session()->put('approved_creds', $approved);

        $redirectTo = url()->previous();

        return redirect()
            ->to($redirectTo)
            ->with('status', "✅ Solicitação de {$solicitacao->nome} {$solicitacao->sobrenome} aprovada com sucesso!");
            // ->with('copy_message', $copyMessage);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN / LIBERADOS — Reprovar
    |--------------------------------------------------------------------------
    */
    public function reprovar(Request $request, SolicitacaoAcesso $solicitacao)
    {
        $this->ensureCanDecideSolicitacoes();

        $data = $request->validate([
            'motivo' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($solicitacao->status !== 'pendente') {
            return back()->withErrors([
                'status' => 'Essa solicitação não está pendente.'
            ]);
        }

        if ($data['motivo'] == '') {
            return back()->withErrors([
                'status' => 'O motivo da reprovação é obrigatório.'
            ]);
        }

        $reprovadoPor = User::query()
                ->where('id', auth()->id())
                ->first();

        if ($solicitacao->discord) {
            $discord = new DiscordService();
            $discord->enviar(
                '/solicitacao-reprovar',
                [
                    'userId' => $solicitacao->discord,
                    'solicitacao' => $solicitacao->id,
                    'passaporte' => $solicitacao->rg,
                    'nome' => $solicitacao->nome,
                    'sobrenome' => $solicitacao->sobrenome,
                    'status' => 'reprovado',
                    'reprovado_por' => $reprovadoPor->name,
                    'motivo' => $data['motivo'] ?? null,
                ]
            );
        }

        $solicitacao->update([
            'status'       => 'reprovado',
            'motivo'       => $data['motivo'] ?? null,
            'decidido_por' => (int) (auth()->id() ?? 0) ?: null,
            'decidido_em'  => now(),
        ]);

        return back()->with('status', 'Solicitação reprovada.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN — Atualizar acessos globais do módulo
    |--------------------------------------------------------------------------
    */
    public function updateAcessosGlobais(Request $request)
    {
        $this->ensureCanManageModulo();

        $data = $request->validate([
            'users'   => ['nullable', 'array'],
            'users.*' => ['integer', 'exists:users,id'],
        ]);

        if (!Schema::hasTable('solicitacao_acesso_permissions')) {
            return back()->withErrors([
                'status' => 'A tabela de permissões do módulo ainda não foi criada.'
            ]);
        }

        $ids = collect($data['users'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($ids) {
            DB::table('solicitacao_acesso_permissions')->delete();

            $now = now();

            if (!empty($ids)) {
                $rows = array_map(function ($id) use ($now) {
                    return [
                        'user_id'     => $id,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }, $ids);

                DB::table('solicitacao_acesso_permissions')->insert($rows);
            }
        });

        return back()->with('status', '✅ Acessos do módulo atualizados com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers — Permissões do módulo
    |--------------------------------------------------------------------------
    */
    private function ensureCanAccessModulo(): void
    {
        abort_unless($this->userCanAccessModulo(), 403);
    }

    private function ensureCanManageModulo(): void
    {
        abort_unless($this->userCanManageModulo(), 403);
    }

    private function ensureCanDecideSolicitacoes(): void
    {
        abort_unless($this->userCanDecideSolicitacoes(), 403);
    }

    private function userCanAccessModulo(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ((int) ($user->nivel ?? 0) >= 9) {
            return true;
        }

        return in_array((int) $user->id, $this->getModuloAccessUserIds(), true);
    }

    private function userCanManageModulo(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return (int) ($user->nivel ?? 0) >= 9;
    }

    private function userCanDecideSolicitacoes(): bool
    {
        return $this->userCanAccessModulo();
    }

    private function getModuloAccessUserIds(): array
    {
        if (!Schema::hasTable('solicitacao_acesso_permissions')) {
            return [];
        }

        return DB::table('solicitacao_acesso_permissions')
            ->pluck('user_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers — Gerais
    |--------------------------------------------------------------------------
    */
    private function normRg(string $v): ?string
    {
        $rg = preg_replace('/\D+/', '', (string) $v);
        return $rg !== '' ? $rg : null;
    }

    private function capName(string $v): string
    {
        $v = trim((string) $v);
        $v = preg_replace('/[^\p{L}\s-]+/u', '', $v) ?? '';
        $v = trim($v);

        if ($v === '') {
            return '';
        }

        $parts = preg_split('/(\s|-)/u', $v, -1, PREG_SPLIT_DELIM_CAPTURE);
        $out = '';

        foreach ($parts as $p) {
            if ($p === ' ' || $p === '-') {
                $out .= $p;
                continue;
            }

            $low = mb_strtolower($p, 'UTF-8');
            $first = mb_strtoupper(mb_substr($low, 0, 1, 'UTF-8'), 'UTF-8');
            $rest  = mb_substr($low, 1, null, 'UTF-8');
            $out .= $first . $rest;
        }

        return str_replace([' ', '-'], '', $out);
    }

    private function generatePassword(int $len = 12): string
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $digits  = '23456789';
        $symbols = '@#%-';

        $all = $letters . $digits . $symbols;

        $pass  = '';
        $pass .= $letters[random_int(0, strlen($letters) - 1)];
        $pass .= $digits[random_int(0, strlen($digits) - 1)];
        $pass .= $symbols[random_int(0, strlen($symbols) - 1)];

        for ($i = 3; $i < $len; $i++) {
            $pass .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($pass);
    }
}
