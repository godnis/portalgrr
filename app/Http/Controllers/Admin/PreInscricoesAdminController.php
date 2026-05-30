<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreInscricao;
use App\Services\AuditoriaLogger;
use Illuminate\Http\Request;

class PreInscricoesAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = PreInscricao::query();

        /**
         * ✅ Status
         * - Seu <option value="">Todos</option> já está certo
         * - Mesmo assim, blindamos caso alguém mande "todos"/"all" via URL
         */
        $status = trim((string) $request->input('status', ''));
        $statusLower = mb_strtolower($status);

        if ($status !== '' && !in_array($statusLower, ['todos', 'todo', 'all'], true)) {
            $query->where('status', $status);
        }

        /**
         * ✅ Busca (mais completa e compatível)
         * - qra_rg (legado/admin)
         * - nome_completo / nome (legado)
         * - rg (string)
         * - discord_id / discord (legado)
         * - ip
         */
        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));

            $query->where(function ($w) use ($term) {
                $w->where('qra_rg', 'like', "%{$term}%")
                    ->orWhere('nome_completo', 'like', "%{$term}%")
                    ->orWhere('nome', 'like', "%{$term}%")
                    ->orWhere('rg', 'like', "%{$term}%")
                    ->orWhere('discord_id', 'like', "%{$term}%")
                    ->orWhere('discord', 'like', "%{$term}%")
                    ->orWhere('ip', 'like', "%{$term}%");
            });
        }

        /**
         * ✅ Ordenação
         */
        $ord = $request->input('ord', 'desc') === 'asc' ? 'asc' : 'desc';

        $perPage = 20;

        $preInscricoes = $query
            ->orderBy('created_at', $ord)
            ->paginate($perPage)
            ->withQueryString();

        AuditoriaLogger::log(
            'preinscricao_admin_index_aberto',
            auth()->id(),
            'PreInscricao',
            null,
            [
                'per_page' => $perPage,
                'ord'      => $ord,
                'filtros'  => [
                    'status' => $status,
                    'q'      => $request->input('q'),
                ],
            ],
            $request
        );

        return view('admin.pre-inscricoes.index', compact('preInscricoes'));
    }

    public function show(Request $request, PreInscricao $preInscricao)
    {
        AuditoriaLogger::log(
            'preinscricao_admin_show_aberto',
            auth()->id(),
            'PreInscricao',
            $preInscricao->id,
            [
                'status'     => $preInscricao->status,
                'qra_rg'     => $preInscricao->qra_rg ?? null,
                'discord_id' => $preInscricao->discord_id ?? null,
                'discord'    => $preInscricao->discord ?? null,
                'ip'         => $preInscricao->ip ?? null,
            ],
            $request
        );

        return view('admin.pre-inscricoes.show', compact('preInscricao'));
    }

    public function updateStatus(Request $request, PreInscricao $preInscricao)
    {
        $data = $request->validate([
            'status'           => ['required', 'in:aprovado,reprovado'],
            'observacao_admin' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'status.required' => 'Selecione aprovar ou reprovar.',
            'status.in' => 'Status inválido.',
            'observacao_admin.required' => 'A observação é obrigatória.',
            'observacao_admin.min' => 'A observação deve ter pelo menos 5 caracteres.',
            'observacao_admin.max' => 'A observação pode ter no máximo 2000 caracteres.',
        ]);

        $antes = [
            'status'       => $preInscricao->status,
            'revisado_por' => $preInscricao->revisado_por,
            'revisado_em'  => optional($preInscricao->revisado_em)->toDateTimeString(),
        ];

        $preInscricao->update([
            'status'           => $data['status'],
            'observacao_admin' => $data['observacao_admin'],
            'revisado_por'     => auth()->id(),
            'revisado_em'      => now(),
        ]);

        $preInscricao->refresh();

        AuditoriaLogger::log(
            'preinscricao_admin_status_alterado',
            auth()->id(),
            'PreInscricao',
            $preInscricao->id,
            [
                'antes' => $antes,
                'depois' => [
                    'status'       => $preInscricao->status,
                    'revisado_por' => $preInscricao->revisado_por,
                    'revisado_em'  => optional($preInscricao->revisado_em)->toDateTimeString(),
                ],
                'obs_chars'  => mb_strlen((string) $data['observacao_admin']),
                'qra_rg'     => $preInscricao->qra_rg ?? null,
                'discord_id' => $preInscricao->discord_id ?? null,
                'discord'    => $preInscricao->discord ?? null,
            ],
            $request
        );

        return back()->with('success', 'Pré-inscrição atualizada com sucesso.');
    }
}
