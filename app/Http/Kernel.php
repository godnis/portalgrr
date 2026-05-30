<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware global (todas as requisições)
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupos de middleware
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class, // (opcional)
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * ✅ Aliases de middleware (Laravel 9/10/11-friendly)
     * IMPORTANTE: você usa "throttle:xx,yy" nas rotas, então o alias throttle precisa existir.
     */
    protected $middlewareAliases = [
        // ====== Padrões do Laravel (recomendado manter) ======
        'auth'              => \App\Http\Middleware\Authenticate::class,
        'auth.basic'        => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'      => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'     => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'               => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'             => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'  => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'            => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'          => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'          => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'bindings'          => \Illuminate\Routing\Middleware\SubstituteBindings::class,

        // ====== Seus middlewares ======
        'hierarquia'        => \App\Http\Middleware\HierarquiaMiddleware::class,
        'auditar.acesso'    => \App\Http\Middleware\AuditarAcessoMiddleware::class,
        'usuario.ativo'     => \App\Http\Middleware\UsuarioAtivoMiddleware::class,

        // ✅ RH: Permissão de edição por seção (hierarquia/controle_saida/etc)
        'rh.edit'           => \App\Http\Middleware\RhEditPermission::class,

        'api' => \App\Http\Middleware\ApiTokenMiddleware::class,
    ];

    /**
     * ✅ Compatibilidade com projetos antigos (Laravel 8/9)
     * Se o seu projeto ainda usa $routeMiddleware, mantenha espelhado.
     * Se não usar, não tem problema deixar aqui.
     */
    protected $routeMiddleware = [
        'auth'              => \App\Http\Middleware\Authenticate::class,
        'auth.basic'        => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'      => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'     => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'               => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'             => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'  => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'            => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'          => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'          => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'bindings'          => \Illuminate\Routing\Middleware\SubstituteBindings::class,

        'hierarquia'        => \App\Http\Middleware\HierarquiaMiddleware::class,
        'auditar.acesso'    => \App\Http\Middleware\AuditarAcessoMiddleware::class,
        'usuario.ativo'     => \App\Http\Middleware\UsuarioAtivoMiddleware::class,

        // ✅ RH: espelhado aqui também
        'rh.edit'           => \App\Http\Middleware\RhEditPermission::class,

        'api' => \App\Http\Middleware\ApiTokenMiddleware::class,
    ];
}
