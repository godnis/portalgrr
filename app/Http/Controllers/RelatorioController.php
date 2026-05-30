<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use App\Models\User;
use App\Services\AuditoriaLogger;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RelatorioController extends Controller
{
    private function normRG($rg): ?string
    {
        if ($rg === null) return null;

        $rg = trim((string) $rg);
        if ($rg === '') return null;

        $rg = preg_replace('/\D+/', '', $rg);
        return $rg !== '' ? $rg : null;
    }

    private function normUnidade($u): ?string
    {
        if ($u === null) return null;

        $u = trim((string) $u);
        if ($u === '') return null;

        $u = preg_replace('/\s+/', ' ', $u);
        return $u !== '' ? $u : null;
    }

    private function unidadeKey(?string $u): string
    {
        $u = $this->normUnidade($u) ?? '';
        return mb_strtoupper($u, 'UTF-8');
    }

    private function isAdministrativo(?string $unidade): bool
    {
        $k = $this->unidadeKey($unidade);
        return $k === 'ADMINISTRATIVO';
    }

    private function normalizeInts(array &$data, array $keys): void
    {
        foreach ($keys as $k) {
            if (!array_key_exists($k, $data)) continue;

            $v = trim((string) $data[$k]);
            $data[$k] = ($v === '') ? null : (int) $v;
        }
    }

    private function normalizeBopmRegistros($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                $value = [$value];
            }
        }

        if (!is_array($value)) {
            return [];
        }

        $out = [];
        foreach ($value as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $out[] = $item;
            }
        }

        return array_values($out);
    }

    private function validateBopmRegistros(array &$data): void
    {
        $bopmQtd = isset($data['bopm']) && $data['bopm'] !== null ? (int) $data['bopm'] : 0;

        $registros = $this->normalizeBopmRegistros($data['bopm_registros'] ?? []);
        $data['bopm_registros'] = $registros;

        if ($bopmQtd < 0) {
            throw ValidationException::withMessages([
                'bopm' => 'A quantidade de BOPM não pode ser negativa.',
            ]);
        }

        if ($bopmQtd === 0) {
            $data['bopm_registros'] = [];
            return;
        }

        if (count($registros) !== $bopmQtd) {
            throw ValidationException::withMessages([
                'bopm_registros' => "Você informou {$bopmQtd} BOPM, então precisa preencher exatamente {$bopmQtd} registro(s) de BOPM.",
            ]);
        }
    }

    private function getUsuarioPatrulhaStatus(?string $rg): array
    {
        $rg = $this->normRG($rg);

        if (!$rg) {
            return [
                'ok' => false,
                'reason' => 'RG inválido.',
                'user' => null,
            ];
        }

        $user = User::query()
            ->where('rg', $rg)
            ->first(['id', 'name', 'cargo', 'rg', 'ativo', 'status']);

        if (!$user) {
            return [
                'ok' => false,
                'reason' => 'RG não encontrado no efetivo.',
                'user' => null,
            ];
        }

        $status = mb_strtolower(trim((string) ($user->status ?? '')), 'UTF-8');
        $ativo = (bool) ($user->ativo ?? false);

        if (in_array($status, ['suspenso', 'suspensa'], true)) {
            return [
                'ok' => false,
                'reason' => 'Este policial está suspenso e não pode patrulhar.',
                'user' => $user,
            ];
        }

        if (in_array($status, ['desligado', 'desligada', 'exonerado', 'exonerada'], true)) {
            return [
                'ok' => false,
                'reason' => 'Este policial está desligado e não pode patrulhar.',
                'user' => $user,
            ];
        }

        if (!$ativo) {
            return [
                'ok' => false,
                'reason' => 'Este policial está inativo e não pode patrulhar.',
                'user' => $user,
            ];
        }

        return [
            'ok' => true,
            'reason' => null,
            'user' => $user,
        ];
    }

    private function validarUsuariosPatrulha(array $campos): void
    {
        $labels = [
            'qra_chefe' => 'Chefe da barca',
            'motorista' => 'Motorista',
            'terceiro'  => 'Auxiliar P3',
            'quarto'    => 'Auxiliar P4',
            'quinto'    => 'Auxiliar P5',
        ];

        $errors = [];

        foreach ($campos as $campo => $rg) {
            $rg = $this->normRG($rg);
            if (!$rg) continue;

            $check = $this->getUsuarioPatrulhaStatus($rg);

            if (!$check['ok']) {
                $errors[$campo] = ($labels[$campo] ?? $campo) . ': ' . $check['reason'];
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function validateRgs(Request $request, bool $motoristaObrigatorio = true): void
    {
        $request->merge([
            'motorista' => $this->normRG($request->input('motorista')),
            'terceiro'  => $this->normRG($request->input('terceiro')),
            'quarto'    => $this->normRG($request->input('quarto')),
            'quinto'    => $this->normRG($request->input('quinto')),
            'qra_chefe' => $this->normRG($request->input('qra_chefe')),
        ]);

        $rules = [
            'qra_chefe' => ['required', 'string', 'max:255'],
            'unidade'   => ['required', 'string', 'max:255'],

            'motorista' => $motoristaObrigatorio ? ['required', 'exists:users,rg'] : ['nullable', 'exists:users,rg'],
            'terceiro'  => ['nullable', 'exists:users,rg'],
            'quarto'    => ['nullable', 'exists:users,rg'],
            'quinto'    => ['nullable', 'exists:users,rg'],

            'inicio_patrulhamento' => ['required', 'date_format:H:i'],

            'pistolas'   => ['nullable', 'integer', 'min:0'],
            'smg_fuzil'  => ['nullable', 'integer', 'min:0'],
            'municoes'   => ['nullable', 'integer', 'min:0'],
            'drogas'     => ['nullable', 'integer', 'min:0'],
            'explosivos' => ['nullable', 'integer', 'min:0'],
            'lockpicks'  => ['nullable', 'integer', 'min:0'],
            'dinheiro'   => ['nullable', 'integer', 'min:0'],

            'abordagens'            => ['nullable', 'integer', 'min:0'],
            'apoio'                 => ['nullable', 'integer', 'min:0'],
            'incursao'              => ['nullable', 'integer', 'min:0'],
            'negociacao'            => ['nullable', 'integer', 'min:0'],
            'blitz'                 => ['nullable', 'integer', 'min:0'],
            'escolta'               => ['nullable', 'integer', 'min:0'],
            'multas'                => ['nullable', 'integer', 'min:0'],
            'bopm'                  => ['nullable', 'integer', 'min:0'],
            'viaturas_fiscalizadas' => ['nullable', 'integer', 'min:0'],

            'bopm_registros'   => ['nullable', 'array'],
            'bopm_registros.*' => ['nullable', 'string', 'max:255'],

            'observacoes' => ['nullable', 'string', 'max:5000'],

            'client_token' => ['nullable', 'string', 'max:64'],
            'redirect_to'  => ['nullable', 'string', 'max:2048'],
        ];

        $messages = [
            'motorista.exists' => 'Motorista: RG não encontrado no efetivo.',
            'terceiro.exists'  => 'Auxiliar P3: RG não encontrado no efetivo.',
            'quarto.exists'    => 'Auxiliar P4: RG não encontrado no efetivo.',
            'quinto.exists'    => 'Auxiliar P5: RG não encontrado no efetivo.',
        ];

        $request->validate($rules, $messages);

        $rgs = array_filter([
            $request->motorista,
            $request->terceiro,
            $request->quarto,
            $request->quinto,
        ]);

        if (count($rgs) !== count(array_unique($rgs))) {
            back()
                ->withErrors(['motorista' => 'Existe RG repetido entre Motorista/P3/P4/P5.'])
                ->withInput()
                ->throwResponse();
        }

        $this->validarUsuariosPatrulha([
            'qra_chefe' => $request->qra_chefe,
            'motorista' => $request->motorista,
            'terceiro'  => $request->terceiro,
            'quarto'    => $request->quarto,
            'quinto'    => $request->quinto,
        ]);
    }

    public function index(Request $request)
    {
        $fRg = $this->normRG($request->get('rg'));
        $fStatus = $request->get('status');

        $allowedStatus = ['em_patrulha', 'pendente', 'aprovado', 'reprovado'];
        if (!in_array($fStatus, $allowedStatus, true)) {
            $fStatus = null;
        }

        $relatorios = Relatorio::query()
            ->when($fStatus, fn($q) => $q->where('status', $fStatus))
            ->when($fRg, function ($q) use ($fRg) {
                $q->where(function ($qq) use ($fRg) {
                    $qq->where('qra_chefe', $fRg)
                        ->orWhere('motorista', $fRg)
                        ->orWhere('terceiro', $fRg)
                        ->orWhere('quarto', $fRg)
                        ->orWhere('quinto', $fRg);
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query());

        $rgs = collect($relatorios->items())
            ->flatMap(function ($r) {
                return [
                    $r->qra_chefe,
                    $r->motorista,
                    $r->terceiro,
                    $r->quarto,
                    $r->quinto,
                ];
            })
            ->filter()
            ->map(fn($rg) => preg_replace('/\D+/', '', (string) $rg))
            ->filter(fn($rg) => $rg !== '')
            ->unique()
            ->values()
            ->all();

        $usersByRg = User::query()
            ->whereIn('rg', $rgs)
            ->get(['id', 'name', 'cargo', 'rg', 'ativo', 'status'])
            ->keyBy('rg');

        $ids = collect($relatorios->items())
            ->flatMap(fn($r) => [(int)($r->aprovado_por ?? 0), (int)($r->reprovado_por ?? 0)])
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $usersById = User::query()
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'cargo', 'rg', 'ativo', 'status'])
            ->keyBy('id');

        return view('relatorios.index', compact('relatorios', 'fRg', 'fStatus', 'usersByRg', 'usersById'));
    }

    public function unidadeStatus(Request $request)
    {
        if (!auth()->check()) abort(403);

        $unidade = $this->normUnidade($request->query('unidade'));
        $unidadeKey = $this->unidadeKey($unidade);

        $rgChefe = $this->normRG(auth()->user()->rg ?? null) ?? '';

        if ($unidadeKey === '') {
            return response()->json([
                'ok' => true,
                'unidade' => $unidade,
                'in_patrulha' => false,
                'chefe_has_open' => false,
            ]);
        }

        $openUnit = Relatorio::query()
            ->where('status', 'em_patrulha')
            ->whereNull('final_patrulhamento')
            ->whereRaw('UPPER(unidade) = ?', [$unidadeKey])
            ->orderByDesc('id')
            ->first(['id', 'unidade', 'qra_chefe', 'user_id']);

        $openChefe = null;
        if ($rgChefe !== '') {
            $openChefe = Relatorio::query()
                ->where('status', 'em_patrulha')
                ->whereNull('final_patrulhamento')
                ->where('qra_chefe', $rgChefe)
                ->orderByDesc('id')
                ->first(['id', 'unidade']);
        }

        return response()->json([
            'ok' => true,
            'unidade' => $unidade,
            'in_patrulha' => (bool) $openUnit,
            'relatorio_id' => $openUnit?->id,
            'chefe_has_open' => (bool) $openChefe,
            'chefe_relatorio_id' => $openChefe?->id,
            'chefe_unidade' => $openChefe?->unidade,
        ]);
    }

    public function iniciarTurno(Request $request)
    {
        if (!auth()->check()) abort(403);

        $rgChefe = $this->normRG(auth()->user()->rg ?? null);
        if (!$rgChefe) {
            return back()->with('error', 'Seu usuário está sem RG cadastrado. Peça para o RH corrigir.');
        }

        $request->merge([
            'unidade'   => $this->normUnidade($request->input('unidade')),
            'motorista' => $this->normRG($request->input('motorista')),
            'terceiro'  => $this->normRG($request->input('terceiro')),
            'quarto'    => $this->normRG($request->input('quarto')),
            'quinto'    => $this->normRG($request->input('quinto')),
        ]);

        $unidade = (string) ($request->input('unidade') ?? '');
        $isAdm = $this->isAdministrativo($unidade);

        if ($isAdm) {
            $request->merge([
                'terceiro' => null,
                'quarto'   => null,
                'quinto'   => null,
            ]);

            if (!$request->input('motorista')) {
                $request->merge(['motorista' => $rgChefe]);
            }
        }

        $rules = [
            'unidade'   => ['required', 'string', 'max:255'],
            'motorista' => $isAdm ? ['nullable', 'exists:users,rg'] : ['required', 'exists:users,rg'],
            'terceiro'  => ['nullable', 'exists:users,rg'],
            'quarto'    => ['nullable', 'exists:users,rg'],
            'quinto'    => ['nullable', 'exists:users,rg'],
        ];

        $messages = [
            'motorista.exists' => 'Motorista: RG não encontrado no efetivo.',
            'terceiro.exists'  => 'Auxiliar P3: RG não encontrado no efetivo.',
            'quarto.exists'    => 'Auxiliar P4: RG não encontrado no efetivo.',
            'quinto.exists'    => 'Auxiliar P5: RG não encontrado no efetivo.',
        ];

        $request->validate($rules, $messages);

        if (!$isAdm) {
            $rgs = array_filter([
                $request->motorista,
                $request->terceiro,
                $request->quarto,
                $request->quinto,
            ]);

            if (count($rgs) !== count(array_unique($rgs))) {
                return back()->with('error', 'Existe RG repetido entre Motorista/P3/P4/P5.')->withInput();
            }
        }

        $this->validarUsuariosPatrulha([
            'qra_chefe' => $rgChefe,
            'motorista' => $request->motorista,
            'terceiro'  => $request->terceiro,
            'quarto'    => $request->quarto,
            'quinto'    => $request->quinto,
        ]);

        $unidadeKey = $this->unidadeKey($unidade);

        $clientToken = bin2hex(random_bytes(16));
        $relatorio = null;

        try {
            DB::transaction(function () use ($request, $rgChefe, $clientToken, $unidadeKey, &$relatorio) {

                $unitBusy = Relatorio::query()
                    ->where('status', 'em_patrulha')
                    ->whereNull('final_patrulhamento')
                    ->whereRaw('UPPER(unidade) = ?', [$unidadeKey])
                    ->lockForUpdate()
                    ->exists();

                if ($unitBusy) {
                    throw ValidationException::withMessages([
                        'unidade' => 'Essa unidade já está em patrulha. Encerre o turno aberto antes de iniciar outro.',
                    ]);
                }

                $chefeBusy = Relatorio::query()
                    ->where('status', 'em_patrulha')
                    ->whereNull('final_patrulhamento')
                    ->where('qra_chefe', $rgChefe)
                    ->lockForUpdate()
                    ->exists();

                if ($chefeBusy) {
                    throw ValidationException::withMessages([
                        'qra_chefe' => 'Você já possui um turno em patrulha aberto. Encerre antes de iniciar outro.',
                    ]);
                }

                $relatorio = Relatorio::create([
                    'user_id'              => auth()->id(),
                    'client_token'         => $clientToken,

                    'qra_chefe'            => $rgChefe,
                    'unidade'              => (string) $request->input('unidade'),

                    'motorista'            => $request->motorista,
                    'terceiro'             => $request->terceiro,
                    'quarto'               => $request->quarto,
                    'quinto'               => $request->quinto,

                    'data_patrulhamento'   => now()->toDateString(),
                    'inicio_patrulhamento' => now()->format('H:i'),
                    'final_patrulhamento'  => null,

                    'status'               => 'em_patrulha',
                    'aprovado_por'         => null,
                    'reprovado_por'        => null,
                    'decisao_obs'          => null,
                    'bopm_registros'       => json_encode([], JSON_UNESCAPED_UNICODE),
                ]);

                if (method_exists($relatorio, 'syncParticipantesPorRG')) {
                    $relatorio->syncParticipantesPorRG();
                }

                $discordApp = new DiscordService();
                $data = now()->format('d/m/Y');
                $inicio = now()->format('H:i');

                $discordChefe = User::query()
                    ->where('rg', $rgChefe)
                    ->first(['rg', 'name', 'cargo', 'discord']);

                $discordMotorista = User::query()
                    ->where('rg', $request->motorista)
                    ->first(['rg', 'name', 'cargo', 'discord']);

                $discordTerceiro = User::query()
                    ->where('rg', $request->terceiro)
                    ->first(['rg', 'name', 'cargo', 'discord']);

                $discordQuarto = User::query()
                    ->where('rg', $request->quarto)
                    ->first(['rg', 'name', 'cargo', 'discord']);

                $discordQuinto = User::query()
                    ->where('rg', $request->quinto)
                    ->first(['rg', 'name', 'cargo', 'discord']);

                $adm = ($discordChefe && $discordMotorista && $discordChefe->rg == $discordMotorista->rg) ? "**Chefe e Motorista:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
                $chefe = ($discordChefe && $discordChefe->rg != $discordMotorista->rg) ? "**Chefe da Unidade:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
                $motorista = ($discordMotorista && $discordMotorista->rg != $discordChefe->rg) ? "**Motorista:** {$discordMotorista->cargo} {$discordMotorista->name} #{$discordMotorista->rg}" : null;
                $terceiro = $discordTerceiro ? "**Terceiro:** {$discordTerceiro->cargo} {$discordTerceiro->name} #{$discordTerceiro->rg}" : null;
                $quarto = $discordQuarto ? "**Quarto:** {$discordQuarto->cargo} {$discordQuarto->name} #{$discordQuarto->rg}" : null;
                $quinto = $discordQuinto ? "**Quinto:** {$discordQuinto->cargo} {$discordQuinto->name} #{$discordQuinto->rg}" : null;

                $linhas = array_filter([
                    $adm,
                    $chefe,
                    $motorista,
                    $terceiro,
                    $quarto,
                    $quinto,
                ]);

                $equipe = implode("\n", $linhas);

                $msg = "🚨 **Turno iniciado:**

                    **Unidade:** {$request->input('unidade')}
                    **Data:** {$data}
                    **Início:** {$inicio}
                    **Status:** Em patrulhamento

                    {$equipe}";

                if ($discordChefe && $discordChefe->discord) {
                    $discordApp->enviar(
                        '/send-dm',
                        [
                            'discord' => $discordChefe->discord,
                            'color' => 'Yellow',
                            'user' => [
                                'rg' => $discordChefe->rg,
                                'nome' => $discordChefe->name,
                                'cargo' => $discordChefe->cargo,
                            ],
                            'message' => $msg,
                        ]
                    );
                }
                if ($discordMotorista && $discordMotorista->discord && $discordChefe->rg !== $discordMotorista->rg) {
                    $discordApp->enviar(
                        '/send-dm',
                        [
                            'discord' => $discordMotorista->discord,
                            'color' => 'Yellow',
                            'user' => [
                                'rg' => $discordMotorista->rg,
                                'nome' => $discordMotorista->name,
                                'cargo' => $discordMotorista->cargo,
                            ],
                            'message' => $msg,
                        ]
                    );
                }
                if ($discordTerceiro && $discordTerceiro->discord) {
                    $discordApp->enviar(
                        '/send-dm',
                        [
                            'discord' => $discordTerceiro->discord,
                            'color' => 'Yellow',
                            'user' => [
                                'rg' => $discordTerceiro->rg,
                                'nome' => $discordTerceiro->name,
                                'cargo' => $discordTerceiro->cargo,
                            ],
                            'message' => $msg,
                        ]
                    );
                }
                if ($discordQuarto && $discordQuarto->discord) {
                    $discordApp->enviar(
                        '/send-dm',
                        [
                            'discord' => $discordQuarto->discord,
                            'color' => 'Yellow',
                            'user' => [
                                'rg' => $discordQuarto->rg,
                                'nome' => $discordQuarto->name,
                                'cargo' => $discordQuarto->cargo,
                            ],
                            'message' => $msg,
                        ]
                    );
                }
                if ($discordQuinto && $discordQuinto->discord) {
                    $discordApp->enviar(
                        '/send-dm',
                        [
                            'discord' => $discordQuinto->discord,
                            'color' => 'Yellow',
                            'user' => [
                                'rg' => $discordQuinto->rg,
                                'nome' => $discordQuinto->name,
                                'cargo' => $discordQuinto->cargo,
                            ],
                            'message' => $msg,
                        ]
                    );
                }
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        AuditoriaLogger::log(
            'relatorio_turno_iniciado',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'unidade'              => $request->input('unidade'),
                'qra_chefe'            => $rgChefe,
                'motorista'            => $request->motorista,
                'p3'                   => $request->terceiro,
                'p4'                   => $request->quarto,
                'p5'                   => $request->quinto,
                'inicio_patrulhamento' => $relatorio->inicio_patrulhamento,
                'client_token'         => $clientToken,
            ],
            $request
        );

        return redirect()
            ->route('relatorios.show', $relatorio)
            ->with('success', 'Turno iniciado! Relatório criado e marcado como EM PATRULHA.');
    }

    public function salvarRascunho(Request $request, Relatorio $relatorio)
    {
        if (!auth()->check()) abort(403);

        if ((int)$relatorio->user_id !== (int)auth()->id()) {
            abort(403, 'Somente quem iniciou o turno pode editar este relatório.');
        }

        if (!in_array((string)($relatorio->status ?? ''), ['em_patrulha', 'pendente'], true)) {
            return response()->json(['ok' => false, 'message' => 'Relatório não permite rascunho neste status.'], 422);
        }

        if (!empty($relatorio->final_patrulhamento)) {
            return response()->json(['ok' => false, 'message' => 'Relatório já encerrado.'], 409);
        }

        $ints = [
            'pistolas',
            'smg_fuzil',
            'municoes',
            'drogas',
            'explosivos',
            'lockpicks',
            'dinheiro',
            'abordagens',
            'apoio',
            'incursao',
            'negociacao',
            'blitz',
            'escolta',
            'multas',
            'bopm',
            'viaturas_fiscalizadas',
        ];

        $data = $request->validate([
            'pistolas'   => ['nullable', 'integer', 'min:0'],
            'smg_fuzil'  => ['nullable', 'integer', 'min:0'],
            'municoes'   => ['nullable', 'integer', 'min:0'],
            'drogas'     => ['nullable', 'integer', 'min:0'],
            'explosivos' => ['nullable', 'integer', 'min:0'],
            'lockpicks'  => ['nullable', 'integer', 'min:0'],
            'dinheiro'   => ['nullable', 'integer', 'min:0'],

            'abordagens'            => ['nullable', 'integer', 'min:0'],
            'apoio'                 => ['nullable', 'integer', 'min:0'],
            'incursao'              => ['nullable', 'integer', 'min:0'],
            'negociacao'            => ['nullable', 'integer', 'min:0'],
            'blitz'                 => ['nullable', 'integer', 'min:0'],
            'escolta'               => ['nullable', 'integer', 'min:0'],
            'multas'                => ['nullable', 'integer', 'min:0'],
            'bopm'                  => ['nullable', 'integer', 'min:0'],
            'viaturas_fiscalizadas' => ['nullable', 'integer', 'min:0'],

            'bopm_registros'   => ['nullable', 'array'],
            'bopm_registros.*' => ['nullable', 'string', 'max:255'],

            'observacoes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->normalizeInts($data, $ints);
        $this->validateBopmRegistros($data);

        $registrosLog = $data['bopm_registros'] ?? [];
        $data['bopm_registros'] = json_encode($registrosLog, JSON_UNESCAPED_UNICODE);

        $relatorio->update($data);

        AuditoriaLogger::log(
            'relatorio_rascunho_salvo',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'status' => $relatorio->status,
                'bopm' => $relatorio->bopm,
                'bopm_registros' => $registrosLog,
            ],
            $request
        );

        return response()->json([
            'ok' => true,
            'saved_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * ENCERRAR TURNO:
     * - dono do relatório pode encerrar o próprio turno
     * - nível 7+ pode encerrar qualquer unidade em patrulha
     */
    public function encerrarTurno(Request $request, Relatorio $relatorio)
    {
        if (!auth()->check()) abort(403);

        $nivel = (int)(auth()->user()->nivel ?? 0);
        $isOwner = (int)$relatorio->user_id === (int)auth()->id();
        $canForceClose = $nivel >= 7;

        if (!$isOwner && !$canForceClose) {
            abort(403, 'Você não tem permissão para encerrar este relatório.');
        }

        if (($relatorio->status ?? '') !== 'em_patrulha') {
            return back()->with('error', 'Este relatório não está como "em patrulha".');
        }

        if (!empty($relatorio->final_patrulhamento)) {
            return back()->with('error', 'Este relatório já foi encerrado/finalizado.');
        }

        $registros = $this->normalizeBopmRegistros($relatorio->bopm_registros ?? []);
        $bopmQtd = (int)($relatorio->bopm ?? 0);

        if ($bopmQtd > 0 && count($registros) !== $bopmQtd) {
            return back()->with('error', "Você informou {$bopmQtd} BOPM, então precisa preencher exatamente {$bopmQtd} registro(s) antes de encerrar.");
        }

        $final = now()->format('H:i');

        $relatorio->update([
            'final_patrulhamento' => $final,
            'status'              => 'pendente',
            'aprovado_por'        => null,
            'reprovado_por'       => null,
            'decisao_obs'         => null,
        ]);

        if (method_exists($relatorio, 'syncParticipantesPorRG')) {
            $relatorio->syncParticipantesPorRG();
        }

        AuditoriaLogger::log(
            $isOwner ? 'relatorio_turno_encerrado' : 'relatorio_turno_encerrado_admin',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'final_patrulhamento' => $final,
                'unidade' => $relatorio->unidade,
                'qra_chefe' => $relatorio->qra_chefe,
                'encerrado_por_owner' => $isOwner,
                'encerrado_por_nivel7' => !$isOwner && $canForceClose,
                'bopm' => $relatorio->bopm,
                'bopm_registros' => $registros,
            ],
            $request
        );

        $discordApp = new DiscordService();
        $data = now()->format('d/m/Y');
        $final = now()->format('H:i');

        $discordChefe = User::query()
            ->where('rg', $relatorio->qra_chefe)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordMotorista = User::query()
            ->where('rg', $relatorio->motorista)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordTerceiro = User::query()
            ->where('rg', $relatorio->terceiro)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordQuarto = User::query()
            ->where('rg', $relatorio->quarto)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordQuinto = User::query()
            ->where('rg', $relatorio->quinto)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $adm = ($discordChefe && $discordMotorista && $discordChefe->rg == $discordMotorista->rg) ? "**Chefe e Motorista:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
        $chefe = ($discordChefe && $discordChefe->rg != $discordMotorista->rg) ? "**Chefe da Unidade:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
        $motorista = ($discordMotorista && $discordMotorista->rg != $discordChefe->rg) ? "**Motorista:** {$discordMotorista->cargo} {$discordMotorista->name} #{$discordMotorista->rg}" : null;
        $terceiro = $discordTerceiro ? "**Terceiro:** {$discordTerceiro->cargo} {$discordTerceiro->name} #{$discordTerceiro->rg}" : null;
        $quarto = $discordQuarto ? "**Quarto:** {$discordQuarto->cargo} {$discordQuarto->name} #{$discordQuarto->rg}" : null;
        $quinto = $discordQuinto ? "**Quinto:** {$discordQuinto->cargo} {$discordQuinto->name} #{$discordQuinto->rg}" : null;

        $linhas = array_filter([
            $adm,
            $chefe,
            $motorista,
            $terceiro,
            $quarto,
            $quinto,
        ]);

        $equipe = implode("\n", $linhas);

        $msg = "🚨 **Turno Finalizado:**

                **Unidade:** {$relatorio->unidade}
                **Data:** {$data}
                **Início:** {$relatorio->inicio_patrulhamento}
                **Final:** {$final}
                **Status:** Finalizado, pendente de aprovação

                {$equipe}";

        if ($discordChefe && $discordChefe->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordChefe->discord,
                    'color' => 'Yellow',
                    'user' => [
                        'rg' => $discordChefe->rg,
                        'nome' => $discordChefe->name,
                        'cargo' => $discordChefe->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordMotorista && $discordMotorista->discord && $discordChefe->rg !== $discordMotorista->rg) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordMotorista->discord,
                    'color' => 'Yellow',
                    'user' => [
                        'rg' => $discordMotorista->rg,
                        'nome' => $discordMotorista->name,
                        'cargo' => $discordMotorista->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordTerceiro && $discordTerceiro->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordTerceiro->discord,
                    'color' => 'Yellow',
                    'user' => [
                        'rg' => $discordTerceiro->rg,
                        'nome' => $discordTerceiro->name,
                        'cargo' => $discordTerceiro->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordQuarto && $discordQuarto->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordQuarto->discord,
                    'color' => 'Yellow',
                    'user' => [
                        'rg' => $discordQuarto->rg,
                        'nome' => $discordQuarto->name,
                        'cargo' => $discordQuarto->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordQuinto && $discordQuinto->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordQuinto->discord,
                    'color' => 'Yellow',
                    'user' => [
                        'rg' => $discordQuinto->rg,
                        'nome' => $discordQuinto->name,
                        'cargo' => $discordQuinto->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }

        return redirect()
            ->route('relatorios.index')
            ->with(
                'success',
                $isOwner
                    ? 'Turno encerrado e enviado para validação (PENDENTE).'
                    : 'Patrulhamento da unidade encerrado com sucesso e enviado para validação (PENDENTE).'
            );
    }

    // =========================
    // LEGADO (mantido por enquanto)
    // =========================

    public function create(Request $request)
    {
        if ($request->get('novo') == 1) {
            $request->session()->flash('clear_relatorio_draft', true);
            $request->session()->forget('relatorio_inicio');
        }

        if (!$request->session()->has('relatorio_inicio')) {
            $request->session()->put('relatorio_inicio', now()->format('H:i'));
        }

        $inicio_registrado = $request->session()->get('relatorio_inicio');

        return view('relatorios.create', compact('inicio_registrado'));
    }

    public function store(Request $request)
    {
        $this->validateRgs($request, true);

        $ints = [
            'pistolas',
            'smg_fuzil',
            'municoes',
            'drogas',
            'explosivos',
            'lockpicks',
            'dinheiro',
            'abordagens',
            'apoio',
            'incursao',
            'negociacao',
            'blitz',
            'escolta',
            'multas',
            'bopm',
            'viaturas_fiscalizadas',
        ];

        $data = $request->validate([
            'qra_chefe' => ['required', 'string', 'max:255'],
            'unidade'   => ['required', 'string', 'max:255'],
            'motorista' => ['required', 'exists:users,rg'],
            'terceiro'  => ['nullable', 'exists:users,rg'],
            'quarto'    => ['nullable', 'exists:users,rg'],
            'quinto'    => ['nullable', 'exists:users,rg'],
            'inicio_patrulhamento' => ['required', 'date_format:H:i'],

            'pistolas'   => ['nullable', 'integer', 'min:0'],
            'smg_fuzil'  => ['nullable', 'integer', 'min:0'],
            'municoes'   => ['nullable', 'integer', 'min:0'],
            'drogas'     => ['nullable', 'integer', 'min:0'],
            'explosivos' => ['nullable', 'integer', 'min:0'],
            'lockpicks'  => ['nullable', 'integer', 'min:0'],
            'dinheiro'   => ['nullable', 'integer', 'min:0'],

            'abordagens'            => ['nullable', 'integer', 'min:0'],
            'apoio'                 => ['nullable', 'integer', 'min:0'],
            'incursao'              => ['nullable', 'integer', 'min:0'],
            'negociacao'            => ['nullable', 'integer', 'min:0'],
            'blitz'                 => ['nullable', 'integer', 'min:0'],
            'escolta'               => ['nullable', 'integer', 'min:0'],
            'multas'                => ['nullable', 'integer', 'min:0'],
            'bopm'                  => ['nullable', 'integer', 'min:0'],
            'viaturas_fiscalizadas' => ['nullable', 'integer', 'min:0'],

            'bopm_registros'   => ['nullable', 'array'],
            'bopm_registros.*' => ['nullable', 'string', 'max:255'],

            'observacoes' => ['nullable', 'string', 'max:5000'],
            'client_token' => ['nullable', 'string', 'max:64'],
            'redirect_to'  => ['nullable', 'string', 'max:2048'],
        ]);

        $this->normalizeInts($data, $ints);
        $this->validateBopmRegistros($data);

        $registrosLog = $data['bopm_registros'] ?? [];

        $clientToken = (string) $request->input('client_token', '');
        if ($clientToken === '') {
            $clientToken = bin2hex(random_bytes(16));
        }

        $data['client_token'] = $clientToken;
        $data['bopm_registros'] = json_encode($registrosLog, JSON_UNESCAPED_UNICODE);

        $data['data_patrulhamento']  = now()->toDateString();
        $data['final_patrulhamento'] = now()->format('H:i');

        $data['user_id'] = auth()->id();
        $data['status']  = 'pendente';

        $relatorioId = null;

        DB::transaction(function () use ($data, $request, &$relatorioId, $clientToken) {

            $relatorio = Relatorio::where('client_token', $clientToken)->first();

            if (!$relatorio) {
                $relatorio = Relatorio::create($data);

                if (method_exists($relatorio, 'syncParticipantesPorRG')) {
                    $relatorio->syncParticipantesPorRG();
                }
            }

            $relatorioId = $relatorio->id;
            $request->session()->forget('relatorio_inicio');
        });

        AuditoriaLogger::log(
            'relatorio_criado_e_finalizado',
            auth()->id(),
            'Relatorio',
            $relatorioId,
            [
                'unidade'        => $data['unidade'] ?? null,
                'qra_chefe'      => $data['qra_chefe'] ?? null,
                'motorista'      => $data['motorista'] ?? null,
                'p3'             => $data['terceiro'] ?? null,
                'p4'             => $data['quarto'] ?? null,
                'p5'             => $data['quinto'] ?? null,
                'status_set'     => 'pendente',
                'client_token'   => $clientToken,
                'redirect_to'    => $request->input('redirect_to'),
                'bopm'           => $data['bopm'] ?? null,
                'bopm_registros' => $registrosLog,
            ],
            $request
        );

        $redirectTo = (string) $request->input('redirect_to', '');

        if ($redirectTo !== '' && str_starts_with($redirectTo, url('/'))) {
            return redirect()
                ->to($redirectTo)
                ->with('success', 'Relatório enviado com sucesso!')
                ->with('clear_relatorio_draft', true);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Relatório enviado com sucesso!')
            ->with('clear_relatorio_draft', true);
    }

    public function show(Request $request, Relatorio $relatorio)
    {
        $rgs = collect([
            $relatorio->qra_chefe,
            $relatorio->motorista,
            $relatorio->terceiro,
            $relatorio->quarto,
            $relatorio->quinto,
        ])
            ->filter()
            ->map(fn($rg) => preg_replace('/\D+/', '', (string) $rg))
            ->filter(fn($rg) => $rg !== '')
            ->unique()
            ->values()
            ->all();

        $usersByRg = User::query()
            ->whereIn('rg', $rgs)
            ->get(['id', 'name', 'cargo', 'rg', 'ativo', 'status'])
            ->keyBy('rg');

        $ids = collect([(int)($relatorio->aprovado_por ?? 0), (int)($relatorio->reprovado_por ?? 0)])
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $usersById = User::query()
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'cargo', 'rg', 'ativo', 'status'])
            ->keyBy('id');

        $back = (string) $request->query('back', '');

        return view('relatorios.show', compact('relatorio', 'usersByRg', 'usersById', 'back'));
    }

    public function finalizar(Request $request, Relatorio $relatorio)
    {
        if ($relatorio->final_patrulhamento) {
            return back()->with('error', 'Este relatório já foi finalizado.');
        }

        $final = $request->input('final_patrulhamento') ?? now()->format('H:i');
        $relatorio->update(['final_patrulhamento' => $final]);

        AuditoriaLogger::log(
            'relatorio_finalizado',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'final_patrulhamento' => $final,
                'unidade' => $relatorio->unidade,
                'qra_chefe' => $relatorio->qra_chefe,
            ],
            $request
        );

        return back()->with('success', 'Relatório finalizado.');
    }

    public function aprovar(Request $request, Relatorio $relatorio)
    {
        Gate::authorize('decide', $relatorio);

        $request->validate([
            'observacao' => ['required', 'string', 'min:8', 'max:400'],
        ]);

        if (!$relatorio->final_patrulhamento || $relatorio->status !== 'pendente') {
            return back()->with('error', 'Relatório inválido para aprovação.');
        }

        $relatorio->update([
            'status'        => 'aprovado',
            'aprovado_por'  => auth()->id(),
            'reprovado_por' => null,
            'decisao_obs'   => $request->observacao,
        ]);

        AuditoriaLogger::log(
            'relatorio_aprovado',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'decisao_obs' => $request->observacao,
                'unidade'     => $relatorio->unidade,
                'qra_chefe'   => $relatorio->qra_chefe,
            ],
            $request
        );

        $discordApp = new DiscordService();
        $data = now()->parse($relatorio->data_patrulhamento)->format('d/m/Y');

        $discordAprovador = User::query()
            ->where('id', auth()->id())
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordChefe = User::query()
            ->where('rg', $relatorio->qra_chefe)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordMotorista = User::query()
            ->where('rg', $relatorio->motorista)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordTerceiro = User::query()
            ->where('rg', $relatorio->terceiro)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordQuarto = User::query()
            ->where('rg', $relatorio->quarto)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordQuinto = User::query()
            ->where('rg', $relatorio->quinto)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $adm = ($discordChefe && $discordMotorista && $discordChefe->rg == $discordMotorista->rg) ? "**Chefe e Motorista:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
        $chefe = ($discordChefe && $discordChefe->rg != $discordMotorista->rg) ? "**Chefe da Unidade:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
        $motorista = ($discordMotorista && $discordMotorista->rg != $discordChefe->rg) ? "**Motorista:** {$discordMotorista->cargo} {$discordMotorista->name} #{$discordMotorista->rg}" : null;
        $terceiro = $discordTerceiro ? "**Terceiro:** {$discordTerceiro->cargo} {$discordTerceiro->name} #{$discordTerceiro->rg}" : null;
        $quarto = $discordQuarto ? "**Quarto:** {$discordQuarto->cargo} {$discordQuarto->name} #{$discordQuarto->rg}" : null;
        $quinto = $discordQuinto ? "**Quinto:** {$discordQuinto->cargo} {$discordQuinto->name} #{$discordQuinto->rg}" : null;

        $linhas = array_filter([
            $adm,
            $chefe,
            $motorista,
            $terceiro,
            $quarto,
            $quinto,
        ]);

        $equipe = implode("\n", $linhas);

        $msg = "🚨 **Turno aprovado:**

                    **Unidade:** {$relatorio->unidade}
                    **Data:** {$data}
                    **Início:** {$relatorio->inicio_patrulhamento}
                    **Final:** {$relatorio->final_patrulhamento}
                    **Status:** Finalizado e aprovado

                    {$equipe}
                    
                    **Aprovado por:** {$discordAprovador->cargo} {$discordAprovador->name} #{$discordAprovador->rg}
                    **Observação:** {$request->observacao}";

        if ($discordChefe && $discordChefe->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordChefe->discord,
                    'color' => 'Green',
                    'user' => [
                        'rg' => $discordChefe->rg,
                        'nome' => $discordChefe->name,
                        'cargo' => $discordChefe->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordMotorista && $discordMotorista->discord && $discordChefe->rg !== $discordMotorista->rg) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordMotorista->discord,
                    'color' => 'Green',
                    'user' => [
                        'rg' => $discordMotorista->rg,
                        'nome' => $discordMotorista->name,
                        'cargo' => $discordMotorista->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordTerceiro && $discordTerceiro->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordTerceiro->discord,
                    'color' => 'Green',
                    'user' => [
                        'rg' => $discordTerceiro->rg,
                        'nome' => $discordTerceiro->name,
                        'cargo' => $discordTerceiro->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordQuarto && $discordQuarto->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordQuarto->discord,
                    'color' => 'Green',
                    'user' => [
                        'rg' => $discordQuarto->rg,
                        'nome' => $discordQuarto->name,
                        'cargo' => $discordQuarto->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordQuinto && $discordQuinto->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordQuinto->discord,
                    'color' => 'Green',
                    'user' => [
                        'rg' => $discordQuinto->rg,
                        'nome' => $discordQuinto->name,
                        'cargo' => $discordQuinto->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }

        return back()->with('success', 'Relatório aprovado.');
    }

    public function reprovar(Request $request, Relatorio $relatorio)
    {
        Gate::authorize('decide', $relatorio);

        $request->validate([
            'observacao' => ['required', 'string', 'min:8', 'max:400'],
        ]);

        if (!$relatorio->final_patrulhamento || $relatorio->status !== 'pendente') {
            return back()->with('error', 'Relatório inválido para reprovação.');
        }

        $relatorio->update([
            'status'        => 'reprovado',
            'reprovado_por' => auth()->id(),
            'aprovado_por'  => null,
            'decisao_obs'   => $request->observacao,
        ]);

        AuditoriaLogger::log(
            'relatorio_reprovado',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'decisao_obs' => $request->observacao,
                'unidade'     => $relatorio->unidade,
                'qra_chefe'   => $relatorio->qra_chefe,
            ],
            $request
        );

        $discordApp = new DiscordService();
        $data = now()->parse($relatorio->data_patrulhamento)->format('d/m/Y');

        $discordAprovador = User::query()
            ->where('id', auth()->id())
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordChefe = User::query()
            ->where('rg', $relatorio->qra_chefe)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordMotorista = User::query()
            ->where('rg', $relatorio->motorista)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordTerceiro = User::query()
            ->where('rg', $relatorio->terceiro)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordQuarto = User::query()
            ->where('rg', $relatorio->quarto)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $discordQuinto = User::query()
            ->where('rg', $relatorio->quinto)
            ->first(['rg', 'name', 'cargo', 'discord']);

        $adm = ($discordChefe && $discordMotorista && $discordChefe->rg == $discordMotorista->rg) ? "**Chefe e Motorista:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
        $chefe = ($discordChefe && $discordChefe->rg != $discordMotorista->rg) ? "**Chefe da Unidade:** {$discordChefe->cargo} {$discordChefe->name} #{$discordChefe->rg}" : null;
        $motorista = ($discordMotorista && $discordMotorista->rg != $discordChefe->rg) ? "**Motorista:** {$discordMotorista->cargo} {$discordMotorista->name} #{$discordMotorista->rg}" : null;
        $terceiro = $discordTerceiro ? "**Terceiro:** {$discordTerceiro->cargo} {$discordTerceiro->name} #{$discordTerceiro->rg}" : null;
        $quarto = $discordQuarto ? "**Quarto:** {$discordQuarto->cargo} {$discordQuarto->name} #{$discordQuarto->rg}" : null;
        $quinto = $discordQuinto ? "**Quinto:** {$discordQuinto->cargo} {$discordQuinto->name} #{$discordQuinto->rg}" : null;

        $linhas = array_filter([
            $adm,
            $chefe,
            $motorista,
            $terceiro,
            $quarto,
            $quinto,
        ]);

        $equipe = implode("\n", $linhas);

        $msg = "🚨 **Turno reprovado:**

                    **Unidade:** {$relatorio->unidade}
                    **Data:** {$data}
                    **Início:** {$relatorio->inicio_patrulhamento}
                    **Final:** {$relatorio->final_patrulhamento}
                    **Status:** Finalizado e reprovado

                    {$equipe}
                    
                    **Reprovado por:** {$discordAprovador->cargo} {$discordAprovador->name} #{$discordAprovador->rg}
                    **Observação:** {$request->observacao}";

        if ($discordChefe && $discordChefe->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordChefe->discord,
                    'color' => 'Red',
                    'user' => [
                        'rg' => $discordChefe->rg,
                        'nome' => $discordChefe->name,
                        'cargo' => $discordChefe->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordMotorista && $discordMotorista->discord && $discordChefe->rg !== $discordMotorista->rg) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordMotorista->discord,
                    'color' => 'Red',
                    'user' => [
                        'rg' => $discordMotorista->rg,
                        'nome' => $discordMotorista->name,
                        'cargo' => $discordMotorista->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordTerceiro && $discordTerceiro->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordTerceiro->discord,
                    'color' => 'Red',
                    'user' => [
                        'rg' => $discordTerceiro->rg,
                        'nome' => $discordTerceiro->name,
                        'cargo' => $discordTerceiro->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordQuarto && $discordQuarto->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordQuarto->discord,
                    'color' => 'Red',
                    'user' => [
                        'rg' => $discordQuarto->rg,
                        'nome' => $discordQuarto->name,
                        'cargo' => $discordQuarto->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }
        if ($discordQuinto && $discordQuinto->discord) {
            $discordApp->enviar(
                '/send-dm',
                [
                    'discord' => $discordQuinto->discord,
                    'color' => 'Red',
                    'user' => [
                        'rg' => $discordQuinto->rg,
                        'nome' => $discordQuinto->name,
                        'cargo' => $discordQuinto->cargo,
                    ],
                    'message' => $msg,
                ]
            );
        }

        return back()->with('success', 'Relatório reprovado.');
    }

    public function edit(Request $request, Relatorio $relatorio)
    {
        if (!auth()->check() || (int) auth()->user()->nivel < 6) {
            abort(403, 'Acesso não autorizado.');
        }

        Gate::authorize('decide', $relatorio);

        if (($relatorio->status ?? 'pendente') === 'pendente') {
            return redirect()
                ->route('relatorios.index')
                ->with('error', 'Não é permitido editar relatório pendente. Finalize e decida pelo fluxo padrão.');
        }

        if (empty($relatorio->final_patrulhamento)) {
            return redirect()
                ->route('relatorios.index')
                ->with('error', 'Não é permitido decidir relatório em andamento.');
        }

        AuditoriaLogger::log(
            'relatorio_decisao_aberta',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'status_atual' => $relatorio->status,
                'unidade'      => $relatorio->unidade,
                'qra_chefe'    => $relatorio->qra_chefe,
            ],
            $request
        );

        return view('relatorios.edit', compact('relatorio'));
    }

    public function update(Request $request, Relatorio $relatorio)
    {
        if (!auth()->check() || (int) auth()->user()->nivel < 6) {
            abort(403, 'Acesso não autorizado.');
        }

        Gate::authorize('decide', $relatorio);

        if (($relatorio->status ?? 'pendente') === 'pendente') {
            return redirect()
                ->route('relatorios.index')
                ->with('error', 'Não é permitido decidir/editar relatório pendente.');
        }

        if (empty($relatorio->final_patrulhamento)) {
            return redirect()
                ->route('relatorios.index')
                ->with('error', 'Não é permitido decidir relatório em andamento.');
        }

        $oldStatus = (string) ($relatorio->status ?? 'pendente');

        $data = $request->validate([
            'status'     => ['required', Rule::in(['aprovado', 'reprovado'])],
            'observacao' => ['required', 'string', 'min:8', 'max:400'],
        ]);

        if ($data['status'] === 'aprovado') {
            $relatorio->update([
                'status'        => 'aprovado',
                'aprovado_por'  => auth()->id(),
                'reprovado_por' => null,
                'decisao_obs'   => $data['observacao'],
            ]);
        } else {
            $relatorio->update([
                'status'        => 'reprovado',
                'reprovado_por' => auth()->id(),
                'aprovado_por'  => null,
                'decisao_obs'   => $data['observacao'],
            ]);
        }

        AuditoriaLogger::log(
            ($data['status'] === 'aprovado') ? 'relatorio_aprovado' : 'relatorio_reprovado',
            auth()->id(),
            'Relatorio',
            $relatorio->id,
            [
                'status_antes'  => $oldStatus,
                'status_depois' => $data['status'],
                'decisao_obs'   => $data['observacao'],
                'unidade'       => $relatorio->unidade,
                'qra_chefe'     => $relatorio->qra_chefe,
                'via'           => 'edit_update',
            ],
            $request
        );

        return redirect()
            ->route('relatorios.index')
            ->with('success', 'Decisão atualizada com sucesso!');
    }

    public function buscarUsuarioPorRg(Request $request)
    {
        $rg = preg_replace('/\D+/', '', (string) $request->query('rg', ''));

        if ($rg === '' || strlen($rg) < 3) {
            return response()->json(['ok' => false, 'found' => false]);
        }

        $user = User::where('rg', $rg)->first(['id', 'name', 'cargo', 'rg', 'ativo', 'status']);

        if (!$user) {
            return response()->json([
                'ok' => true,
                'found' => false,
                'blocked' => false,
            ]);
        }

        $check = $this->getUsuarioPatrulhaStatus($rg);

        return response()->json([
            'ok'      => true,
            'found'   => true,
            'blocked' => !$check['ok'],
            'reason'  => $check['reason'],
            'name'    => (string) $user->name,
            'cargo'   => (string) ($user->cargo ?? ''),
            'rg'      => (string) ($user->rg ?? ''),
            'ativo'   => (bool) ($user->ativo ?? false),
            'status'  => (string) ($user->status ?? ''),
        ]);
    }
}
