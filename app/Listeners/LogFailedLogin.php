<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Services\AuditoriaLogger;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        AuditoriaLogger::log(
            'login_falhou',
            $event->user?->id,
            'User',
            $event->user?->id,
            [
                'email_tentado' => $event->credentials['email'] ?? null,
                'alvo_user_id'  => $event->user?->id,
                'alvo_rg'       => $event->user?->rg,
                'alvo_nome'     => $event->user?->name,
            ]
        );
    }
}
