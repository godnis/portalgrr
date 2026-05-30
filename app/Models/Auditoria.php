<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $fillable = [
        'request_id',

        'user_id',
        'actor_rg',
        'actor_nome',

        'acao',

        'entidade_tipo',
        'entidade_id',

        'alvo_user_id',
        'alvo_rg',
        'alvo_nome',

        'route_name',
        'method',
        'url',

        'detalhes',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'detalhes' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
