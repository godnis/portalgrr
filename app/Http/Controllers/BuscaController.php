<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuscaController extends Controller
{
    public function index(Request $request)
    {
        // 🔎 Você pode implementar a busca real depois.
        // Por enquanto, só renderiza a página/resultado.

        $q = (string) $request->query('q', '');

        // Se você já tem uma view de busca, coloque aqui:
        // return view('publico.buscar', compact('q'));

        // Se ainda não tem, dá pra reaproveitar uma view simples:
        return view('publico.buscar', [
            'q' => $q,
        ]);
    }
}
