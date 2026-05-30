<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuditoriaLogger;

class AuditarAcessoMiddleware
{
    /**
     * Audita acesso às páginas (GET) por nome de rota.
     * Evita spam usando "debounce" via session (rota + alvo).
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // só audita se autenticado
        $userId = auth()->id();
        if (!$userId) return $response;

        // só GET (abrir página)
        if (!$request->isMethod('GET')) return $response;

        /**
         * ✅ evita spam/ruído:
         * - não audita ajax/json
         * - não audita pré-carregamentos
         */
        if ($request->ajax() || $request->wantsJson()) return $response;

        $purpose = (string) $request->headers->get('purpose');
        $secPurpose = (string) $request->headers->get('sec-purpose');
        if (stripos($purpose, 'prefetch') !== false || stripos($secPurpose, 'prefetch') !== false) {
            return $response;
        }

        $route = $request->route();
        $routeName = $route?->getName();
        if (!$routeName) return $response;

        /**
         * ✅ Map: rota => ação de auditoria
         * Coloque aqui tudo o que você quer auditar como "abriu tela".
         */
        $map = [
            // =========================
            // Efetivo
            // =========================
            'efetivo.index'  => 'efetivo_index_aberto',
            'efetivo.create' => 'efetivo_create_aberto',
            'efetivo.show'   => 'efetivo_show_aberto',
            'efetivo.edit'   => 'efetivo_edit_aberto',

            // =========================
            // Canais de Atendimento
            // =========================
            'atendimentos.index' => 'atendimento_index_aberto',
            'atendimentos.show'  => 'atendimento_show_aberto',

            // =========================
            // Tickets (usuário)
            // =========================
            'tickets.index'  => 'tickets_index_aberto',
            'tickets.create' => 'tickets_create_aberto',
            'tickets.show'   => 'tickets_show_aberto',

            // =========================
            // Tickets (Admin)
            // =========================
            'admin.tickets.index' => 'tickets_admin_index_aberto',
            'admin.tickets.show'  => 'tickets_admin_show_aberto',

            // =========================
            // Pré-inscrições (Admin)
            // =========================
            'admin.preinscricoes.index' => 'preinscricoes_index_aberto',
            'admin.preinscricoes.show'  => 'preinscricoes_show_aberto',
        ];

        if (!isset($map[$routeName])) return $response;

        /**
         * ✅ Descobre "alvo" (entidade_id) quando fizer sentido.
         */
        [$entidadeTipo, $targetId, $targetRg, $targetNome] = $this->resolveTarget($routeName, $route);

        // ✅ debounce: evita spam no refresh/voltar/abrir repetido
        $key  = 'audit_last:' . $map[$routeName] . ':' . ($targetId ?? 0);
        $last = (int) $request->session()->get($key, 0);

        // janela de 5 segundos
        if (time() - $last < 5) return $response;
        $request->session()->put($key, time());

        $actor = auth()->user();

        AuditoriaLogger::log(
            $map[$routeName],
            $userId,
            $entidadeTipo ?? 'Sistema',
            $targetId,
            [
                // ✅ ator (padronizado)
                'actor_user_id' => $userId,
                'actor_rg'      => $actor?->rg ?? null,
                'actor_nome'    => $actor?->name ?? null,

                // ✅ alvo (padronizado quando existir)
                'alvo_user_id'  => $targetId, // mantém o mesmo nome por padrão (mesmo não sendo User)
                'alvo_rg'       => $targetRg,
                'alvo_nome'     => $targetNome,
            ],
            $request
        );

        return $response;
    }

    /**
     * Resolve alvo conforme a rota.
     * Retorna: [entidadeTipo, targetId, targetRg, targetNome]
     */
    private function resolveTarget(string $routeName, $route): array
    {
        $entidadeTipo = null;
        $targetId = null;
        $targetRg = null;
        $targetNome = null;

        // Efetivo
        if (in_array($routeName, ['efetivo.show', 'efetivo.edit'], true)) {
            $param = $route?->parameter('user');
            $entidadeTipo = 'User';

            if (is_object($param)) {
                $targetId   = $param->id ?? null;
                $targetRg   = $param->rg ?? null;
                $targetNome = $param->name ?? null;
            } elseif (is_numeric($param)) {
                $targetId = (int) $param;
            }

            return [$entidadeTipo, $targetId, $targetRg, $targetNome];
        }

        // Canais de Atendimento
        if ($routeName === 'atendimentos.show') {
            $param = $route?->parameter('atendimento');
            $entidadeTipo = 'Atendimento';

            if (is_object($param)) {
                $targetId   = $param->id ?? null;
                $targetNome = $param->nome ?? ($param->titulo ?? null);
            } elseif (is_numeric($param)) {
                $targetId = (int) $param;
            }

            return [$entidadeTipo, $targetId, $targetRg, $targetNome];
        }

        // Tickets (usuário/admin)
        if (in_array($routeName, ['tickets.show', 'admin.tickets.show'], true)) {
            $param = $route?->parameter('ticket');
            $entidadeTipo = 'Ticket';

            if (is_object($param)) {
                $targetId   = $param->id ?? null;
                $targetNome = $param->titulo ?? null;
            } elseif (is_numeric($param)) {
                $targetId = (int) $param;
            }

            return [$entidadeTipo, $targetId, $targetRg, $targetNome];
        }

        // Pré-inscrições (Admin)
        if ($routeName === 'admin.preinscricoes.show') {
            $param = $route?->parameter('preInscricao');
            $entidadeTipo = 'PreInscricao';

            if (is_object($param)) {
                $targetId   = $param->id ?? null;
                $targetNome = $param->nome ?? ($param->email ?? null);
            } elseif (is_numeric($param)) {
                $targetId = (int) $param;
            }

            return [$entidadeTipo, $targetId, $targetRg, $targetNome];
        }

        // rotas de lista/criar: sem alvo
        return [null, null, null, null];
    }
}
