<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;
use App\Http\Middleware\RhEditPermission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * ✅ Corrige: Target class [rh.edit] does not exist
         * Alguns fluxos (terminate middleware/container) podem tentar resolver "rh.edit"
         * via app()->make('rh.edit'). Então registramos um binding.
         */
        $this->app->bind('rh.edit', RhEditPermission::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sincroniza automaticamente Efetivo (User) -> RH Hierarquia
        User::observe(UserObserver::class);
    }
}
