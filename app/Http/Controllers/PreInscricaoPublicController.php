<?php

namespace App\Http\Controllers;

use App\Models\PreInscricao;
use Illuminate\Http\Request;

class PreInscricaoPublicController extends Controller
{
    public function store(Request $request)
    {
        // ajuste os campos conforme seu formulário real
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'rg' => ['nullable', 'string', 'max:50'],
            'telefone' => ['nullable', 'string', 'max:30'],

            // exemplo: tudo que vier como respostas[...]
            'respostas' => ['nullable', 'array'],
        ]);

        $user = auth()->user();

        PreInscricao::create([
            'user_id' => $user?->id,
            'nome' => $data['nome'] ?? $user?->name,
            'rg' => $data['rg'] ?? $user?->rg,
            'telefone' => $data['telefone'] ?? $user?->telefone,
            'respostas' => $data['respostas'] ?? $request->except(['_token']),
            'status' => 'pendente',
        ]);

        // se você tiver seu AuditoriaLogger, dá pra registrar aqui também
        // AuditoriaLogger::log('preinscricao_enviada', [...]);

        return back()->with('success', 'Pré-inscrição enviada. Aguarde avaliação do Administrativo.');
    }
}
