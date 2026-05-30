<?php

namespace App\Http\Controllers;

use App\Models\RhHierarquiaRecord;
use App\Models\RhPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RhController extends Controller
{
    public function index()
    {
        return view('rh.index');
    }

    public function hierarquia()
    {
        return redirect()->route('rh.hierarquia');
    }

    public function controleSaida()
    {
        return redirect()->route('rh.controle_saida');
    }

    /**
     * ============================================================
     * ✅ TRAVA REAL DE PERMISSÕES (RH)
     * - Nível 9+ sempre pode
     * - Demais níveis: precisa estar marcado no painel RH Permissões
     * ============================================================
     */
    public static function canEditSection(string $section): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $nivel = (int) ($user->nivel ?? 0);
        if ($nivel >= 9) return true;

        $field = RhPermission::fieldFor($section);
        if (!$field) return false;

        $perm = RhPermission::where('user_id', $user->id)->first();
        return (bool) ($perm?->{$field} ?? false);
    }

    public static function requireEditSection(string $section): void
    {
        abort_unless(self::canEditSection($section), 403, 'Sem permissão para editar esta seção do RH.');
    }

    /**
     * ✅ Helper pra UI (mostrar botão "Gerenciar permissões" só nível 9+)
     */
    public static function canManagePermissions(): bool
    {
        $nivel = (int) (auth()->user()->nivel ?? 0);
        return $nivel >= 9;
    }

    /**
     * ============================================================
     * HELPERS INTERNOS
     * ============================================================
     */
    private function normalizeDoc(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);
        return $digits !== '' ? $digits : null;
    }

    private function normalizeName(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $value = Str::of($value)->lower()->ascii()->__toString();
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value) ?: null;
    }

    private function hhmmFromMinutes(int $minutes): string
    {
        $minutes = max(0, $minutes);
        $hh = floor($minutes / 60);
        $mm = $minutes % 60;

        return str_pad((string) $hh, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $mm, 2, '0', STR_PAD_LEFT);
    }

    private function tableHas(string $table, array $columns = []): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $col) {
            if (!Schema::hasColumn($table, $col)) {
                return false;
            }
        }

        return true;
    }

    private function calcMinutesSQLiteSafe($data, $inicio, $fim): int
    {
        try {
            if (empty($data) || empty($inicio) || empty($fim)) return 0;

            $date = $data instanceof Carbon ? $data->toDateString() : (string) $data;

            $start = Carbon::parse($date . ' ' . (string) $inicio);
            $end   = Carbon::parse($date . ' ' . (string) $fim);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            return max(0, (int) $start->diffInMinutes($end));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * ============================================================
     * ✅ LÊ DADOS CONSOLIDADOS DE RANKING + HORAS
     * Compatível com bancos que podem não ter users.cpf
     * ============================================================
     */
    private function buildPerformanceIndexes(): array
    {
        $indexes = [
            'by_user_id' => [],
            'by_rg'      => [],
            'by_cpf'     => [],
            'by_name'    => [],
        ];

        if (
            !$this->tableHas('relatorios', ['id', 'status']) ||
            !$this->tableHas('relatorio_participantes', ['relatorio_id', 'user_id']) ||
            !$this->tableHas('users', ['id'])
        ) {
            return $indexes;
        }

        $hasUserRg   = Schema::hasColumn('users', 'rg');
        $hasUserCpf  = Schema::hasColumn('users', 'cpf');
        $hasUserName = Schema::hasColumn('users', 'name');

        $hasRpPapel = Schema::hasColumn('relatorio_participantes', 'papel');

        $requiredRelatorioCols = [
            'data_patrulhamento',
            'drogas',
            'pistolas',
            'smg_fuzil',
            'municoes',
            'dinheiro',
            'explosivos',
            'lockpicks',
            'abordagens',
            'multas',
            'bopm',
            'viaturas_fiscalizadas',
        ];

        foreach ($requiredRelatorioCols as $col) {
            if (!Schema::hasColumn('relatorios', $col)) {
                return $indexes;
            }
        }

        $colInicio = (string) config('grr.patrulha.inicio', 'inicio_patrulhamento');
        $colFim    = (string) config('grr.patrulha.fim', 'final_patrulhamento');

        if (!Schema::hasColumn('relatorios', $colInicio) || !Schema::hasColumn('relatorios', $colFim)) {
            return $indexes;
        }

        $xp = array_merge([
            'drogas' => 0,
            'pistolas' => 0,
            'smg_fuzil' => 0,
            'municoes' => 0,
            'dinheiro' => 0,
            'explosivos' => 0,
            'lockpicks' => 0,
            'abordagens' => 0,
            'multas' => 0,
            'bopm' => 0,
            'viaturas_fiscalizadas' => 0,
            'relatorio_aprovado' => 0,
        ], (array) config('grr.xp', []));

        $mult = array_merge([
            'P1' => 1.20,
            'P2' => 1.30,
            'P3' => 1.00,
            'P4' => 1.00,
            'P5' => 1.00,
        ], (array) config('grr.xp_multipliers', []));

        foreach (['P1', 'P2', 'P3', 'P4', 'P5'] as $p) {
            if (!isset($mult[$p]) || !is_numeric($mult[$p])) {
                $mult[$p] = 1.0;
            }
            $mult[$p] = max(0, (float) $mult[$p]);
        }

        /**
         * ============================================================
         * XP OFICIAL
         * ============================================================
         */
        $relatoriosBase = DB::table('relatorios as r')
            ->where('r.status', '=', 'aprovado');

        $base = DB::query()
            ->fromSub($relatoriosBase->select('r.*'), 'r')
            ->join('relatorio_participantes as rp', 'rp.relatorio_id', '=', 'r.id');

        if ($hasRpPapel) {
            $baseDistinct = DB::query()
                ->fromSub(
                    $base->selectRaw('DISTINCT rp.user_id, rp.papel, r.id as relatorio_id'),
                    'x'
                )
                ->join('relatorios as r', 'r.id', '=', 'x.relatorio_id');

            $byRole = DB::query()->fromSub(
                $baseDistinct->selectRaw("
                    x.user_id as user_id,
                    x.papel as papel,

                    COUNT(DISTINCT r.id) as relatorios,

                    COALESCE(SUM(r.drogas),0) as drogas,
                    COALESCE(SUM(r.pistolas),0) as pistolas,
                    COALESCE(SUM(r.smg_fuzil),0) as smg_fuzil,
                    COALESCE(SUM(r.municoes),0) as municoes,
                    COALESCE(SUM(r.dinheiro),0) as dinheiro,
                    COALESCE(SUM(r.explosivos),0) as explosivos,
                    COALESCE(SUM(r.lockpicks),0) as lockpicks,

                    COALESCE(SUM(r.abordagens),0) as abordagens,
                    COALESCE(SUM(r.multas),0) as multas,
                    COALESCE(SUM(r.bopm),0) as bopm,
                    COALESCE(SUM(r.viaturas_fiscalizadas),0) as viaturas_fiscalizadas
                ")->groupBy('x.user_id', 'x.papel'),
                't'
            );

            $dinheiroExpr = "CAST((t.dinheiro / 1000) AS INTEGER) * " . (int) $xp['dinheiro'];

            $xpBaseExpr = "
                (
                    t.drogas * " . (int) $xp['drogas'] . " +
                    t.pistolas * " . (int) $xp['pistolas'] . " +
                    t.smg_fuzil * " . (int) $xp['smg_fuzil'] . " +
                    t.municoes * " . (int) $xp['municoes'] . " +
                    {$dinheiroExpr} +
                    t.explosivos * " . (int) $xp['explosivos'] . " +
                    t.lockpicks * " . (int) $xp['lockpicks'] . " +
                    t.abordagens * " . (int) $xp['abordagens'] . " +
                    t.multas * " . (int) $xp['multas'] . " +
                    t.bopm * " . (int) $xp['bopm'] . " +
                    t.viaturas_fiscalizadas * " . (int) $xp['viaturas_fiscalizadas'] . " +
                    t.relatorios * " . (int) $xp['relatorio_aprovado'] . "
                )
            ";

            $xpMultExpr = "
                CASE t.papel
                    WHEN 'P1' THEN {$mult['P1']}
                    WHEN 'P2' THEN {$mult['P2']}
                    WHEN 'P3' THEN {$mult['P3']}
                    WHEN 'P4' THEN {$mult['P4']}
                    WHEN 'P5' THEN {$mult['P5']}
                    ELSE 1.0
                END
            ";

            $xpRoleExpr = "({$xpBaseExpr} * {$xpMultExpr})";

            $sumUsers = DB::query()->fromSub(
                $byRole->selectRaw("
                    t.user_id as user_id,
                    SUM({$xpRoleExpr}) as xp,
                    SUM(t.relatorios) as relatorios
                ")->groupBy('t.user_id'),
                's'
            );
        } else {
            $baseDistinct = DB::query()
                ->fromSub(
                    $base->selectRaw('DISTINCT rp.user_id, r.id as relatorio_id'),
                    'x'
                )
                ->join('relatorios as r', 'r.id', '=', 'x.relatorio_id');

            $dinheiroExpr = "CAST((COALESCE(SUM(r.dinheiro),0) / 1000) AS INTEGER) * " . (int) $xp['dinheiro'];

            $sumUsers = DB::query()->fromSub(
                $baseDistinct->selectRaw("
                    x.user_id as user_id,
                    (
                        COALESCE(SUM(r.drogas),0) * " . (int) $xp['drogas'] . " +
                        COALESCE(SUM(r.pistolas),0) * " . (int) $xp['pistolas'] . " +
                        COALESCE(SUM(r.smg_fuzil),0) * " . (int) $xp['smg_fuzil'] . " +
                        COALESCE(SUM(r.municoes),0) * " . (int) $xp['municoes'] . " +
                        {$dinheiroExpr} +
                        COALESCE(SUM(r.explosivos),0) * " . (int) $xp['explosivos'] . " +
                        COALESCE(SUM(r.lockpicks),0) * " . (int) $xp['lockpicks'] . " +
                        COALESCE(SUM(r.abordagens),0) * " . (int) $xp['abordagens'] . " +
                        COALESCE(SUM(r.multas),0) * " . (int) $xp['multas'] . " +
                        COALESCE(SUM(r.bopm),0) * " . (int) $xp['bopm'] . " +
                        COALESCE(SUM(r.viaturas_fiscalizadas),0) * " . (int) $xp['viaturas_fiscalizadas'] . " +
                        COUNT(DISTINCT r.id) * " . (int) $xp['relatorio_aprovado'] . "
                    ) as xp,
                    COUNT(DISTINCT r.id) as relatorios
                ")->groupBy('x.user_id'),
                's'
            );
        }

        $rankingSelect = [
            'users.id as user_id',
            DB::raw($hasUserRg ? 'users.rg as rg' : 'NULL as rg'),
            DB::raw($hasUserCpf ? 'users.cpf as cpf' : 'NULL as cpf'),
            DB::raw($hasUserName ? 'users.name as nome' : 'NULL as nome'),
            's.xp as xp',
            's.relatorios as relatorios',
        ];

        $rankingRows = DB::query()
            ->fromSub($sumUsers, 's')
            ->join('users', 'users.id', '=', 's.user_id')
            ->select($rankingSelect)
            ->get();

        foreach ($rankingRows as $row) {
            $payload = [
                'user_id'          => (int) ($row->user_id ?? 0),
                'xp'               => (int) round((float) ($row->xp ?? 0)),
                'relatorios'       => (int) ($row->relatorios ?? 0),
                'relatorios_horas' => 0,
                'total_minutos'    => 0,
                'total_horas'      => 0.0,
                'hhmm'             => '00:00',
                'rg'               => $this->normalizeDoc($row->rg ?? null),
                'cpf'              => $this->normalizeDoc($row->cpf ?? null),
                'nome'             => $this->normalizeName($row->nome ?? null),
            ];

            if ($payload['user_id'] > 0) {
                $indexes['by_user_id'][$payload['user_id']] = $payload;
            }

            if ($payload['rg']) {
                $indexes['by_rg'][$payload['rg']] = $payload;
            }

            if ($payload['cpf']) {
                $indexes['by_cpf'][$payload['cpf']] = $payload;
            }

            if ($payload['nome']) {
                $indexes['by_name'][$payload['nome']] = $payload;
            }
        }

        /**
         * ============================================================
         * HORAS OFICIAIS
         * ============================================================
         */
        $horasSelect = [
            'users.id as user_id',
            DB::raw($hasUserName ? 'users.name as name' : 'NULL as name'),
            DB::raw($hasUserRg ? 'users.rg as rg' : 'NULL as rg'),
            DB::raw($hasUserCpf ? 'users.cpf as cpf' : 'NULL as cpf'),
            'relatorios.id as relatorio_id',
            'relatorios.data_patrulhamento',
            DB::raw("relatorios.$colInicio as hora_inicio"),
            DB::raw("relatorios.$colFim as hora_fim"),
        ];

        $rowsHoras = DB::table('relatorios')
            ->where('relatorios.status', '=', 'aprovado')
            ->join('relatorio_participantes', 'relatorio_participantes.relatorio_id', '=', 'relatorios.id')
            ->join('users', 'users.id', '=', 'relatorio_participantes.user_id')
            ->select($horasSelect)
            ->get();

        $acc = [];

        foreach ($rowsHoras as $r) {
            $uid = (int) ($r->user_id ?? 0);
            if ($uid <= 0) continue;

            if (!isset($acc[$uid])) {
                $acc[$uid] = [
                    'user_id' => $uid,
                    'nome' => $r->name ?? null,
                    'rg' => $r->rg ?? null,
                    'cpf' => $r->cpf ?? null,
                    'minutes' => 0,
                    'relatorio_ids' => [],
                ];
            }

            $rid = (int) ($r->relatorio_id ?? 0);
            if ($rid > 0) {
                $acc[$uid]['relatorio_ids'][$rid] = true;
            }

            $mins = $this->calcMinutesSQLiteSafe(
                $r->data_patrulhamento ?? null,
                $r->hora_inicio ?? null,
                $r->hora_fim ?? null
            );

            $acc[$uid]['minutes'] += $mins;
        }

        foreach ($acc as $u) {
            $uid  = (int) ($u['user_id'] ?? 0);
            $rg   = $this->normalizeDoc($u['rg'] ?? null);
            $cpf  = $this->normalizeDoc($u['cpf'] ?? null);
            $nome = $this->normalizeName($u['nome'] ?? null);

            $min  = max(0, (int) ($u['minutes'] ?? 0));
            $relH = count($u['relatorio_ids'] ?? []);

            $base = null;

            if ($uid > 0 && isset($indexes['by_user_id'][$uid])) {
                $base = $indexes['by_user_id'][$uid];
            } elseif ($rg && isset($indexes['by_rg'][$rg])) {
                $base = $indexes['by_rg'][$rg];
            } elseif ($cpf && isset($indexes['by_cpf'][$cpf])) {
                $base = $indexes['by_cpf'][$cpf];
            } elseif ($nome && isset($indexes['by_name'][$nome])) {
                $base = $indexes['by_name'][$nome];
            }

            $payload = array_merge([
                'user_id'          => $uid,
                'xp'               => 0,
                'relatorios'       => 0,
                'relatorios_horas' => 0,
                'total_minutos'    => 0,
                'total_horas'      => 0.0,
                'hhmm'             => '00:00',
                'rg'               => $rg,
                'cpf'              => $cpf,
                'nome'             => $nome,
            ], $base ?? []);

            $payload['relatorios_horas'] = $relH;
            $payload['total_minutos']    = $min;
            $payload['total_horas']      = round($min / 60, 2);
            $payload['hhmm']             = $this->hhmmFromMinutes($min);

            if ($uid > 0) {
                $indexes['by_user_id'][$uid] = $payload;
            }

            if ($rg) {
                $indexes['by_rg'][$rg] = $payload;
            }

            if ($cpf) {
                $indexes['by_cpf'][$cpf] = $payload;
            }

            if ($nome) {
                $indexes['by_name'][$nome] = $payload;
            }
        }

        return $indexes;
    }

    /**
     * ============================================================
     * ✅ ENRIQUECE REGISTROS DA HIERARQUIA COM XP + HORAS
     * Prioridade de vínculo:
     * 1. user_id
     * 2. rg
     * 3. serial
     * 4. cpf
     * 5. nome
     * ============================================================
     */
    private function enrichHierarchyRowsWithPerformance($rows)
    {
        $indexes = $this->buildPerformanceIndexes();

        return $rows->map(function ($r) use ($indexes) {
            $userId    = (int) ($r->user_id ?? 0);
            $rgKey     = $this->normalizeDoc($r->rg ?? null);
            $serialKey = $this->normalizeDoc($r->serial ?? null);
            $cpfKey    = $this->normalizeDoc($r->cpf ?? null);
            $nomeKey   = $this->normalizeName($r->nome ?? null);

            $perf = null;

            if ($userId > 0 && isset($indexes['by_user_id'][$userId])) {
                $perf = $indexes['by_user_id'][$userId];
            } elseif ($rgKey && isset($indexes['by_rg'][$rgKey])) {
                $perf = $indexes['by_rg'][$rgKey];
            } elseif ($serialKey && isset($indexes['by_rg'][$serialKey])) {
                $perf = $indexes['by_rg'][$serialKey];
            } elseif ($cpfKey && isset($indexes['by_cpf'][$cpfKey])) {
                $perf = $indexes['by_cpf'][$cpfKey];
            } elseif ($nomeKey && isset($indexes['by_name'][$nomeKey])) {
                $perf = $indexes['by_name'][$nomeKey];
            }

            $r->xp               = (int) ($perf['xp'] ?? 0);
            $r->relatorios       = (int) ($perf['relatorios'] ?? 0);
            $r->relatorios_horas = (int) ($perf['relatorios_horas'] ?? 0);
            $r->total_minutos    = (int) ($perf['total_minutos'] ?? 0);
            $r->total_horas      = (float) ($perf['total_horas'] ?? 0);
            $r->hhmm             = (string) ($perf['hhmm'] ?? '00:00');

            return $r;
        });
    }

    /**
     * ============================================================
     * ✅ MONTA OBJETO RESUMO DA EQUIPE
     * ============================================================
     */
    private function buildEquipeCard(string $nome, $membros)
    {
        $membros = $membros->sort(function ($a, $b) {
            $xpA = (int) ($a->xp ?? 0);
            $xpB = (int) ($b->xp ?? 0);

            if ($xpA !== $xpB) {
                return $xpB <=> $xpA;
            }

            $hA = (float) ($a->total_horas ?? 0);
            $hB = (float) ($b->total_horas ?? 0);

            if ($hA !== $hB) {
                return $hB <=> $hA;
            }

            $rA = (int) ($a->relatorios ?? 0);
            $rB = (int) ($b->relatorios ?? 0);

            if ($rA !== $rB) {
                return $rB <=> $rA;
            }

            $oa = (int) ($a->ordem_hierarquia ?? 999);
            $ob = (int) ($b->ordem_hierarquia ?? 999);

            if ($oa !== $ob) {
                return $oa <=> $ob;
            }

            return strcmp((string) ($a->nome ?? ''), (string) ($b->nome ?? ''));
        })->values();

        $membrosCount    = $membros->count();
        $xpTotal         = (int) $membros->sum(fn ($m) => (int) ($m->xp ?? 0));
        $relatoriosTotal = (int) $membros->sum(fn ($m) => (int) ($m->relatorios ?? 0));
        $totalMinutos    = (int) $membros->sum(fn ($m) => (int) ($m->total_minutos ?? 0));
        $horasTotal      = round($totalMinutos / 60, 2);

        return (object) [
            'slug'             => $nome,
            'nome'             => $nome,
            'membros'          => $membros,
            'membros_count'    => $membrosCount,
            'xp_total'         => $xpTotal,
            'relatorios_total' => $relatoriosTotal,
            'total_minutos'    => $totalMinutos,
            'horas_total'      => $horasTotal,
            'horas_hhmm'       => $this->hhmmFromMinutes($totalMinutos),
            'xp_medio'         => $membrosCount > 0 ? round($xpTotal / $membrosCount, 2) : 0,
            'horas_media'      => $membrosCount > 0 ? round($horasTotal / $membrosCount, 2) : 0,
        ];
    }

    /**
     * ✅ ESTATÍSTICA DO EFETIVO
     * Puxa TUDO automaticamente de rh_hierarquia_records
     */
    public function estatisticaEfetivo()
    {
        $rows = RhHierarquiaRecord::query()->get();

        $norm = function (?string $s): string {
            $s = trim((string) $s);
            if ($s === '') return '';
            $s = Str::of($s)->lower()->ascii()->__toString();
            $s = str_replace(['º', '°', 'ª'], '', $s);
            $s = str_replace(['-', '_', '.'], ' ', $s);
            $s = preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };

        $isAtivo = function ($st): bool {
            $st = (string) $st;
            return in_array($st, ['em_exercicio', 'em_licenca', 'sob_reserva'], true);
        };

        $pct = function (int $num, int $den): int {
            $den = max(1, $den);
            $v = (int) round(($num / $den) * 100);
            return max(0, min(100, $v));
        };

        $totalRegistros = $rows->count();

        $statusCounts = $rows
            ->groupBy(fn ($r) => (string) ($r->status ?? 'indefinido'))
            ->map(fn ($g) => $g->count())
            ->toArray();

        $situacao = [
            'em_ingresso' => (int) ($statusCounts['em_ingresso'] ?? 0),
            'em_exercicio' => (int) ($statusCounts['em_exercicio'] ?? 0),
            'ausente' => (int) ($statusCounts['ausente'] ?? 0),
            'em_licenca' => (int) ($statusCounts['em_licenca'] ?? 0),
            'desligado' => (int) ($statusCounts['desligado'] ?? 0),
            'estagio' => (int) ($statusCounts['estagio'] ?? 0),
            'sob_reserva' => (int) ($statusCounts['sob_reserva'] ?? 0),
        ];
        $situacao['total'] = array_sum($situacao);

        $estagio = (int) ($statusCounts['estagio'] ?? 0);

        $efetivo = (int) $rows->filter(function ($r) {
            $st = (string) ($r->status ?? '');
            return $st !== 'estagio' && $st !== 'desligado';
        })->count();

        $efetivoEstagio = [
            'efetivo' => $efetivo,
            'estagio' => $estagio,
            'total'   => $efetivo + $estagio,
        ];

        $instrutores = (int) $rows->filter(fn ($r) => (bool) ($r->instrutor ?? false))->count();

        $ativosRows = $rows->filter(fn ($r) => $isAtivo($r->status ?? null));
        $baseFormacoes = (int) $ativosRows->count();

        $countYesAtivos = function (string $key) use ($ativosRows): int {
            return (int) $ativosRows->filter(fn ($r) => (bool) ($r->{$key} ?? false))->count();
        };

        $formacoes = [
            'pop'  => $pct($countYesAtivos('pop'),  $baseFormacoes),
            'clt'  => $pct($countYesAtivos('clt'),  $baseFormacoes),
            'cap'  => $pct($countYesAtivos('cap'),  $baseFormacoes),
            'ctb'  => $pct($countYesAtivos('ctb'),  $baseFormacoes),
            'bopm' => $pct($countYesAtivos('bopm'), $baseFormacoes),
            'satb' => $pct($countYesAtivos('satb'), $baseFormacoes),
            'cta'  => $pct($countYesAtivos('cta'),  $baseFormacoes),
            'gmp'  => $pct($countYesAtivos('gmp'),  $baseFormacoes),
            'doa'  => $pct($countYesAtivos('doa'),  $baseFormacoes),
        ];
        $formacoes['media'] = max(0, min(100, (int) round(collect($formacoes)->avg())));

        $cargoOrder = [
            'aluno'               => 10,
            'agente de 3 classe'  => 20,
            'agente de 2 classe'  => 30,
            'agente de 1 classe'  => 40,
            'agente especial'     => 50,
            'inspetor'            => 60,
            'superintendente'     => 70,
            'coordenador'         => 80,
            'vice diretor'        => 90,
            'diretor'             => 100,
        ];

        $cargoKey = function (?string $cargo) use ($norm) {
            $c = $norm($cargo);
            $c = str_replace('vice-diretor', 'vice diretor', $c);

            if (str_contains($c, 'diretor') && !str_contains($c, 'vice')) return 'diretor';
            if (str_contains($c, 'vice') && str_contains($c, 'diretor')) return 'vice diretor';
            if (str_contains($c, 'coordenador')) return 'coordenador';
            if (str_contains($c, 'superintendente')) return 'superintendente';
            if (str_contains($c, 'inspetor')) return 'inspetor';
            if (str_contains($c, 'agente especial')) return 'agente especial';
            if (str_contains($c, 'agente de 1')) return 'agente de 1 classe';
            if (str_contains($c, 'agente de 2')) return 'agente de 2 classe';
            if (str_contains($c, 'agente de 3')) return 'agente de 3 classe';
            if (str_contains($c, 'aluno')) return 'aluno';

            return $c !== '' ? $c : '—';
        };

        $cargoLabel = function (string $key) {
            return match ($key) {
                'aluno' => 'Aluno',
                'agente de 3 classe' => 'Agente de 3º Classe',
                'agente de 2 classe' => 'Agente de 2º Classe',
                'agente de 1 classe' => 'Agente de 1º Classe',
                'agente especial' => 'Agente Especial',
                'inspetor' => 'Inspetor',
                'superintendente' => 'Superintendente',
                'coordenador' => 'Coordenador',
                'vice diretor' => 'Vice Diretor',
                'diretor' => 'Diretor',
                default => Str::title($key),
            };
        };

        $cargosRaw = $rows
            ->groupBy(fn ($r) => $cargoKey($r->cargo ?? null))
            ->map(fn ($g) => $g->count())
            ->toArray();

        $cargosSorted = collect($cargosRaw)
            ->mapWithKeys(fn ($qt, $k) => [$cargoLabel((string) $k) => (int) $qt])
            ->sortBy(function ($qt, $label) use ($cargoOrder, $norm) {
                $base = $norm($label);
                foreach ($cargoOrder as $needle => $ord) {
                    if (str_contains($base, $needle)) return $ord;
                }
                return 999;
            })
            ->toArray();

        $classes = [
            'estrategicos'  => 0,
            'taticos'       => 0,
            'operacionais'  => 0,
            'total'         => 0,
        ];

        foreach ($rows as $r) {
            $k = $cargoKey($r->cargo ?? null);

            if (in_array($k, ['diretor', 'vice diretor', 'coordenador', 'superintendente'], true)) {
                $classes['estrategicos']++;
            } elseif (in_array($k, ['inspetor', 'agente especial'], true)) {
                $classes['taticos']++;
            } elseif (in_array($k, ['agente de 1 classe', 'agente de 2 classe', 'agente de 3 classe'], true)) {
                $classes['operacionais']++;
            }
        }
        $classes['total'] = $classes['estrategicos'] + $classes['taticos'] + $classes['operacionais'];

        $stats = [
            'meta' => [
                'total_registros' => $totalRegistros,
                'base_formacoes'  => $baseFormacoes,
            ],
            'efetivo_estagio'    => $efetivoEstagio,
            'situacao'           => $situacao,
            'instrutores'        => $instrutores,
            'classes_funcionais' => $classes,
            'cargos'             => $cargosSorted,
            'formacoes'          => $formacoes,
        ];

        return view('rh.estatistica_efetivo', compact('stats'));
    }

    /**
     * ✅ INSTRUTORES (puxa automaticamente da Hierarquia)
     */
    public function instrutores(Request $request)
    {
        $q      = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $hasRg = Schema::hasColumn('rh_hierarquia_records', 'rg');

        $rows = RhHierarquiaRecord::query()
            ->where('instrutor', true)
            ->when($q !== '', function ($query) use ($q, $hasRg) {
                $query->where(function ($sub) use ($q, $hasRg) {
                    $sub->where('nome', 'like', "%{$q}%")
                        ->orWhere('cpf', 'like', "%{$q}%")
                        ->orWhere('serial', 'like', "%{$q}%")
                        ->orWhere('discord_id', 'like', "%{$q}%");

                    if ($hasRg) {
                        $sub->orWhere('rg', 'like', "%{$q}%");
                    }
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('nome')
            ->get();

        $total = $rows->count();

        $statusCounts = $rows
            ->groupBy(fn ($r) => (string) ($r->status ?? 'indefinido'))
            ->map(fn ($g) => $g->count())
            ->toArray();

        $ativos = (int) $rows->filter(fn ($r) => in_array((string) ($r->status ?? ''), ['em_exercicio', 'em_licenca', 'sob_reserva'], true))->count();

        $pct = fn (int $num, int $den) => max(0, min(100, (int) round(($num / max(1, $den)) * 100)));

        $ativosRows = $rows->filter(fn ($r) => in_array((string) ($r->status ?? ''), ['em_exercicio', 'em_licenca', 'sob_reserva'], true));
        $countYesAtivos = fn (string $key) => (int) $ativosRows->filter(fn ($r) => (bool) ($r->{$key} ?? false))->count();

        $form = [
            'pop'  => $pct($countYesAtivos('pop'),  $ativos),
            'clt'  => $pct($countYesAtivos('clt'),  $ativos),
            'bopm' => $pct($countYesAtivos('bopm'), $ativos),
            'ctb'  => $pct($countYesAtivos('ctb'),  $ativos),
            'cta'  => $pct($countYesAtivos('cta'),  $ativos),
            'cap'  => $pct($countYesAtivos('cap'),  $ativos),
            'satb' => $pct($countYesAtivos('satb'), $ativos),
        ];
        $form['media'] = max(0, min(100, (int) round(collect($form)->avg())));

        $rowsView = $rows->map(function ($r) {
            $r->status_label = match ($r->status) {
                'em_exercicio' => 'Em Exercício',
                'sob_reserva'  => 'Sob Reserva',
                'em_licenca'   => 'Em Licença',
                'ausente'      => 'Ausente',
                'estagio'      => 'Estágio',
                'desligado'    => 'Desligado',
                default        => $r->status ? ucfirst(str_replace('_', ' ', $r->status)) : '—',
            };
            return $r;
        });

        return view('rh.instrutores', [
            'rows'          => $rowsView,
            'q'             => $q,
            'status'        => $status,
            'total'         => $total,
            'statusCounts'  => $statusCounts,
            'ativos'        => $ativos,
            'form'          => $form,
            'statusOptions' => [
                ''             => 'Todos',
                'em_exercicio' => 'Em Exercício',
                'sob_reserva'  => 'Sob Reserva',
                'em_licenca'   => 'Em Licença',
                'ausente'      => 'Ausente',
                'estagio'      => 'Estágio',
                'desligado'    => 'Desligado',
            ],
        ]);
    }

    /**
     * ✅ EQUIPE (ALFA / BRAVO / CHARLIE + sem equipe)
     */
    public function equipe(Request $request)
    {
        $q      = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', 'em_exercicio'));

        $hasRg = Schema::hasColumn('rh_hierarquia_records', 'rg');

        $normEquipe = function (?string $s): string {
            $s = trim((string) $s);
            if ($s === '') return '';

            $u = mb_strtoupper($s);

            if (in_array($u, ['A', 'ALFA', 'ALPHA'], true)) return 'ALFA';
            if (in_array($u, ['B', 'BRAVO'], true)) return 'BRAVO';
            if (in_array($u, ['C', 'CHARLIE'], true)) return 'CHARLIE';

            return $u;
        };

        $rows = RhHierarquiaRecord::query()
            ->when($q !== '', function ($query) use ($q, $hasRg) {
                $query->where(function ($sub) use ($q, $hasRg) {
                    $sub->where('nome', 'like', "%{$q}%")
                        ->orWhere('cpf', 'like', "%{$q}%")
                        ->orWhere('cargo', 'like', "%{$q}%")
                        ->orWhere('serial', 'like', "%{$q}%")
                        ->orWhere('discord_id', 'like', "%{$q}%");

                    if ($hasRg) {
                        $sub->orWhere('rg', 'like', "%{$q}%");
                    }
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(function ($r) use ($normEquipe) {
                $r->equipe_norm = $normEquipe($r->equipe ?? null);
                return $r;
            });

        $rows = $this->enrichHierarchyRowsWithPerformance($rows);

        $alfa = $rows->filter(fn ($r) => $r->equipe_norm === 'ALFA')->values();
        $bravo = $rows->filter(fn ($r) => $r->equipe_norm === 'BRAVO')->values();
        $charlie = $rows->filter(fn ($r) => $r->equipe_norm === 'CHARLIE')->values();

        $semEquipe = $rows->filter(function ($r) {
            $e = (string) ($r->equipe_norm ?? '');
            return $e === '' || !in_array($e, ['ALFA', 'BRAVO', 'CHARLIE'], true);
        })->values();

        $statusCounts = $rows
            ->groupBy(fn ($r) => (string) ($r->status ?? 'indefinido'))
            ->map(fn ($g) => $g->count())
            ->toArray();

        $sorter = function ($a, $b) {
            $xpA = (int) ($a->xp ?? 0);
            $xpB = (int) ($b->xp ?? 0);

            if ($xpA !== $xpB) {
                return $xpB <=> $xpA;
            }

            $hA = (float) ($a->total_horas ?? 0);
            $hB = (float) ($b->total_horas ?? 0);

            if ($hA !== $hB) {
                return $hB <=> $hA;
            }

            $rA = (int) ($a->relatorios ?? 0);
            $rB = (int) ($b->relatorios ?? 0);

            if ($rA !== $rB) {
                return $rB <=> $rA;
            }

            $oa = (int) ($a->ordem_hierarquia ?? 999);
            $ob = (int) ($b->ordem_hierarquia ?? 999);

            if ($oa === $ob) {
                return strcmp((string) ($a->nome ?? ''), (string) ($b->nome ?? ''));
            }

            return $oa <=> $ob;
        };

        $alfa = $alfa->sort($sorter)->values();
        $bravo = $bravo->sort($sorter)->values();
        $charlie = $charlie->sort($sorter)->values();
        $semEquipe = $semEquipe->sort($sorter)->values();

        $equipes = collect([
            $this->buildEquipeCard('ALFA', $alfa),
            $this->buildEquipeCard('BRAVO', $bravo),
            $this->buildEquipeCard('CHARLIE', $charlie),
        ])->sort(function ($a, $b) {
            if ((int) $a->xp_total !== (int) $b->xp_total) {
                return (int) $b->xp_total <=> (int) $a->xp_total;
            }

            if ((float) $a->horas_total !== (float) $b->horas_total) {
                return (float) $b->horas_total <=> (float) $a->horas_total;
            }

            if ((int) $a->relatorios_total !== (int) $b->relatorios_total) {
                return (int) $b->relatorios_total <=> (int) $a->relatorios_total;
            }

            return (float) $b->xp_medio <=> (float) $a->xp_medio;
        })->values();

        $melhorEquipe = $equipes->first();
        $piorEquipe   = $equipes->last();

        return view('rh.equipe', [
            'q'             => $q,
            'status'        => $status,
            'statusCounts'  => $statusCounts,
            'total'         => $rows->count(),

            'alfa'          => $alfa,
            'bravo'         => $bravo,
            'charlie'       => $charlie,
            'semEquipe'     => $semEquipe,

            'equipes'       => $equipes,
            'melhorEquipe'  => $melhorEquipe,
            'piorEquipe'    => $piorEquipe,

            'statusOptions' => [
                ''             => 'Todos',
                'em_ingresso'  => 'Em ingresso',
                'em_exercicio' => 'Em Exercício',
                'sob_reserva'  => 'Sob Reserva',
                'em_licenca'   => 'Em Licença',
                'ausente'      => 'Ausente',
                'estagio'      => 'Estágio',
                'desligado'    => 'Desligado',
            ],
        ]);
    }

    /**
     * ✅ Vincula integrante a uma equipe direto pela tela de RH > Equipe
     */
    public function vincularEquipe(Request $request, $user)
    {
        $data = $request->validate([
            'equipe' => ['required', 'in:ALFA,BRAVO,CHARLIE'],
        ]);

        $registro = RhHierarquiaRecord::findOrFail($user);

        $registro->equipe = $data['equipe'];
        $registro->save();

        return redirect()
            ->route('rh.equipe')
            ->with('success', 'Equipe vinculada com sucesso.');
    }
}