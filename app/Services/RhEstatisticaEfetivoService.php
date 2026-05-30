<?php

namespace App\Services;

use Illuminate\Support\Collection;

class RhEstatisticaEfetivoService
{
    public function build(Collection $rows): array
    {
        $rows = $rows->values();

        // --------------------------
        // Totais base
        // --------------------------
        $totalAll = $rows->count();

        // Situação (status)
        $statusCounts = $rows
            ->groupBy(fn($r) => $r->status ?: 'indefinido')
            ->map(fn($g) => $g->count());

        $cEx = (int) ($statusCounts['em_exercicio'] ?? 0);
        $cLi = (int) ($statusCounts['em_licenca'] ?? 0);
        $cDe = (int) ($statusCounts['desligado'] ?? 0);
        $cEs = (int) ($statusCounts['estagio'] ?? 0);

        // --------------------------
        // Efetivo / Estágio
        // (regra prática: efetivo = em_exercicio + em_licenca)
        // --------------------------
        $efetivo = $cEx + $cLi;
        $estagio = $cEs;
        $totalEfetivoEstagio = $efetivo + $estagio;

        // --------------------------
        // Instrutores (flag instrutor)
        // --------------------------
        $instrutores = (int) $rows->where('instrutor', true)->count();

        // --------------------------
        // Cargos (pelo cargo_sync se existir / senão cargo)
        // --------------------------
        $cargos = $rows
            ->groupBy(function ($r) {
                $c = $r->cargo_sync ?? $r->cargo ?? '—';
                $c = trim((string)$c);
                return $c !== '' ? $c : '—';
            })
            ->map(fn($g) => $g->count())
            ->sortDesc();

        // --------------------------
        // Classes Funcionais (do seu PRINT):
        // Estratégicos = diretoria
        // Táticos      = agente especial + agente 1º classe
        // Operacionais = agente 2º classe + agente 3º classe
        // (Aluno fica fora)
        // --------------------------
        $norm = function (string $s): string {
            $s = mb_strtolower(trim($s));
            $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
            $s = str_replace(['º','°','ª','-','_','.'], ' ', $s);
            $s = preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };

        $is = function ($cargo, string $needle) use ($norm): bool {
            return str_contains($norm((string)$cargo), $needle);
        };

        $estrategicos = 0;
        $taticos = 0;
        $operacionais = 0;

        foreach ($rows as $r) {
            $cargo = $r->cargo_sync ?? $r->cargo ?? '';
            $cargoN = $norm((string)$cargo);

            // ignora aluno
            if (str_contains($cargoN, 'aluno')) continue;

            // diretoria
            if (
                str_contains($cargoN, 'diretor') ||
                str_contains($cargoN, 'vice diretor') ||
                str_contains($cargoN, 'coordenador') ||
                str_contains($cargoN, 'superintendente') ||
                str_contains($cargoN, 'inspetor')
            ) {
                $estrategicos++;
                continue;
            }

            // táticos
            if (str_contains($cargoN, 'agente especial') || str_contains($cargoN, 'agente de 1')) {
                $taticos++;
                continue;
            }

            // operacionais
            if (str_contains($cargoN, 'agente de 2') || str_contains($cargoN, 'agente de 3')) {
                $operacionais++;
                continue;
            }
        }

        $totalClasses = $estrategicos + $taticos + $operacionais;

        // --------------------------
        // Formações (%)
        // Base: somente "ativos" (em_exercicio + em_licenca)
        // --------------------------
        $base = $rows->filter(fn($r) => in_array($r->status, ['em_exercicio','em_licenca'], true))->values();
        $baseTotal = max(1, $base->count()); // evita divisão por zero

        $keys = ['pop','clt','cap','ctb','bopm','satb','cta','gmp','doa'];

        $pct = [];
        $sum = 0;

        foreach ($keys as $k) {
            $ok = (int) $base->where($k, true)->count();
            $p = (int) round(($ok / $baseTotal) * 100);
            $pct[$k] = $p;
            $sum += $p;
        }

        $media = (int) round($sum / count($keys));

        // --------------------------
        // Retorno padronizado (nunca vazio)
        // --------------------------
        return [
            'efetivo_estagio' => [
                'efetivo' => $efetivo,
                'estagio' => $estagio,
                'total'   => $totalEfetivoEstagio,
            ],
            'situacao' => [
                'em_ingresso' => (int) ($statusCounts['em_ingresso'] ?? 0),
                'em_exercicio'=> $cEx,
                'ausente'     => (int) ($statusCounts['ausente'] ?? 0),
                'em_licenca'  => $cLi,
                'desligado'   => $cDe,
                'estagio'     => $cEs,
                'total'       => $cEx + $cLi + $cDe + $cEs
                               + (int)($statusCounts['em_ingresso'] ?? 0)
                               + (int)($statusCounts['ausente'] ?? 0),
            ],
            'classes_funcionais' => [
                'estrategicos' => $estrategicos,
                'taticos'      => $taticos,
                'operacionais' => $operacionais,
                'total'        => $totalClasses,
            ],
            'instrutores' => $instrutores,
            'cargos' => $cargos->toArray(),
            'formacoes' => [
                'pop'  => $pct['pop'],
                'clt'  => $pct['clt'],
                'cap'  => $pct['cap'],
                'ctb'  => $pct['ctb'],
                'bopm' => $pct['bopm'],
                'satb' => $pct['satb'],
                'cta'  => $pct['cta'],
                'gmp'  => $pct['gmp'],
                'doa'  => $pct['doa'],
                'media'=> $media,
            ],
            'meta' => [
                'base_formacoes' => $base->count(),
                'total_registros' => $totalAll,
            ]
        ];
    }
}
