<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;

class RhHierarquiaRecord extends Model
{
    protected $table = 'rh_hierarquia_records';

    protected $fillable = [
        // vínculo
        'user_id',

        // dados principais (alguns sincronizados)
        'cpf',
        'nome',
        'equipe',
        'cargo',

        // situação funcional
        'efetivacao',
        'status',
        'admissao',
        'ultima_promocao',

        // identificação
        'serial',
        'discord_id',
        'funcao_obs',

        // flags
        'instrutor',

        // qualificações
        'pop',
        'clt',
        'cap',
        'ctb',
        'cta',
        'satb',
        'bopm',
        'gmp',
        'doa',

        // extras
        'medalhas',
        'alinhamento',

        // auditoria
        'updated_by',
    ];

    protected $casts = [
        'user_id'    => 'integer',
        'updated_by' => 'integer',

        'admissao'        => 'date',
        'ultima_promocao' => 'date',

        'instrutor' => 'boolean',

        'pop'  => 'boolean',
        'clt'  => 'boolean',
        'cap'  => 'boolean',
        'ctb'  => 'boolean',
        'cta'  => 'boolean',
        'satb' => 'boolean',
        'bopm' => 'boolean',
        'gmp'  => 'boolean',
        'doa'  => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (dados automáticos do EFETIVO)
    |--------------------------------------------------------------------------
    */

    /**
     * RG sempre vem do User (fonte da verdade)
     */
    public function getRgAttribute(): ?string
    {
        return $this->user?->rg;
    }

    /**
     * Avatar do usuário (foto)
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->user) {
            return (string) $this->user->avatar_url;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode((string) $this->nome) . '&size=256';
    }

    /**
     * Nome sincronizado (prioriza User)
     */
    public function getNomeSyncAttribute(): string
    {
        return (string) ($this->user?->name ?? $this->nome ?? '');
    }

    /**
     * Cargo sincronizado (prioriza User)
     */
    public function getCargoSyncAttribute(): ?string
    {
        return $this->user?->cargo ?? $this->cargo;
    }

    /*
    |--------------------------------------------------------------------------
    | HIERARQUIA / GRUPOS (Diretoria x Oficiais)
    |--------------------------------------------------------------------------
    */

    /**
     * Normaliza cargo (remove acento, símbolos, padroniza espaços)
     */
    public function normalizeCargo(?string $cargo): string
    {
        $c = trim((string) $cargo);

        $c = Str::of($c)->lower()->ascii()->__toString();
        $c = str_replace(['º', '°', 'ª'], '', $c);
        $c = str_replace(['-', '_', '.'], ' ', $c);
        $c = preg_replace('/\s+/', ' ', $c);

        // normalizações úteis
        $c = str_replace('vice diretor', 'vice diretor', $c); // mantém
        $c = str_replace('vice-diretor', 'vice diretor', $c);
        $c = str_replace('agente 1 classe', 'agente de 1', $c);
        $c = str_replace('agente 2 classe', 'agente de 2', $c);
        $c = str_replace('agente 3 classe', 'agente de 3', $c);
        $c = str_replace('agente 1o classe', 'agente de 1', $c);
        $c = str_replace('agente 2o classe', 'agente de 2', $c);
        $c = str_replace('agente 3o classe', 'agente de 3', $c);

        return trim($c);
    }

    /**
     * Grupo hierárquico para separação visual
     */
    public function getGrupoHierarquiaAttribute(): string
    {
        $c = $this->normalizeCargo($this->cargo_sync);

        if (
            str_contains($c, 'diretor') ||
            str_contains($c, 'vice diretor') ||
            str_contains($c, 'coordenador') ||
            str_contains($c, 'superintendente') ||
            str_contains($c, 'inspetor')
        ) {
            return 'diretoria';
        }

        return 'oficial';
    }

    /**
     * Ordem hierárquica (para ordenar corretamente)
     * menor = mais alto
     */
    public function getOrdemHierarquiaAttribute(): int
    {
        $c = $this->normalizeCargo($this->cargo_sync);

        // Diretoria
        if (str_contains($c, 'diretor') && !str_contains($c, 'vice diretor')) return 1;
        if (str_contains($c, 'vice diretor')) return 2;
        if (str_contains($c, 'coordenador')) return 3;
        if (str_contains($c, 'superintendente')) return 4;
        if (str_contains($c, 'inspetor')) return 5;

        // Oficiais
        if (str_contains($c, 'agente especial')) return 10;
        if (str_contains($c, 'agente de 1')) return 11;
        if (str_contains($c, 'agente de 2')) return 12;
        if (str_contains($c, 'agente de 3')) return 13;
        if (str_contains($c, 'aluno')) return 20;

        return 99;
    }
}
