<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhPermission extends Model
{
    protected $table = 'rh_permissions';

    /**
     * Campos liberados para mass assignment
     */
    protected $fillable = [
        'user_id',
        'passaporte',

        // Permissões (edição) — mesmas da tela
        'can_hierarquia',
        'can_controle_saida',
        'can_estatistica_efetivo',
        'can_instrutores',
        'can_equipe',

        // Observações administrativas
        'observacao',

        // Auditoria
        'updated_by',
    ];

    /**
     * Casts automáticos
     */
    protected $casts = [
        'can_hierarquia'          => 'boolean',
        'can_controle_saida'      => 'boolean',
        'can_estatistica_efetivo' => 'boolean',
        'can_instrutores'         => 'boolean',
        'can_equipe'              => 'boolean',
    ];

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuário que atualizou a permissão (auditoria)
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Helper para mapear seção → campo no banco
     * Usado por middleware, policies e controllers
     */
    public static function fieldFor(string $section): ?string
    {
        return match ($section) {
            'hierarquia'          => 'can_hierarquia',
            'controle_saida'      => 'can_controle_saida',
            'estatistica_efetivo' => 'can_estatistica_efetivo',
            'instrutores'         => 'can_instrutores',
            'equipe'              => 'can_equipe',
            default               => null,
        };
    }

    /**
     * Verifica se o usuário pode editar determinada seção
     */
    public function canEdit(string $section): bool
    {
        $field = self::fieldFor($section);
        return $field ? (bool) ($this->{$field} ?? false) : false;
    }
}
