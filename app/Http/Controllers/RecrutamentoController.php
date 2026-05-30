<?php

namespace App\Http\Controllers;

use App\Models\PreInscricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecrutamentoController extends Controller
{
    public function index()
    {
        return view('publico.recrutamento');
    }

    public function cursos()
    {
        return view('publico.cursos-prf');
    }

    public function store(Request $request)
    {
        /**
         * Anti-spam (Honeypot)
         * Campo invisível no form: name="website"
         * Se vier preenchido, é bot.
         */
        if ($request->filled('website')) {
            Log::warning('PreInscricao bloqueada (honeypot)', [
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
                'at' => now()->toDateTimeString(),
            ]);

            abort(429, 'Muitas tentativas. Tente novamente mais tarde.');
        }

        $rules = [
            // Tracking
            'origem' => ['nullable', 'string', 'max:255'],

            // Pergunta 1
            'nome_completo' => ['required', 'string', 'min:5', 'max:120'],

            // RG como string numérica
            'rg' => ['required', 'regex:/^\d{1,12}$/'],

            // Pergunta 2 — Discord ID
            'discord_id' => ['required', 'regex:/^\d{6,}$/'],

            // Pergunta 3
            'possui_cnh_ab' => ['required', 'in:sim,nao'],

            // Pergunta 4
            'motivo_grr_agora' => ['required', 'string', 'min:30', 'max:1200'],

            // Pergunta 5
            'diferencial_grr' => ['required', 'string', 'min:30', 'max:1200'],

            // Pergunta 6
            'estagio_15_dias' => ['required', 'in:sim,nao'],

            // Pergunta 7
            'dias_ativo_semana' => ['required', 'in:1-2,3-4,5-6,7'],

            // Pergunta 8
            'ordem_nao_concorda' => ['required', 'in:cumpro_e_depois_questiono,questiono_no_momento,nao_cumpro'],

            // Pergunta 9
            'horario_frequente' => ['required', 'in:manha,tarde,noite,madrugada,varia'],

            // Pergunta 10
            'como_lida_frustracao' => ['required', 'string', 'min:30', 'max:1200'],

            // Pergunta 11
            'experiencia_anterior' => ['required', 'string', 'min:30', 'max:1200'],

            // Checkbox final
            'confirm_all' => ['accepted'],
        ];

        $messages = [
            'confirm_all.accepted' => 'Você precisa confirmar que preencheu todas as perguntas e está ciente das condições.',

            'nome_completo.required' => 'Informe seu nome completo (Cidade BC).',
            'nome_completo.min'      => 'O nome completo deve ter pelo menos 5 caracteres.',

            'rg.required' => 'Informe seu RG (Cidade BC).',
            'rg.regex'    => 'O RG deve conter apenas números (somente dígitos).',

            'discord_id.required' => 'Informe seu Discord ID.',
            'discord_id.regex'    => 'O Discord ID deve conter somente números e no mínimo 6 dígitos.',

            'possui_cnh_ab.required' => 'Informe se você possui CNH A e B.',
            'possui_cnh_ab.in'       => 'Selecione "Sim" ou "Não" na pergunta sobre CNH A e B.',

            'motivo_grr_agora.min'     => 'Na pergunta 4, escreva pelo menos 30 caracteres.',
            'diferencial_grr.min'      => 'Na pergunta 5, escreva pelo menos 30 caracteres.',
            'como_lida_frustracao.min' => 'Na pergunta 10, escreva pelo menos 30 caracteres.',
            'experiencia_anterior.min' => 'Na pergunta 11, escreva pelo menos 30 caracteres.',

            'estagio_15_dias.in'    => 'Selecione "Sim" ou "Não" na pergunta do estágio probatório.',
            'dias_ativo_semana.in'  => 'Selecione uma opção válida na pergunta de dias ativos.',
            'ordem_nao_concorda.in' => 'Selecione uma opção válida na pergunta sobre ordem.',
            'horario_frequente.in'  => 'Selecione uma opção válida na pergunta de horário.',
        ];

        $data = $request->validate($rules, $messages);

        // não precisa salvar checkbox
        unset($data['confirm_all']);

        // Normaliza espaços
        $data['nome_completo'] = trim((string) ($data['nome_completo'] ?? ''));
        $data['rg']            = trim((string) ($data['rg'] ?? ''));
        $data['discord_id']    = preg_replace('/\D+/', '', (string) ($data['discord_id'] ?? ''));

        /**
         * Compatibilidade com tabela antiga
         */
        $data['nome']    = $data['nome_completo']; // legado
        $data['qra_rg']  = $data['nome_completo'] . ' — RG: ' . $data['rg'];
        $data['discord'] = $data['discord_id'];    // legado

        /**
         * Blindagem para migration antiga (NOT NULL)
         */
        $data['disponibilidade'] = $data['horario_frequente'] ?? 'nao_informado';
        $data['experiencia']     = $data['experiencia'] ?? 'recrutamento_2026';

        Log::info('PreInscricao enviada', [
            'ip'            => $request->ip(),
            'ua'            => $request->userAgent(),
            'at'            => now()->toDateTimeString(),
            'origem'        => $data['origem'] ?? null,
            'nome_completo' => $data['nome_completo'] ?? null,
            'rg'            => $data['rg'] ?? null,
            'discord_id'    => $data['discord_id'] ?? null,
            'possui_cnh_ab' => $data['possui_cnh_ab'] ?? null,
        ]);

        try {
            PreInscricao::create([
                ...$data,
                'status'     => 'pendente',
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Falha ao salvar PreInscricao', [
                'message' => $e->getMessage(),
                'ip'      => $request->ip(),
                'ua'      => $request->userAgent(),
                'data'    => [
                    'origem'        => $data['origem'] ?? null,
                    'nome_completo' => $data['nome_completo'] ?? null,
                    'rg'            => $data['rg'] ?? null,
                    'discord_id'    => $data['discord_id'] ?? null,
                    'possui_cnh_ab' => $data['possui_cnh_ab'] ?? null,
                ],
            ]);

            return back()
                ->withInput()
                ->with('error', 'Não foi possível enviar sua pré-inscrição agora. Tente novamente em alguns minutos.');
        }

        return back()->with('ok', 'Pré-inscrição enviada com sucesso! Aguarde contato via Discord.');
    }
}