<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreInscricao extends Model
{
    protected $table = 'pre_inscricoes';

    protected $fillable = [
        // ===============================
        // Tracking / Auditoria
        // ===============================
        'origem',
        'ip',
        'user_agent',

        // ===============================
        // Dados do candidato (BC)
        // ===============================
        'nome_completo',
        'rg',

        // (legado — mantém compatibilidade)
        'qra_rg',

        // ===============================
        // Formulário de recrutamento
        // ===============================
        'discord_id',
        'motivo_grr_agora',
        'diferencial_grr',
        'estagio_15_dias',
        'dias_ativo_semana',
        'ordem_nao_concorda',
        'horario_frequente',
        'como_lida_frustracao',
        'experiencia_anterior',

        // ===============================
        // Administrativo
        // ===============================
        'status',
        'observacao_admin',
        'revisado_por',
        'revisado_em',

        // ===============================
        // Campos MUITO antigos (segurança)
        // ===============================
        'nome',
        'discord',
        'id_fivem',
        'disponibilidade',
        'experiencia',
        'mensagem',
    ];

    protected $casts = [
        'revisado_em' => 'datetime',
        'rg'          => 'integer',
    ];

    /**
     * Usuário que revisou (admin / comando)
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}
