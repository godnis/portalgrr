<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ✅ RH Permissões
use App\Models\RhPermission;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * ✅ Campos permitidos para update em massa (fill / update / create)
     */
    protected $fillable = [
        'name',
        'email',
        'discord',
        'password',

        // Efetivo
        'rg',
        'cargo',
        'nivel',
        'ativo',   // legado/compat
        'status',  // fonte da verdade do efetivo (ativo|suspenso|desligado)

        // Perfil
        'telefone',
        'bio',
        'tema',
        'avatar_path',
    ];

    /**
     * Campos ocultos
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'ativo'             => 'boolean',
            'nivel'             => 'integer',
        ];
    }

    // =========================================================
    // ✅ RH • PERMISSÕES (VISUALIZAR x EDITAR)
    // =========================================================

    /**
     * Permissão RH do usuário (1:1)
     */
    public function rhPermission()
    {
        return $this->hasOne(RhPermission::class, 'user_id');
    }

    /**
     * ✅ Pode EDITAR uma seção do RH
     * - nível 9+ sempre pode
     * - outros dependem da tabela rh_permissions
     *
     * Exemplos:
     *  - hierarquia
     *  - controle_saida
     *  - estatistica_efetivo
     *  - instrutores
     *  - equipe
     */
    public function canEditRh(string $section): bool
    {
        // Admin RH sempre pode
        if ($this->temNivel(9)) {
            return true;
        }

        $perm = $this->rhPermission;
        if (!$perm) {
            return false;
        }

        $field = RhPermission::fieldFor($section);
        if (!$field) {
            return false;
        }

        return (bool) ($perm->{$field} ?? false);
    }

    /**
     * ✅ Pode VISUALIZAR RH?
     * - TODO usuário logado pode visualizar
     * (regra institucional que você pediu)
     */
    public function canViewRh(): bool
    {
        return true;
    }

    /**
     * ===== Atalhos úteis =====
     */
    public function canEditHierarquia(): bool
    {
        return $this->canEditRh('hierarquia');
    }

    public function canEditControleSaida(): bool
    {
        return $this->canEditRh('controle_saida');
    }

    public function canEditEstatisticaEfetivo(): bool
    {
        return $this->canEditRh('estatistica_efetivo');
    }

    public function canEditInstrutores(): bool
    {
        return $this->canEditRh('instrutores');
    }

    public function canEditEquipe(): bool
    {
        return $this->canEditRh('equipe');
    }

    // =========================================================
    // ✅ Helpers do Efetivo / Perfil
    // =========================================================

    /**
     * Fonte da verdade do efetivo: status
     * Mantém compatibilidade com campo legado "ativo"
     */
    public function isAtivo(): bool
    {
        if (!is_null($this->status)) {
            return (string) $this->status === 'ativo';
        }

        return (bool) $this->ativo;
    }

    public function temNivel(int $nivel): bool
    {
        return (int) ($this->nivel ?? 0) >= $nivel;
    }

    /**
     * Scopes úteis
     */
    public function scopeAtivos($q)
    {
        return $q->where('status', 'ativo');
    }

    public function scopeSuspensos($q)
    {
        return $q->where('status', 'suspenso');
    }

    public function scopeDesligados($q)
    {
        return $q->where('status', 'desligado');
    }

    /**
     * Accessor: $user->avatar_url
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar_path)) {
            return asset('storage/' . ltrim((string) $this->avatar_path, '/'));
        }

        $defaultLocal = public_path('images/avatar-default.png');
        if (file_exists($defaultLocal)) {
            return asset('images/avatar-default.png');
        }

        return 'https://ui-avatars.com/api/?name='
            . urlencode((string) $this->name)
            . '&size=256';
    }

    /**
     * Compatibilidade antiga
     */
    public function avatarUrl(): string
    {
        return $this->avatar_url;
    }

    /**
     * Label do status (UI)
     */
    public function statusLabel(): string
    {
        return match ((string) $this->status) {
            'ativo'     => 'Ativo',
            'suspenso'  => 'Suspenso',
            'desligado' => 'Desligado',
            'aprovado'  => 'Aprovado',
            'pendente'  => 'Pendente',
            'reprovado' => 'Reprovado',
            default     => ucfirst((string) $this->status),
        };
    }
}
