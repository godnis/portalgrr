<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        // ✅ RATE LIMITER da pré-inscrição (público)
        RateLimiter::for('preinscricao', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';

            // ✅ 3 tentativas a cada 10 minutos por IP
            return Limit::perMinutes(10, 3)->by($ip);
        });

        // ✅ RATE LIMITER dos Canais de Atendimento (público)
        RateLimiter::for('atendimento', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';

            // ✅ 5 tentativas a cada 10 minutos por IP
            return Limit::perMinutes(10, 5)->by($ip);
        });

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
