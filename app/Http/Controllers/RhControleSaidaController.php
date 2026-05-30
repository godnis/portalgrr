<?php

namespace App\Http\Controllers;

use App\Models\RhControleSaida;
use App\Models\RhHierarquiaRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Support\Rh;

class RhControleSaidaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->check(), 403);

        $q = trim((string) $request->get('q', ''));

        $from = trim((string) $request->get('from', ''));
        $to   = trim((string) $request->get('to', ''));

        $fromDate = null;
        $toDate   = null;

        if ($from !== '') {
            try {
                $fromDate = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            } catch (\Throwable $e) {}
        }

        if ($to !== '') {
            try {
                $toDate = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
            } catch (\Throwable $e) {}
        }

        $fromDate = $fromDate ?: now()->subDays(30)->startOfDay();
        $toDate   = $toDate   ?: now()->endOfDay();

        $from = $fromDate->toDateString();
        $to   = $toDate->toDateString();

        $rows = RhControleSaida::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nome', 'like', "%{$q}%")
                        ->orWhere('cpf', 'like', "%{$q}%")
                        ->orWhere('cargo', 'like', "%{$q}%")
                        ->orWhere('serial', 'like', "%{$q}%")
                        ->orWhere('discord_id', 'like', "%{$q}%")
                        ->orWhere('motivo_saida', 'like', "%{$q}%")
                        ->orWhere('motivo_detalhe', 'like', "%{$q}%");
                });
            })
            ->whereBetween('saida_em', [$fromDate, $toDate])
            ->orderByDesc('saida_em')
            ->paginate(50)
            ->appends($request->all());

        // ✅ mostra apenas militares ainda ativos para registrar saída
        $militares = RhHierarquiaRecord::query()
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'desligado');
            })
            ->orderBy('nome')
            ->get();

        $canEdit = Rh::canEdit(auth()->user(), 'controle_saida');

        return view('rh.controle-saida', compact(
            'rows',
            'militares',
            'q',
            'canEdit',
            'from',
            'to',
            'fromDate',
            'toDate'
        ));
    }

    public function store(Request $request)
    {
        abort_unless(Rh::canEdit(auth()->user(), 'controle_saida'), 403, 'Somente leitura.');

        $data = $request->validate([
            'hierarquia_id'  => 'required|integer|exists:rh_hierarquia_records,id',
            'saida_em'       => 'required|date',
            'motivo_saida'   => 'required|string|max:120',
            'motivo_detalhe' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $h = RhHierarquiaRecord::lockForUpdate()->findOrFail($data['hierarquia_id']);

            // ✅ evita registrar saída duplicada de alguém já desligado
            if (($h->status ?? null) === 'desligado') {
                abort(422, 'Este militar já está desligado do efetivo.');
            }

            RhControleSaida::create([
                'hierarquia_id'   => $h->id,
                'cpf'             => $h->cpf,
                'nome'            => $h->nome,
                'cargo'           => $h->cargo,
                'efetivacao'      => $h->efetivacao,
                'status'          => $h->status,
                'admissao'        => $h->admissao,
                'ultima_promocao' => $h->ultima_promocao,
                'serial'          => $h->serial,
                'discord_id'      => $h->discord_id,
                'saida_em'        => $data['saida_em'],
                'motivo_saida'    => $data['motivo_saida'],
                'motivo_detalhe'  => $data['motivo_detalhe'] ?? null,
                'created_by'      => auth()->id(),
            ]);

            // ✅ baixa automática do efetivo
            $h->update([
                'status' => 'desligado',
            ]);
        });

        return redirect()
            ->route('rh.controle_saida')
            ->with('success', 'Saída registrada e militar removido do efetivo ativo.');
    }

    public function destroy(RhControleSaida $row)
    {
        abort_unless(Rh::canEdit(auth()->user(), 'controle_saida'), 403, 'Somente leitura.');

        $row->delete();

        return back()->with('success', 'Registro removido.');
    }
}