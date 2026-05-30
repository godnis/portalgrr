<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        // =========================================================
        // ✅ Filtros (igual Dashboard)
        // =========================================================
        $mes = (int) $request->get('mes', now()->month);
        $ano = (int) $request->get('ano', now()->year);

        $mes = max(1, min(12, $mes));
        $ano = max((int)now()->year - 10, min((int)now()->year + 2, $ano));

        $fUnidade = trim((string) $request->get('unidade', ''));
        $fTipo    = trim((string) $request->get('tipo', ''));
        $fAgente  = trim((string) $request->get('agente', ''));

        // mantém compatibilidade com o seu sistema antigo
        $periodo = (string) $request->get('periodo', 'mes');

        // 🔎 filtro da tabela de horas (nível 6+)
        $q = trim((string) $request->get('q', ''));

        // ✅ colunas reais (configurável)
        $colInicio = (string) config('grr.patrulha.inicio', 'inicio_patrulhamento');
        $colFim    = (string) config('grr.patrulha.fim', 'final_patrulhamento');

        // =========================================================
        // ✅ Datas do período:
        // - se periodo=geral => null/null
        // - senão => usa mes/ano selecionado
        // =========================================================
        if ($periodo === 'geral') {
            $inicio = null;
            $fim = null;
        } else {
            $inicio = Carbon::create($ano, $mes, 1)->startOfDay();
            $fim    = Carbon::create($ano, $mes, 1)->endOfMonth()->endOfDay();
        }

        // =========================================================
        // XP CONFIG
        // =========================================================
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

        // =========================================================
        // MULTIPLICADORES POR PAPEL (P1/P2)
        // =========================================================
        $mult = array_merge([
            'P1' => 1.20,
            'P2' => 1.30,
            'P3' => 1.00,
            'P4' => 1.00,
            'P5' => 1.00,
        ], (array) config('grr.xp_multipliers', []));

        foreach (['P1','P2','P3','P4','P5'] as $p) {
            if (!isset($mult[$p]) || !is_numeric($mult[$p])) $mult[$p] = 1.0;
            $mult[$p] = max(0, (float) $mult[$p]);
        }

        // =========================================================
        // ✅ Descobre coluna de "tipo" (pra não quebrar)
        // =========================================================
        $tipoCol = null;
        if (Schema::hasColumn('relatorios', 'tipo_ocorrencia')) {
            $tipoCol = 'tipo_ocorrencia';
        } elseif (Schema::hasColumn('relatorios', 'tipo')) {
            $tipoCol = 'tipo';
        }

        // =========================================================
        // ✅ RANKING XP (corrigido: filtro de agente por relatório + dedupe)
        // =========================================================

        // 1) Filtra os RELATÓRIOS (r) primeiro
        $relatoriosBase = DB::table('relatorios as r')
            ->where('r.status', '=', 'aprovado');

        if ($inicio && $fim) {
            $relatoriosBase->whereBetween('r.data_patrulhamento', [
                $inicio->toDateString(),
                $fim->toDateString(),
            ]);
        }

        if ($fUnidade !== '') {
            $relatoriosBase->where('r.unidade', '=', $fUnidade);
        }

        if ($fTipo !== '' && $tipoCol) {
            $relatoriosBase->where("r.$tipoCol", '=', $fTipo);
        }

        // ✅ Agente: filtra relatórios onde esse agente participou (sem matar o ranking)
        if ($fAgente !== '') {
            $agenteId = (int) $fAgente;
            $relatoriosBase->whereExists(function ($w) use ($agenteId) {
                $w->select(DB::raw(1))
                  ->from('relatorio_participantes as rp2')
                  ->whereColumn('rp2.relatorio_id', 'r.id')
                  ->where('rp2.user_id', '=', $agenteId);
            });
        }

        // 2) Expande para participantes
        $base = DB::query()
            ->fromSub($relatoriosBase->select('r.*'), 'r')
            ->join('relatorio_participantes as rp', 'rp.relatorio_id', '=', 'r.id');

        // 3) DEDUPE: garante 1 linha por (relatorio_id, user_id, papel)
        //    Evita XP duplicado se tiver rp duplicado.
        $baseDistinct = DB::query()
            ->fromSub(
                $base->selectRaw('DISTINCT rp.user_id, rp.papel, r.id as relatorio_id'),
                'x'
            )
            ->join('relatorios as r', 'r.id', '=', 'x.relatorio_id');

        // Subquery 1: agrega por (user_id, papel)
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

        // Expressões de XP
        $dinheiroExpr = "CAST((t.dinheiro / 1000) AS INTEGER) * " . (int) $xp['dinheiro'];

        $xpBaseExpr = "
            (
                t.drogas * " . (int)$xp['drogas'] . " +
                t.pistolas * " . (int)$xp['pistolas'] . " +
                t.smg_fuzil * " . (int)$xp['smg_fuzil'] . " +
                t.municoes * " . (int)$xp['municoes'] . " +
                {$dinheiroExpr} +
                t.explosivos * " . (int)$xp['explosivos'] . " +
                t.lockpicks * " . (int)$xp['lockpicks'] . " +
                t.abordagens * " . (int)$xp['abordagens'] . " +
                t.multas * " . (int)$xp['multas'] . " +
                t.bopm * " . (int)$xp['bopm'] . " +
                t.viaturas_fiscalizadas * " . (int)$xp['viaturas_fiscalizadas'] . " +
                t.relatorios * " . (int)$xp['relatorio_aprovado'] . "
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

        // Subquery 2: soma tudo por user_id
        $sumUsers = DB::query()->fromSub(
            $byRole->selectRaw("
                t.user_id as user_id,
                SUM({$xpRoleExpr}) as xp,
                SUM(t.relatorios) as relatorios,

                SUM(t.drogas) as drogas,
                SUM(t.pistolas) as pistolas,
                SUM(t.smg_fuzil) as smg_fuzil,
                SUM(t.municoes) as municoes,
                SUM(t.dinheiro) as dinheiro,
                SUM(t.explosivos) as explosivos,
                SUM(t.lockpicks) as lockpicks
            ")->groupBy('t.user_id'),
            's'
        );

        // Query final: junta com users
        $ranking = DB::query()
            ->fromSub($sumUsers, 's')
            ->join('users', 'users.id', '=', 's.user_id')
            ->selectRaw("
                users.id as user_id,
                users.name,
                users.rg,
                users.cargo,
                users.avatar_path,

                s.xp as xp,
                s.relatorios as relatorios,

                s.drogas as drogas,
                s.pistolas as pistolas,
                s.smg_fuzil as smg_fuzil,
                s.municoes as municoes,
                s.dinheiro as dinheiro,
                s.explosivos as explosivos,
                s.lockpicks as lockpicks
            ")
            ->orderByDesc('xp')
            ->orderByDesc('relatorios')
            ->orderBy('users.name')
            ->get()
            ->map(function ($row) {
                $row->xp = (int) round((float) $row->xp);
                return $row;
            });

        $top10 = $ranking->take(10)->values();
        $top1  = $ranking->first();

        // =========================================================
        // POSIÇÃO DO USUÁRIO
        // =========================================================
        $meuId = auth()->id();
        $posicao = null;
        $meuResumo = null;

        foreach ($ranking as $i => $row) {
            if ((int) $row->user_id === (int) $meuId) {
                $posicao = $i + 1;
                $meuResumo = $row;
                break;
            }
        }

        // =========================================================
        // ✅ HORAS DE PATRULHAMENTO (NÍVEL 6+)
        // =========================================================
        $horasOficiais = collect();

        if ((int)(auth()->user()->nivel ?? 0) >= 6) {

            $hq = Relatorio::query()
                ->where('relatorios.status', '=', 'aprovado')
                ->join('relatorio_participantes', 'relatorio_participantes.relatorio_id', '=', 'relatorios.id')
                ->join('users', 'users.id', '=', 'relatorio_participantes.user_id');

            if ($inicio && $fim) {
                $hq->whereBetween('relatorios.data_patrulhamento', [
                    $inicio->toDateString(),
                    $fim->toDateString(),
                ]);
            }

            // filtros avançados (mesmos)
            if ($fUnidade !== '') {
                $hq->where('relatorios.unidade', '=', $fUnidade);
            }
            if ($fTipo !== '' && $tipoCol) {
                $hq->where("relatorios.$tipoCol", '=', $fTipo);
            }
            if ($fAgente !== '') {
                $hq->where('users.id', '=', (int)$fAgente);
            }

            // filtro de busca RG/nome
            if ($q !== '') {
                $hq->where(function ($w) use ($q) {
                    $w->where('users.name', 'like', '%' . $q . '%')
                      ->orWhere('users.rg', 'like', '%' . $q . '%');
                });
            }

            $rows = $hq->select([
                'users.id as user_id',
                'users.name',
                'users.rg',
                'users.cargo',
                'users.avatar_path',
                'relatorios.id as relatorio_id',
                'relatorios.data_patrulhamento',
                DB::raw("relatorios.$colInicio as hora_inicio"),
                DB::raw("relatorios.$colFim as hora_fim"),
            ])->get();

            $acc = [];

            foreach ($rows as $r) {
                $uid = (int) ($r->user_id ?? 0);
                if ($uid <= 0) continue;

                if (!isset($acc[$uid])) {
                    $acc[$uid] = [
                        'user_id' => $uid,
                        'name' => $r->name,
                        'rg' => $r->rg,
                        'cargo' => $r->cargo,
                        'avatar_path' => $r->avatar_path,
                        'minutes' => 0,
                        'relatorio_ids' => [],
                    ];
                }

                $rid = (int) ($r->relatorio_id ?? 0);
                if ($rid > 0) {
                    $acc[$uid]['relatorio_ids'][$rid] = true;
                }

                $mins = $this->calcMinutesSQLiteSafe(
                    $r->data_patrulhamento,
                    $r->hora_inicio,
                    $r->hora_fim
                );

                $acc[$uid]['minutes'] += $mins;
            }

            $horasOficiais = collect(array_values($acc))
                ->map(function ($u) {
                    $mins = max(0, (int)$u['minutes']);
                    $h = intdiv($mins, 60);
                    $m = $mins % 60;

                    return (object) [
                        'user_id' => $u['user_id'],
                        'name' => $u['name'],
                        'rg' => $u['rg'],
                        'cargo' => $u['cargo'],
                        'avatar_path' => $u['avatar_path'],
                        'relatorios' => count($u['relatorio_ids'] ?? []),
                        'total_minutes' => $mins,
                        'total_horas' => round($mins / 60, 2),
                        'hhmm' => sprintf('%02d:%02d', $h, $m),
                    ];
                })
                ->sortBy([
                    ['total_minutes', 'desc'],
                    ['name', 'asc'],
                ])
                ->values();
        }

        // =========================================================
        // ✅ LISTAS PARA OS SELECTS (Unidade / Tipo / Agente)
        // =========================================================
        $unidades = DB::table('relatorios')
            ->where('status', 'aprovado')
            ->whereNotNull('unidade')
            ->where('unidade', '<>', '')
            ->distinct()
            ->orderBy('unidade')
            ->pluck('unidade')
            ->values();

        $tipos = collect();
        if ($tipoCol) {
            $tipos = DB::table('relatorios')
                ->where('status', 'aprovado')
                ->whereNotNull($tipoCol)
                ->where($tipoCol, '<>', '')
                ->distinct()
                ->orderBy($tipoCol)
                ->pluck($tipoCol)
                ->values();
        }

        $agentes = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('ranking.index', [
            // compat
            'periodo' => $periodo,

            // ✅ datas do mês/ano
            'inicio' => $inicio,
            'fim' => $fim,

            // ✅ ranking
            'top10' => $top10,
            'top1' => $top1,
            'posicao' => $posicao,
            'meuResumo' => $meuResumo,

            // ✅ horas
            'horasOficiais' => $horasOficiais,
            'q' => $q,

            // ✅ filtros + listas
            'mes' => $mes,
            'ano' => $ano,
            'fUnidade' => $fUnidade,
            'fTipo' => $fTipo,
            'fAgente' => $fAgente,
            'unidades' => $unidades,
            'tipos' => $tipos,
            'agentes' => $agentes,
        ]);
    }

    /**
     * ✅ RESUMO (API)
     * GET /ranking/resumo/{user}?mes=1&ano=2026&unidade=...&tipo=...&agente=...
     * (mantém compatibilidade com ?periodo=geral)
     */
    public function resumo(Request $request, int $user)
    {
        $mes = (int) $request->get('mes', now()->month);
        $ano = (int) $request->get('ano', now()->year);
        $mes = max(1, min(12, $mes));
        $ano = max((int)now()->year - 10, min((int)now()->year + 2, $ano));

        $fUnidade = trim((string) $request->get('unidade', ''));
        $fTipo    = trim((string) $request->get('tipo', ''));
        $fAgente  = trim((string) $request->get('agente', ''));

        $periodo = (string) $request->get('periodo', 'mes');

        if ($periodo === 'geral') {
            $inicio = null;
            $fim = null;
        } else {
            $inicio = Carbon::create($ano, $mes, 1)->startOfDay();
            $fim    = Carbon::create($ano, $mes, 1)->endOfMonth()->endOfDay();
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

        foreach (['P1','P2','P3','P4','P5'] as $p) {
            if (!isset($mult[$p]) || !is_numeric($mult[$p])) $mult[$p] = 1.0;
            $mult[$p] = max(0, (float) $mult[$p]);
        }

        $tipoCol = null;
        if (Schema::hasColumn('relatorios', 'tipo_ocorrencia')) {
            $tipoCol = 'tipo_ocorrencia';
        } elseif (Schema::hasColumn('relatorios', 'tipo')) {
            $tipoCol = 'tipo';
        }

        // =========================================================
        // ✅ RESUMO (mesma lógica do ranking: filtra relatório + dedupe)
        // =========================================================
        $relatoriosBase = DB::table('relatorios as r')
            ->where('r.status', '=', 'aprovado');

        if ($inicio && $fim) {
            $relatoriosBase->whereBetween('r.data_patrulhamento', [
                $inicio->toDateString(),
                $fim->toDateString(),
            ]);
        }

        if ($fUnidade !== '') {
            $relatoriosBase->where('r.unidade', '=', $fUnidade);
        }
        if ($fTipo !== '' && $tipoCol) {
            $relatoriosBase->where("r.$tipoCol", '=', $fTipo);
        }

        // ✅ Agente: filtra relatórios onde esse agente participou (sem matar o conjunto)
        if ($fAgente !== '') {
            $agenteId = (int) $fAgente;
            $relatoriosBase->whereExists(function ($w) use ($agenteId) {
                $w->select(DB::raw(1))
                  ->from('relatorio_participantes as rp2')
                  ->whereColumn('rp2.relatorio_id', 'r.id')
                  ->where('rp2.user_id', '=', $agenteId);
            });
        }

        $base = DB::query()
            ->fromSub($relatoriosBase->select('r.*'), 'r')
            ->join('relatorio_participantes as rp', 'rp.relatorio_id', '=', 'r.id');

        $baseDistinct = DB::query()
            ->fromSub(
                $base->selectRaw('DISTINCT rp.user_id, rp.papel, r.id as relatorio_id'),
                'x'
            )
            ->join('relatorios as r', 'r.id', '=', 'x.relatorio_id');

        // Subquery 1: agrega por (user_id, papel)
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
                t.drogas * " . (int)$xp['drogas'] . " +
                t.pistolas * " . (int)$xp['pistolas'] . " +
                t.smg_fuzil * " . (int)$xp['smg_fuzil'] . " +
                t.municoes * " . (int)$xp['municoes'] . " +
                {$dinheiroExpr} +
                t.explosivos * " . (int)$xp['explosivos'] . " +
                t.lockpicks * " . (int)$xp['lockpicks'] . " +
                t.abordagens * " . (int)$xp['abordagens'] . " +
                t.multas * " . (int)$xp['multas'] . " +
                t.bopm * " . (int)$xp['bopm'] . " +
                t.viaturas_fiscalizadas * " . (int)$xp['viaturas_fiscalizadas'] . " +
                t.relatorios * " . (int)$xp['relatorio_aprovado'] . "
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
                SUM(t.relatorios) as relatorios,

                SUM(t.drogas) as drogas,
                SUM(t.pistolas) as pistolas,
                SUM(t.smg_fuzil) as smg_fuzil,
                SUM(t.municoes) as municoes,
                SUM(t.dinheiro) as dinheiro,
                SUM(t.explosivos) as explosivos,
                SUM(t.lockpicks) as lockpicks
            ")->groupBy('t.user_id'),
            's'
        );

        $ranking = DB::query()
            ->fromSub($sumUsers, 's')
            ->join('users', 'users.id', '=', 's.user_id')
            ->selectRaw("
                users.id as user_id,
                users.name,
                users.rg,
                users.cargo,
                users.avatar_path,

                s.xp as xp,
                s.relatorios as relatorios,

                s.drogas as drogas,
                s.pistolas as pistolas,
                s.smg_fuzil as smg_fuzil,
                s.municoes as municoes,
                s.dinheiro as dinheiro,
                s.explosivos as explosivos,
                s.lockpicks as lockpicks
            ")
            ->orderByDesc('xp')
            ->orderByDesc('relatorios')
            ->orderBy('users.name')
            ->get()
            ->map(function ($row) {
                $row->xp = (int) round((float) $row->xp);
                return $row;
            });

        $top1 = $ranking->first();

        $posicao = null;
        $rowUser = null;

        foreach ($ranking as $i => $row) {
            if ((int)($row->user_id ?? 0) === (int)$user) {
                $posicao = $i + 1;
                $rowUser = $row;
                break;
            }
        }

        if (!$rowUser) {
            $u = DB::table('users')
                ->select('id as user_id', 'name', 'rg', 'cargo', 'avatar_path')
                ->where('id', '=', $user)
                ->first();

            return response()->json([
                'user_id' => (int) $user,
                'name' => $u->name ?? '—',
                'rg' => $u->rg ?? '—',
                'cargo' => $u->cargo ?? '—',
                'avatar_path' => $u->avatar_path ?? null,

                'posicao' => null,

                'xp' => 0,
                'relatorios' => 0,

                'drogas' => 0,
                'pistolas' => 0,
                'smg_fuzil' => 0,
                'municoes' => 0,
                'dinheiro' => 0,
                'explosivos' => 0,
                'lockpicks' => 0,

                'top1' => $top1 ? [
                    'user_id' => (int) $top1->user_id,
                    'name' => $top1->name,
                    'rg' => $top1->rg,
                    'cargo' => $top1->cargo,
                    'avatar_path' => $top1->avatar_path,
                    'xp' => (int) $top1->xp,
                ] : null,
            ]);
        }

        return response()->json([
            'user_id' => (int) $rowUser->user_id,
            'name' => $rowUser->name,
            'rg' => $rowUser->rg,
            'cargo' => $rowUser->cargo,
            'avatar_path' => $rowUser->avatar_path,

            'posicao' => $posicao,

            'xp' => (int) $rowUser->xp,
            'relatorios' => (int) $rowUser->relatorios,

            'drogas' => (int) $rowUser->drogas,
            'pistolas' => (int) $rowUser->pistolas,
            'smg_fuzil' => (int) $rowUser->smg_fuzil,
            'municoes' => (int) $rowUser->municoes,
            'dinheiro' => (int) $rowUser->dinheiro,
            'explosivos' => (int) $rowUser->explosivos,
            'lockpicks' => (int) $rowUser->lockpicks,

            'top1' => $top1 ? [
                'user_id' => (int) $top1->user_id,
                'name' => $top1->name,
                'rg' => $top1->rg,
                'cargo' => $top1->cargo,
                'avatar_path' => $top1->avatar_path,
                'xp' => (int) $top1->xp,
            ] : null,
        ]);
    }

    private function calcMinutesSQLiteSafe($data, $inicio, $fim): int
    {
        try {
            if (empty($data) || empty($inicio) || empty($fim)) return 0;

            $date = $data instanceof Carbon ? $data->toDateString() : (string)$data;

            $start = Carbon::parse($date . ' ' . (string)$inicio);
            $end   = Carbon::parse($date . ' ' . (string)$fim);

            if ($end->lessThan($start)) $end->addDay();

            return max(0, (int) $start->diffInMinutes($end));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    // Mantive por compatibilidade (se você usar em outros lugares)
    private function periodoParaDatas(string $periodo): array
    {
        $now = now();

        return match ($periodo) {
            'hoje'   => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'semana' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'mes'    => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'geral'  => [null, null],
            default  => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}