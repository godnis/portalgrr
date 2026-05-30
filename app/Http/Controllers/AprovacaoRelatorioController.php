<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use App\Models\User;
use Illuminate\Http\Request;

class AprovacaoRelatorioController extends Controller
{
    public function index(Request $request)
    {
        $q = Relatorio::query()
            ->where('status', 'pendente')
            ->latest();

        // filtro por unidade
        if ($request->filled('unidade')) {
            $q->where('unidade', $request->unidade);
        }

        // filtro por data
        if ($request->filled('data_inicio')) {
            $q->whereDate('data_patrulhamento', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $q->whereDate('data_patrulhamento', '<=', $request->data_fim);
        }

        // busca por RG (chefe da barca qra_chefe ou qualquer membro da guarnição)
        if ($request->filled('rg')) {
            $rg = trim($request->rg);

            $q->where(function ($sub) use ($rg) {
                $sub->where('qra_chefe', $rg)
                    ->orWhere('motorista', $rg)
                    ->orWhere('terceiro', $rg)
                    ->orWhere('quarto', $rg)
                    ->orWhere('quinto', $rg);
            });
        }

        // final_patrulhamento preenchido? (opcional: mostrar só "prontos")
        if ($request->get('prontos') === '1') {
            $q->whereNotNull('final_patrulhamento');
        }

        $relatorios = $q->paginate(20)->withQueryString();

        $unidades = [
            'Administrativo',
            'GRR-01 CMD',
            'GRR-02 CRD',
            'GRR-03 SUP-A',
            'GRR-04 SUP-B',
            'GRR-05',
            'GRR-06',
            'GRR-10',
            'GRR-11',
            'GRR-15',
            'GRR-16',
            'GRR-17',
            'GRR-18',
        ];

        return view('aprovacao.relatorios.index', compact('relatorios', 'unidades'));
    }

    public function show(Relatorio $relatorio)
    {
        // Quem é o autor? (melhora o detalhe)
        $autor = User::find($relatorio->user_id);

        return view('aprovacao.relatorios.show', compact('relatorio', 'autor'));
    }
}
