<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Relatorio extends Model
{
    protected $fillable = [
        'user_id',

        // Controle de envio / idempotência
        'client_token',

        // Guarnição
        'qra_chefe',
        'unidade',
        'motorista',
        'terceiro',
        'quarto',
        'quinto',

        // Tempo
        'data_patrulhamento',
        'inicio_patrulhamento',
        'final_patrulhamento',

        // Apreensões
        'pistolas',
        'smg_fuzil',
        'municoes',
        'drogas',
        'dinheiro',
        'explosivos',
        'lockpicks',

        // Ações operacionais
        'abordagens',
        'apoio',
        'incursao',
        'negociacao',
        'blitz',
        'escolta',
        'multas',
        'bopm',
        'bopm_registros',
        'viaturas_fiscalizadas',

        // Observações
        'observacoes',

        // Controle / decisão
        'status',
        'aprovado_por',
        'reprovado_por',
        'decisao_obs',
    ];

    /**
     * Participantes vinculados ao relatório
     */
    public function participantes()
    {
        return $this->belongsToMany(
            User::class,
            'relatorio_participantes',
            'relatorio_id',
            'user_id'
        )->withPivot('papel')->withTimestamps();
    }

    /**
     * Normaliza RG
     * - remove espaços
     * - mantém apenas números
     */
    private function normRG($rg): ?string
    {
        if ($rg === null) {
            return null;
        }

        $rg = trim((string) $rg);

        if ($rg === '') {
            return null;
        }

        $rg = preg_replace('/\D+/', '', $rg);

        return $rg !== '' ? $rg : null;
    }

    /**
     * Sincroniza participantes do relatório pela tabela pivot
     *
     * Regras:
     * P1 = motorista
     * P2 = chefe da barca
     * P3 = terceiro
     * P4 = quarto
     * P5 = quinto
     */
    public function syncParticipantesPorRG(): void
    {
        $map = [
            'P1' => $this->normRG($this->motorista),
            'P2' => $this->normRG($this->qra_chefe),
            'P3' => $this->normRG($this->terceiro),
            'P4' => $this->normRG($this->quarto),
            'P5' => $this->normRG($this->quinto),
        ];

        // Remove valores nulos/vazios
        $map = array_filter($map, fn ($rg) => !is_null($rg));

        if (empty($map)) {
            $this->participantes()->sync([]);
            return;
        }

        // Busca usuários pelos RGs informados
        $users = User::query()
            ->whereIn('rg', array_values($map))
            ->get(['id', 'rg']);

        $sync = [];

        foreach ($map as $papel => $rg) {
            $user = $users->firstWhere('rg', $rg);

            if ($user) {
                $sync[$user->id] = ['papel' => $papel];
            }
        }

        $this->participantes()->sync($sync);
    }
}