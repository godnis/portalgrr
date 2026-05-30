<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1) Helpers locais
        |--------------------------------------------------------------------------
        */
        $hasColumn = function (string $table, string $column): bool {
            try {
                $cols = DB::select("PRAGMA table_info({$table})");

                foreach ($cols as $col) {
                    if (($col->name ?? null) === $column) {
                        return true;
                    }
                }

                return false;
            } catch (\Throwable $e) {
                return false;
            }
        };

        $calcVariation = function ($current, $previous): object {
            $current = (float) ($current ?? 0);
            $previous = (float) ($previous ?? 0);

            $diff = $current - $previous;
            $pct = $previous > 0
                ? (($diff / $previous) * 100)
                : ($current > 0 ? 100 : 0);

            return (object) [
                'diff' => $diff,
                'pct'  => $pct,
                'dir'  => $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'flat'),
            ];
        };

        $emptyTotals = fn () => (object) [
            'relatorios' => 0,
            'drogas' => 0,
            'explosivos' => 0,
            'lockpicks' => 0,
            'pistolas' => 0,
            'smg_fuzil' => 0,
            'municoes' => 0,
            'dinheiro' => 0,
            'multas' => 0,
            'bopm' => 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | 2) Configuração base
        |--------------------------------------------------------------------------
        */
        $dateCol = 'data_patrulhamento';
        $hasTipo = $hasColumn('relatorios', 'tipo_ocorrencia');
        $hasAgenteId = $hasColumn('relatorios', 'agente_id');
        $is6 = auth()->check() && (int) (auth()->user()->nivel ?? 0) >= 6;

        /*
        |--------------------------------------------------------------------------
        | 3) Período (mês/ano ou geral)
        |--------------------------------------------------------------------------
        */
        $mes = (int) $request->get('mes', now()->month);
        $ano = (int) $request->get('ano', now()->year);

        if ($mes !== 0 && ($mes < 1 || $mes > 12)) {
            $mes = now()->month;
        }

        if ($mes === 0) {
            $inicio = Carbon::create($ano, 1, 1)->startOfDay();
            $fim = Carbon::create($ano, 12, 31)->endOfDay();

            $inicioPrev = Carbon::create($ano - 1, 1, 1)->startOfDay();
            $fimPrev = Carbon::create($ano - 1, 12, 31)->endOfDay();
        } else {
            $inicio = Carbon::create($ano, $mes, 1)->startOfDay();
            $fim = Carbon::create($ano, $mes, 1)->endOfMonth()->endOfDay();

            $inicioPrev = (clone $inicio)->subMonthNoOverflow()->startOfMonth()->startOfDay();
            $fimPrev = (clone $inicioPrev)->copy()->endOfMonth()->endOfDay();
        }

        /*
        |--------------------------------------------------------------------------
        | 4) Filtros avançados
        |--------------------------------------------------------------------------
        */
        $fUnidade = trim((string) $request->get('unidade', ''));
        $fTipo = trim((string) $request->get('tipo', ''));
        $fAgente = trim((string) $request->get('agente', ''));

        $applyFilters = function ($query) use ($fUnidade, $fTipo, $fAgente, $hasTipo, $hasAgenteId) {
            if ($fUnidade !== '') {
                $query->where('unidade', $fUnidade);
            }

            if ($fTipo !== '' && $hasTipo) {
                $query->where('tipo_ocorrencia', $fTipo);
            }

            if ($fAgente !== '') {
                if ($hasAgenteId) {
                    $query->where('agente_id', $fAgente);
                } else {
                    $query->where('user_id', $fAgente);
                }
            }

            return $query;
        };

        /*
        |--------------------------------------------------------------------------
        | 5) Query base
        |--------------------------------------------------------------------------
        */
        $base = Relatorio::query()->where('status', 'aprovado');

        /*
        |--------------------------------------------------------------------------
        | 6) Cache
        |--------------------------------------------------------------------------
        */
        $ttl = 60;

        $cacheSignature = [
            'mes' => $mes,
            'ano' => $ano,
            'unidade' => $fUnidade,
            'tipo' => $fTipo,
            'agente' => $fAgente,
            'is6' => $is6 ? 1 : 0,
            'has_tipo' => $hasTipo ? 1 : 0,
            'has_agente_id' => $hasAgenteId ? 1 : 0,
            'date_col' => $dateCol,
        ];

        $keyBase = 'dash:v6:' . md5(json_encode($cacheSignature));

        /*
        |--------------------------------------------------------------------------
        | 7) Totais + comparativo + donut
        |--------------------------------------------------------------------------
        */
        $totaisPack = Cache::remember($keyBase . ':totais', $ttl, function () use (
            $base,
            $applyFilters,
            $dateCol,
            $inicio,
            $fim,
            $inicioPrev,
            $fimPrev,
            $calcVariation,
            $emptyTotals
        ) {
            $qAtual = (clone $base)->whereBetween($dateCol, [
                $inicio->toDateString(),
                $fim->toDateString(),
            ]);
            $applyFilters($qAtual);

            $totais = $qAtual->selectRaw("
                COUNT(*) as relatorios,
                COALESCE(SUM(drogas),0) as drogas,
                COALESCE(SUM(explosivos),0) as explosivos,
                COALESCE(SUM(lockpicks),0) as lockpicks,
                COALESCE(SUM(pistolas),0) as pistolas,
                COALESCE(SUM(smg_fuzil),0) as smg_fuzil,
                COALESCE(SUM(municoes),0) as municoes,
                COALESCE(SUM(dinheiro),0) as dinheiro,
                COALESCE(SUM(multas),0) as multas,
                COALESCE(SUM(bopm),0) as bopm
            ")->first() ?? $emptyTotals();

            $qPrev = (clone $base)->whereBetween($dateCol, [
                $inicioPrev->toDateString(),
                $fimPrev->toDateString(),
            ]);
            $applyFilters($qPrev);

            $totaisPrev = $qPrev->selectRaw("
                COUNT(*) as relatorios,
                COALESCE(SUM(drogas),0) as drogas,
                COALESCE(SUM(explosivos),0) as explosivos,
                COALESCE(SUM(lockpicks),0) as lockpicks,
                COALESCE(SUM(pistolas),0) as pistolas,
                COALESCE(SUM(smg_fuzil),0) as smg_fuzil,
                COALESCE(SUM(municoes),0) as municoes,
                COALESCE(SUM(dinheiro),0) as dinheiro,
                COALESCE(SUM(multas),0) as multas,
                COALESCE(SUM(bopm),0) as bopm
            ")->first() ?? $emptyTotals();

            $variacoes = (object) [
                'relatorios' => $calcVariation($totais->relatorios, $totaisPrev->relatorios),
                'drogas'     => $calcVariation($totais->drogas, $totaisPrev->drogas),
                'explosivos' => $calcVariation($totais->explosivos, $totaisPrev->explosivos),
                'lockpicks'  => $calcVariation($totais->lockpicks, $totaisPrev->lockpicks),
                'pistolas'   => $calcVariation($totais->pistolas, $totaisPrev->pistolas),
                'smg_fuzil'  => $calcVariation($totais->smg_fuzil, $totaisPrev->smg_fuzil),
                'municoes'   => $calcVariation($totais->municoes, $totaisPrev->municoes),
                'dinheiro'   => $calcVariation($totais->dinheiro, $totaisPrev->dinheiro),
                'multas'     => $calcVariation($totais->multas, $totaisPrev->multas),
                'bopm'       => $calcVariation($totais->bopm, $totaisPrev->bopm),
            ];

            $distLabels = [
                'Drogas',
                'Explosivos',
                'Lockpicks',
                'Pistolas',
                'SMG/Fuzil',
                'Munições',
                'Dinheiro',
                'Multas',
                'BOPM',
            ];

            $distData = [
                (int) ($totais->drogas ?? 0),
                (int) ($totais->explosivos ?? 0),
                (int) ($totais->lockpicks ?? 0),
                (int) ($totais->pistolas ?? 0),
                (int) ($totais->smg_fuzil ?? 0),
                (int) ($totais->municoes ?? 0),
                (int) ($totais->dinheiro ?? 0),
                (int) ($totais->multas ?? 0),
                (int) ($totais->bopm ?? 0),
            ];

            return [
                'totais' => $totais,
                'totaisPrev' => $totaisPrev,
                'variacoes' => $variacoes,
                'distLabels' => $distLabels,
                'distData' => $distData,
            ];
        });

        $totais = $totaisPack['totais'];
        $totaisPrev = $totaisPack['totaisPrev'];
        $variacoes = $totaisPack['variacoes'];
        $distLabels = $totaisPack['distLabels'];
        $distData = $totaisPack['distData'];

        /*
        |--------------------------------------------------------------------------
        | 8) Relatórios por dia
        |--------------------------------------------------------------------------
        */
        $porDia = Cache::remember($keyBase . ':pordia', $ttl, function () use (
            $base,
            $applyFilters,
            $dateCol,
            $inicio,
            $fim
        ) {
            $qDia = (clone $base)->whereBetween($dateCol, [
                $inicio->toDateString(),
                $fim->toDateString(),
            ]);

            $applyFilters($qDia);

            return $qDia->selectRaw("DATE($dateCol) as dia, COUNT(*) as total")
                ->groupBy('dia')
                ->orderBy('dia')
                ->get();
        });

        /*
        |--------------------------------------------------------------------------
        | 9) Resumo por unidade + Top unidades
        |--------------------------------------------------------------------------
        */
        if ($is6) {
            $porUnidadePack = Cache::remember($keyBase . ':porunidade', $ttl, function () use (
                $base,
                $applyFilters,
                $dateCol,
                $inicio,
                $fim
            ) {
                $qUn = (clone $base)->whereBetween($dateCol, [
                    $inicio->toDateString(),
                    $fim->toDateString(),
                ]);

                $applyFilters($qUn);

                $porUnidade = $qUn->selectRaw("
                    unidade,
                    COUNT(*) as relatorios,
                    COALESCE(SUM(drogas),0) as drogas,
                    COALESCE(SUM(explosivos),0) as explosivos,
                    COALESCE(SUM(lockpicks),0) as lockpicks,
                    COALESCE(SUM(pistolas),0) as pistolas,
                    COALESCE(SUM(smg_fuzil),0) as smg_fuzil,
                    COALESCE(SUM(multas),0) as multas,
                    COALESCE(SUM(bopm),0) as bopm
                ")
                    ->groupBy('unidade')
                    ->orderByDesc('relatorios')
                    ->orderBy('unidade')
                    ->get();

                $topUnidades = $porUnidade->take(6)->values();

                return [
                    'porUnidade' => $porUnidade,
                    'topUnidades' => $topUnidades,
                ];
            });

            $porUnidade = $porUnidadePack['porUnidade'];
            $topUnidades = $porUnidadePack['topUnidades'];
        } else {
            $porUnidade = collect();
            $topUnidades = collect();
        }

        /*
        |--------------------------------------------------------------------------
        | 10) Opções dos filtros
        |--------------------------------------------------------------------------
        */
        $optsKey = 'dash:v6:opts:' . md5(json_encode([
            'mes' => $mes,
            'ano' => $ano,
            'has_tipo' => $hasTipo ? 1 : 0,
            'date_col' => $dateCol,
        ]));

        $opts = Cache::remember($optsKey, $ttl, function () use (
            $base,
            $dateCol,
            $inicio,
            $fim,
            $hasTipo,
            $hasAgenteId
        ) {
            $basePeriodo = (clone $base)->whereBetween($dateCol, [
                $inicio->toDateString(),
                $fim->toDateString(),
            ]);

            $unidades = (clone $basePeriodo)
                ->whereNotNull('unidade')
                ->where('unidade', '!=', '')
                ->select('unidade')
                ->distinct()
                ->orderBy('unidade')
                ->pluck('unidade');

            $tipos = collect();

            if ($hasTipo) {
                $tipos = (clone $basePeriodo)
                    ->whereNotNull('tipo_ocorrencia')
                    ->where('tipo_ocorrencia', '!=', '')
                    ->select('tipo_ocorrencia')
                    ->distinct()
                    ->orderBy('tipo_ocorrencia')
                    ->pluck('tipo_ocorrencia');
            }

            $agenteField = $hasAgenteId ? 'agente_id' : 'user_id';

            $agentIds = (clone $basePeriodo)
                ->whereNotNull($agenteField)
                ->select($agenteField)
                ->distinct()
                ->pluck($agenteField)
                ->filter()
                ->values();

            $agentes = User::query()
                ->whereIn('id', $agentIds)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return [
                'unidades' => $unidades,
                'tipos' => $tipos,
                'agentes' => $agentes,
            ];
        });

        $unidades = $opts['unidades'];
        $tipos = $opts['tipos'];
        $agentes = $opts['agentes'];

        /*
        |--------------------------------------------------------------------------
        | 11) View
        |--------------------------------------------------------------------------
        */
        return view('dashboard', compact(
            'inicio',
            'fim',
            'inicioPrev',
            'fimPrev',
            'mes',
            'ano',
            'totais',
            'totaisPrev',
            'variacoes',
            'porDia',
            'porUnidade',
            'topUnidades',
            'unidades',
            'tipos',
            'agentes',
            'fUnidade',
            'fTipo',
            'fAgente',
            'distLabels',
            'distData'
        ));
    }
}