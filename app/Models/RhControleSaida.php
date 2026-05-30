<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RhControleSaida extends Model
{
    use HasFactory;

    protected $table = 'rh_controle_saidas';

    protected $fillable = [
        'hierarquia_id',
        'cpf',
        'nome',
        'cargo',
        'efetivacao',
        'status',
        'admissao',
        'ultima_promocao',
        'serial',
        'discord_id',

        'saida_em',
        'motivo_saida',
        'motivo_detalhe',

        'created_by',
    ];

    protected $casts = [
        'admissao' => 'date',
        'ultima_promocao' => 'date',
        'saida_em' => 'datetime',
    ];
}
