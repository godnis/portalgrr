<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    protected $fillable = [
        'tipo','assunto','mensagem','nome','contato','prova_url',
        'status','ip','user_agent'
    ];
}
