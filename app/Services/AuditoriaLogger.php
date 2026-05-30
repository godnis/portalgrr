<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuditoriaLogger
{
    /** @var array<string,bool>|null */
    private static ?array $colsCache = null;

    private static function hasCol(string $col): bool
    {
        if (self::$colsCache === null) {
            self::$colsCache = [];
        }

        if (!array_key_exists($col, self::$colsCache)) {
            self::$colsCache[$col] = Schema::hasColumn('auditorias', $col);
        }

        return (bool) self::$colsCache[$col];
    }

    private static function safeRequestInput(?Request $request): array
    {
        if (!$request) return [];

        // Evita dados sensíveis no log
        $blacklist = [
            'password', 'password_confirmation', 'current_password',
            'token', '_token', 'access_token', 'refresh_token',
        ];

        $all = $request->all() ?? [];

        foreach ($blacklist as $k) {
            if (array_key_exists($k, $all)) unset($all[$k]);
        }

        // Evita payload gigante
        return Arr::only($all, array_slice(array_keys($all), 0, 30));
    }

    /**
     * ✅ Compatível com chamadas antigas e novas:
     * - Novo (recomendado):
     *   log('acao', auth()->id(), 'tipo', 123, ['detalhes'=>...], request())
     *
     * - Antigo (muito comum no projeto):
     *   log('acao', ['detalhes'=>...])
     *
     * - Antigo variante:
     *   log('acao', auth()->id(), ['detalhes'=>...])
     */
    public static function log(
        string $acao,
        $userId = null,
        $entidadeTipo = null,
        $entidadeId = null,
        $detalhes = null,
        $request = null
    ): void {
        try {
            // ----------------------------
            // Normalização para evitar 500
            // ----------------------------

            // Se vier Request em alguma posição, encaixa
            if ($userId instanceof Request && $request === null) {
                $request = $userId;
                $userId = null;
            }
            if ($entidadeTipo instanceof Request && $request === null) {
                $request = $entidadeTipo;
                $entidadeTipo = null;
            }
            if ($detalhes instanceof Request && $request === null) {
                $request = $detalhes;
                $detalhes = null;
            }

            // Caso MAIS IMPORTANTE: log('acao', [payload])
            if (is_array($userId) && $detalhes === null && $entidadeTipo === null && $entidadeId === null) {
                $detalhes = $userId;
                $userId = null;
            }

            // Variante: log('acao', auth()->id(), [payload])
            if (is_array($entidadeTipo) && $detalhes === null) {
                $detalhes = $entidadeTipo;
                $entidadeTipo = null;
                $entidadeId = null;
            }

            // Variante: alguém passou o payload como 4º arg
            if (is_array($entidadeId) && $detalhes === null) {
                $detalhes = $entidadeId;
                $entidadeId = null;
            }

            // Tipos finais
            $request  = ($request instanceof Request) ? $request : request();
            $detalhes = is_array($detalhes) ? $detalhes : [];

            $actor = auth()->user();

            // userId pode vir string/array por bug antigo -> normaliza
            if (is_numeric($userId)) {
                $userId = (int) $userId;
            } else {
                $userId = null;
            }
            $userId = $userId ?? ($actor?->id);

            // entidadeId normaliza
            if (is_numeric($entidadeId)) {
                $entidadeId = (int) $entidadeId;
            } else {
                $entidadeId = null;
            }

            $entidadeTipo = is_string($entidadeTipo) && $entidadeTipo !== '' ? $entidadeTipo : null;

            // ✅ 1 request_id por request
            $requestId = (string) ($request?->attributes->get('request_id') ?? '');
            if ($requestId === '') {
                $requestId = (string) Str::uuid();
                $request?->attributes->set('request_id', $requestId);
            }

            $routeName = $request?->route()?->getName();
            $method    = $request?->method();
            $url       = $request?->fullUrl();

            // alvo_* (se vier)
            $alvoUserId = Arr::get($detalhes, 'alvo_user_id');
            $alvoRg     = Arr::get($detalhes, 'alvo_rg');
            $alvoNome   = Arr::get($detalhes, 'alvo_nome');

            // ✅ base detalhes enriquecidos (sempre)
            $detalhesBase = array_merge($detalhes, array_filter([
                'request_id'    => $requestId,
                'route_name'    => $routeName,
                'method'        => $method,
                'url'           => $url,
                'referrer'      => $request?->headers->get('referer'),
                'is_ajax'       => $request?->ajax() ? true : false,

                // ator
                'actor_user_id' => $userId,
                'actor_rg'      => $actor?->rg ?? Arr::get($detalhes, 'actor_rg'),
                'actor_nome'    => $actor?->name ?? Arr::get($detalhes, 'actor_nome'),

                // alvo (se existir)
                'alvo_user_id'  => is_numeric($alvoUserId) ? (int) $alvoUserId : null,
                'alvo_rg'       => $alvoRg,
                'alvo_nome'     => $alvoNome,

                // request (leve)
                'request_input' => self::safeRequestInput($request),
                'query'         => $request?->query() ?? [],
            ], fn ($v) => $v !== null && $v !== ''));

            $payload = [
                'user_id'       => $userId,
                'acao'          => $acao,
                'entidade_tipo' => $entidadeTipo,
                'entidade_id'   => $entidadeId,
                'detalhes'      => $detalhesBase,
                'ip'            => $request?->ip(),
                'user_agent'    => substr((string) $request?->userAgent(), 0, 1000),
            ];

            // ✅ espelha em colunas se existirem (melhor para filtro e index)
            if (self::hasCol('request_id'))   $payload['request_id']   = $requestId;
            if (self::hasCol('actor_rg'))     $payload['actor_rg']     = $detalhesBase['actor_rg'] ?? null;
            if (self::hasCol('actor_nome'))   $payload['actor_nome']   = $detalhesBase['actor_nome'] ?? null;

            if (self::hasCol('alvo_user_id')) $payload['alvo_user_id'] = $detalhesBase['alvo_user_id'] ?? null;
            if (self::hasCol('alvo_rg'))      $payload['alvo_rg']      = $detalhesBase['alvo_rg'] ?? null;
            if (self::hasCol('alvo_nome'))    $payload['alvo_nome']    = $detalhesBase['alvo_nome'] ?? null;

            if (self::hasCol('route_name'))   $payload['route_name']   = $routeName;
            if (self::hasCol('method'))       $payload['method']       = $method;
            if (self::hasCol('url'))          $payload['url']          = $url;

            Auditoria::create($payload);
        } catch (\Throwable $e) {
            // Auditoria NUNCA pode quebrar o sistema
        }
    }
}
