<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitacaoAcesso;
use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    public function liberarConta(Request $request)
    {
        $messages = [
            'required' => 'O campo :attribute é obrigatório.',
            'string'   => 'O campo :attribute deve ser um texto.',
            'max'      => 'O campo :attribute não pode ter mais que :max caracteres.',
        ];

        $attributes = [
            'nome'       => 'nome',
            'sobrenome'  => 'sobrenome',
            'passaporte' => 'passaporte',
            'discord'    => 'discord',
        ];

        $validator = Validator::make($request->all(), [
            'nome'       => 'required|string|max:160',
            'sobrenome'  => 'required|string|max:160',
            'passaporte' => 'required|string|max:30',
            'discord'    => 'required|string|max:120',
        ], $messages, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '❌ Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 200);
        }

        try {
            $nome = trim((string) $request->input('nome'));
            $sobrenome = trim((string) $request->input('sobrenome'));
            $rg           = preg_replace('/\D+/', '', (string) $request->input('passaporte'));
            $discord      = trim((string) $request->input('discord'));

            //USUÁRIO JÁ EXISTE, ATUALIZAR DISCORD
            $user = User::query()
                ->where('rg', $rg)
                ->first();

            if ($user) {
                if ($user->discord) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Seus dados já estão atualizados.',
                    ], 200);
                }

                if ($user->name === $nome . " " . $sobrenome) {
                    $update = User::where('rg', $rg)->update([
                        'discord' => $discord,
                        'status' => 'ativo',
                        'suspenso_em' => null,
                        'suspenso_por' => null,
                        'motivo_suspensao' => null,
                    ]);

                    if ($update) {
                        $discordApp = new DiscordService();
                        $discordApp->enviar(
                            '/discord-atualizado',
                            [
                                'passaporte' => $user->rg,
                                'nome' => $user->name,
                                'cargo' => $user->cargo,
                                'discord' => $discord,
                            ]
                        );
                    }

                    return response()->json([
                        'success' => false,
                        'message' => '✅ Liberação de acesso efetuada com sucesso.',
                    ], 200);
                }

                return response()->json([
                    'success' => false,
                    'message' => '❌ Os dados informados não correspondem ao passaporte cadastrado.',
                ], 200);
            }

            //USUÁRIO AINDA NÃO EXISTE, CRIAR SOLICITAÇÃO DE ACESSO
            if ($nome === '') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Nome inválido.',
                ], 200);
            }

            if ($sobrenome === '') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Sobrenome inválido.',
                ], 200);
            }

            if ($rg === '') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Passaporte inválido.',
                ], 200);
            }

            $nome = $this->formatarNome($nome);
            $sobrenome = $this->formatarNome($sobrenome);

            $email = $this->gerarEmailInstitucional($nome, $sobrenome);

            $existePendentePorRg = SolicitacaoAcesso::where('rg', $rg)
                ->where('status', 'pendente')
                ->exists();

            if ($existePendentePorRg) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Já existe uma solicitação pendente para este passaporte.',
                ], 200);
            }

            $existePendentePorEmail = SolicitacaoAcesso::where('email', $email)
                ->where('status', 'pendente')
                ->exists();

            if ($existePendentePorEmail) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Já existe uma solicitação pendente para este e-mail.',
                ], 200);
            }

            $existePendentePorDiscord = SolicitacaoAcesso::where('discord', $discord)
                ->where('status', 'pendente')
                ->exists();

            if ($existePendentePorDiscord) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Já existe uma solicitação pendente para este discord.',
                ], 200);
            }

            $solicitacao = SolicitacaoAcesso::create([
                'nome'       => $nome,
                'sobrenome'  => $sobrenome,
                'rg'         => $rg,
                'email'      => $email,
                'discord'    => $discord,
                'status'     => 'pendente',
                'motivo'     => 'Solicitação enviada via bot.',
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Solicitação enviada com sucesso para aprovação.',
                'data' => [
                    'id'         => $solicitacao->id,
                    'nome'       => $solicitacao->nome,
                    'sobrenome'  => $solicitacao->sobrenome,
                    'rg'         => $solicitacao->rg,
                    'email'      => $solicitacao->email,
                    'discord'    => $solicitacao->discord,
                    'status'     => $solicitacao->status,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erro interno ao enviar solicitação. ' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 200);
        }
    }

    public function suspenderConta(Request $request)
    {
        $discord = $request->input('user');

        $user = User::where('discord', $discord)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'rg' => $user->rg,
                'nome' => $user->name,
                'cargo' => $user->cargo,
                'message' => 'Usuário não encontrado.',
            ], 200);
        }

        $update = $user->update([
            'status' => 'suspenso',
            'suspenso_em' => now(),
            'motivo_suspensao' => 'O usuário desautorizou o bot.',
        ]);

        if ($update) {
            return response()->json([
                'success' => true,
                'rg' => $user->rg,
                'nome' => $user->name,
                'cargo' => $user->cargo,
                'message' => 'Acesso do usuário foi suspenso.',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'rg' => $user->rg,
            'nome' => $user->name,
            'cargo' => $user->cargo,
            'message' => 'Erro ao suspender conta.',
        ], 200);
    }

    private function formatarNome(string $valor): string
    {
        $valor = trim($valor);
        $valor = mb_strtolower($valor, 'UTF-8');

        return mb_convert_case($valor, MB_CASE_TITLE, 'UTF-8');
    }

    private function gerarEmailInstitucional(string $nome, string $sobrenome): string
    {
        $parteNome = $this->normalizarParteEmail($nome);
        $parteSobrenome = $this->normalizarParteEmail($sobrenome);

        return $parteNome . '.' . $parteSobrenome . '@grr.com';
    }

    private function normalizarParteEmail(string $valor): string
    {
        $valor = Str::ascii($valor);
        $valor = strtolower($valor);
        $valor = preg_replace('/[^a-z0-9]+/', '.', $valor);
        $valor = preg_replace('/\.+/', '.', $valor);
        $valor = trim($valor, '.');

        return $valor !== '' ? $valor : 'usuario';
    }
}
