<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    protected $table = 'tickets';

    // ===== ENUMS / PADRÕES =====
    public const STATUSES = [
        'aberto',
        'em_andamento',
        'aguardando_usuario',
        'resolvido',
        'fechado',
    ];

    public const CATEGORIES = [
        'suporte_geral',
        'administrativo',
        'denuncia',
        'financeiro',
        'tecnico',
        'recrutamento',
    ];

    public const PRIORITIES = [
        'baixa',
        'normal',
        'alta',
        'urgente',
    ];

    // Labels para UI (centralizado)
    public const STATUS_LABELS = [
        'aberto'             => 'Aberto',
        'em_andamento'       => 'Em andamento',
        'aguardando_usuario' => 'Aguardando usuário',
        'resolvido'          => 'Resolvido',
        'fechado'            => 'Fechado',
    ];

    public const CATEGORY_LABELS = [
        'suporte_geral'  => 'Suporte geral',
        'tecnico'        => 'Técnico',
        'administrativo' => 'Administrativo',
        'recrutamento'   => 'Recrutamento',
        'financeiro'     => 'Financeiro',
        'denuncia'       => 'Denúncia',
    ];

    public const PRIORITY_LABELS = [
        'baixa'   => 'Baixa',
        'normal'  => 'Normal',
        'alta'    => 'Alta',
        'urgente' => 'Urgente',
    ];

    // Bootstrap badge
    public const STATUS_BADGE = [
        'aberto'             => 'secondary',
        'em_andamento'       => 'primary',
        'aguardando_usuario' => 'warning',
        'resolvido'          => 'success',
        'fechado'            => 'dark',
    ];

    public const PRIORITY_BADGE = [
        'baixa'   => 'secondary',
        'normal'  => 'info',
        'alta'    => 'warning',
        'urgente' => 'danger',
    ];

    protected $fillable = [
        'user_id',
        'assigned_to',

        'categoria',
        'prioridade',
        'titulo',
        'descricao',

        'status',

        // auditoria
        'ip',
        'user_agent',

        // lifecycle
        'fechado_em',

        // leitura / UX
        'user_last_read_at',
        'admin_last_read_at',
        'last_message_at',
    ];

    protected $casts = [
        'fechado_em'         => 'datetime',
        'user_last_read_at'  => 'datetime',
        'admin_last_read_at' => 'datetime',
        'last_message_at'    => 'datetime',
    ];

    // ===== RELAÇÕES =====

    /** Dono do ticket */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Responsável (admin que assumiu) */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Mensagens em ordem cronológica (antigo -> novo) */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')
            ->orderBy('created_at', 'asc');
    }

    // ===== HELPERS =====

    public function isClosed(): bool
    {
        return in_array((string) $this->status, ['fechado', 'resolvido'], true);
    }

    public function isOpen(): bool
    {
        return !$this->isClosed();
    }

    /** Última atividade real (chat) ou fallback (created_at) */
    public function lastActivityAt(): ?Carbon
    {
        if ($this->last_message_at instanceof Carbon) {
            return $this->last_message_at;
        }

        return $this->created_at instanceof Carbon ? $this->created_at : null;
    }

    public function statusLabel(): string
    {
        $s = (string) ($this->status ?? 'aberto');
        return self::STATUS_LABELS[$s] ?? strtoupper(str_replace('_', ' ', $s));
    }

    public function statusBadge(): string
    {
        $s = (string) ($this->status ?? 'aberto');
        return self::STATUS_BADGE[$s] ?? 'secondary';
    }

    public function categoriaLabel(): string
    {
        $c = (string) ($this->categoria ?? '');
        return self::CATEGORY_LABELS[$c] ?? ucfirst(str_replace('_', ' ', $c));
    }

    public function prioridadeLabel(): string
    {
        $p = (string) ($this->prioridade ?? 'normal');
        return self::PRIORITY_LABELS[$p] ?? strtoupper($p);
    }

    public function prioridadeBadge(): string
    {
        $p = (string) ($this->prioridade ?? 'normal');
        return self::PRIORITY_BADGE[$p] ?? 'info';
    }

    // ===== UNREAD =====

    /** Não lidas para usuário */
    public function userHasUnread(): bool
    {
        if (!$this->last_message_at instanceof Carbon) return false;
        if (!$this->user_last_read_at instanceof Carbon) return true;
        return $this->last_message_at->gt($this->user_last_read_at);
    }

    /** Não lidas para admin */
    public function adminHasUnread(): bool
    {
        if (!$this->last_message_at instanceof Carbon) return false;
        if (!$this->admin_last_read_at instanceof Carbon) return true;
        return $this->last_message_at->gt($this->admin_last_read_at);
    }

    // ===== SCOPES =====

    public function scopeAbertos($query)
    {
        return $query->whereNotIn('status', ['fechado', 'resolvido']);
    }

    public function scopeFechados($query)
    {
        return $query->whereIn('status', ['fechado', 'resolvido']);
    }

    /** Ordena por última atividade do chat (mais útil pro painel) */
    public function scopeOrderByLastActivityDesc($query)
    {
        return $query->orderByDesc(\DB::raw('COALESCE(last_message_at, created_at)'));
    }
}