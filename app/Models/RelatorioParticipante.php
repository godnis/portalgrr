<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatorioParticipante extends Model
{
    protected $table = 'relatorio_participantes';

    protected $fillable = [
        'relatorio_id',
        'user_id',
        'papel',
    ];
}
