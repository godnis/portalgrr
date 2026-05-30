<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Atendimento;
use App\Services\AuditoriaLogger;

class AtendimentoController extends Controller
{
    /**
     * PUBLICO: recebe envio do modal do portal
     */
    public function enviar(Request $request)
    {
        $data = $request->validate([
            'tipo'        => 'required|string|max:30',
            'nome'        => 'nullable|string|max:80',
            'contato'     => 'nullable|string|max:120',
            'assunto'     => 'required|string|max:80',
            'mensagem'    => 'required|string|max:1500',
            'prova_url'   => 'nullable|url|max:255',
            'redirect_to' => 'nullable|string|max:255',
        ]);

        $atendimento = Atendimento::create([
            ...collect($data)->except('redirect_to')->toArray(),
            'status'     => 'aberto',
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        AuditoriaLogger::log(
            'atendimento_publico_enviado',
            auth()->id(),
            'Atendimento',
            $atendimento->id,
            [
                'tipo'           => $atendimento->tipo,
                'assunto'        => $atendimento->assunto,
                'has_nome'       => !empty($atendimento->nome),
                'has_contato'    => !empty($atendimento->contato),
                'has_prova'      => !empty($atendimento->prova_url),
                'mensagem_chars' => mb_strlen((string) $data['mensagem']),
                'redirect_to'    => $data['redirect_to'] ?? null,
            ],
            $request
        );

        $redirect = $request->input('redirect_to');

        return redirect()->to($redirect ?: route('publico.home'))
            ->with('success', 'Mensagem enviada com sucesso. Obrigado pelo contato!');
    }

    /**
     * INTERNO (restrito): lista atendimentos
     * Permissão: middleware hierarquia:7+ nas rotas
     */
    public function index(Request $request)
    {
        $q = Atendimento::query();

        if ($request->filled('status')) {
            $q->where('status', $request->get('status'));
        }

        if ($request->filled('tipo')) {
            $q->where('tipo', $request->get('tipo'));
        }

        if ($request->filled('busca')) {
            $b = (string) $request->get('busca');
            $q->where(function ($w) use ($b) {
                $w->where('assunto', 'like', "%{$b}%")
                  ->orWhere('mensagem', 'like', "%{$b}%")
                  ->orWhere('nome', 'like', "%{$b}%")
                  ->orWhere('contato', 'like', "%{$b}%");
            });
        }

        $perPage = 15;
        $atendimentos = $q->latest()->paginate($perPage)->withQueryString();

        AuditoriaLogger::log(
            'atendimento_index_aberto',
            auth()->id(),
            'Atendimento',
            null,
            [
                'per_page' => $perPage,
                'filtros'  => $request->only(['status', 'tipo', 'busca']),
            ],
            $request
        );

        return view('atendimentos.index', compact('atendimentos'));
    }

    /**
     * INTERNO (restrito): ver detalhe
     */
    public function show(Request $request, Atendimento $atendimento)
    {
        $canViewTechnicalData = auth()->check() && (int) (auth()->user()->nivel ?? 0) >= 9;

        AuditoriaLogger::log(
            'atendimento_show_aberto',
            auth()->id(),
            'Atendimento',
            $atendimento->id,
            [
                'status'                  => $atendimento->status,
                'tipo'                    => $atendimento->tipo,
                'visualizou_dados_tecnicos' => $canViewTechnicalData,
            ],
            $request
        );

        return view('atendimentos.show', compact('atendimento', 'canViewTechnicalData'));
    }

    /**
     * INTERNO (restrito): atualizar status
     */
    public function updateStatus(Request $request, Atendimento $atendimento)
    {
        $data = $request->validate([
            'status' => 'required|in:aberto,em_analise,resolvido,arquivado',
        ]);

        $antes = (string) ($atendimento->status ?? '');

        $atendimento->update([
            'status' => $data['status'],
        ]);

        $atendimento->refresh();

        AuditoriaLogger::log(
            'atendimento_status_alterado',
            auth()->id(),
            'Atendimento',
            $atendimento->id,
            [
                'status_antes'  => $antes,
                'status_depois' => (string) $atendimento->status,
                'tipo'          => $atendimento->tipo,
            ],
            $request
        );

        return back()->with('success', 'Status atualizado com sucesso!');
    }
}