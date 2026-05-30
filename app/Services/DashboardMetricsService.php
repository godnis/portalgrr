<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Métricas PÚBLICAS (sem dados sensíveis)
     */
    public function publicMetrics(): array
    {
        return Cache::remember('metrics_public_month_' . now()->format('Ym'), now()->addMinutes(5), function () {

            $ini = now()->startOfMonth();
            $fim = now()->endOfMonth();

            /**
             * ⚠️ TROQUE AQUI para as SUAS tabelas/colunas reais do board
             * A ideia é: usar as mesmas fontes do seu dashboard interno.
             */

            // Exemplo 1: operações = relatórios finalizados no mês
            $operacoesMes = DB::table('relatorios')
                ->whereBetween('created_at', [$ini, $fim])
                ->where('status', 'finalizado')
                ->count();

            // Exemplo 2: abordagens = tabela abordagens (ou eventos)
            $abordagensMes = DB::table('abordagens')
                ->whereBetween('created_at', [$ini, $fim])
                ->count();

            // Exemplo 3: apreensões = tabela apreensoes no mês
            $apreensoesMes = DB::table('apreensoes')
                ->whereBetween('created_at', [$ini, $fim])
                ->count();

            // Exemplo 4: veículos recuperados (pode ser um tipo específico)
            $veiculosRecuperados = DB::table('apreensoes')
                ->whereBetween('created_at', [$ini, $fim])
                ->where('tipo', 'veiculo_recuperado')
                ->count();

            // Apreensões por categoria (agrupado)
            $apreensoesPorTipo = DB::table('apreensoes')
                ->select('tipo', DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$ini, $fim])
                ->groupBy('tipo')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($r) => ['tipo' => $r->tipo, 'total' => (int) $r->total])
                ->toArray();

            return [
                'kpis' => [
                    'operacoes_mes'        => (int) $operacoesMes,
                    'abordagens_mes'       => (int) $abordagensMes,
                    'apreensoes_mes'       => (int) $apreensoesMes,
                    'veiculos_recuperados' => (int) $veiculosRecuperados,
                ],
                'apreensoes_por_tipo' => $apreensoesPorTipo,
            ];
        });
    }
}
