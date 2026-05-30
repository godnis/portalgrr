<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ResultadosPublicosController extends Controller
{
    public function index(Request $request)
    {
        $inicioMes = now()->startOfMonth();
        $fimMes    = now()->endOfMonth();

        $cacheKey = 'resultados_publicos_' . now()->format('Ym');

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($inicioMes, $fimMes) {

            // Apenas relatórios APROVADOS
            $base = DB::table('relatorios')
                ->where('status', 'aprovado')
                ->whereBetween('created_at', [$inicioMes, $fimMes]);

            // ✅ KPIs principais
            $operacoesMes = (clone $base)->count();

            $abordagensMes = (int) (clone $base)->sum('abordagens');

            $totalDrogas = (int) (clone $base)->sum('drogas');
            $totalArmas  = (int) (
                (clone $base)->sum('pistolas') +
                (clone $base)->sum('smg_fuzil')
            );
            $totalExplosivos = (int) (clone $base)->sum('explosivos');

            $apreensoesMes = $totalDrogas + $totalArmas + $totalExplosivos;

            $veiculosFiscalizados = (int) (clone $base)->sum('viaturas_fiscalizadas');

            // ✅ Apreensões por categoria (resumo público)
            $apreensoesPorTipo = [];

            if ($totalDrogas > 0) {
                $apreensoesPorTipo[] = [
                    'tipo'  => 'Entorpecentes',
                    'total' => $totalDrogas,
                ];
            }

            if ($totalArmas > 0) {
                $apreensoesPorTipo[] = [
                    'tipo'  => 'Armamentos',
                    'total' => $totalArmas,
                ];
            }

            if ($totalExplosivos > 0) {
                $apreensoesPorTipo[] = [
                    'tipo'  => 'Explosivos',
                    'total' => $totalExplosivos,
                ];
            }

            return [
                'kpis' => [
                    'operacoes_mes'        => $operacoesMes,
                    'abordagens_mes'       => $abordagensMes,
                    'apreensoes_mes'       => $apreensoesMes,
                    'veiculos_recuperados' => $veiculosFiscalizados,
                ],
                'apreensoes_por_tipo' => $apreensoesPorTipo,
            ];
        });

        return view('publico.resultados', [
            'kpis' => $data['kpis'],
            'apreensoes_por_tipo' => $data['apreensoes_por_tipo'],
        ]);
    }
}
