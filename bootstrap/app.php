<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // ✅ já existente
            'hierarquia'     => \App\Http\Middleware\HierarquiaMiddleware::class,

            // ✅ bloqueia usuário suspenso/desligado
            'usuario.ativo'  => \App\Http\Middleware\UsuarioAtivo::class,

            // ✅ novo: auditoria de acesso (abrir páginas)
            'auditar.acesso' => \App\Http\Middleware\AuditarAcessoMiddleware::class,

            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
