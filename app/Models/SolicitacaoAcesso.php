<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitacaoAcesso extends Model
{
    use HasFactory;

    protected $table = 'solicitacao_acessos';

    protected $fillable = [
        'nome',
        'sobrenome',
        'rg',
        'email',
        'discord',
        'status',
        'motivo',
        'ip',
        'user_agent',
        'decidido_por',
        'decidido_em',
    ];

    protected $casts = [
        'decidido_por' => 'integer',
        'decidido_em'  => 'datetime',
    ];
}