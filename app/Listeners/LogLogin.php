<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\AuditoriaLogger;

class LogLogin
{
    public function handle(Login $event): void
    {
        AuditoriaLogger::log(
            'login',
            $event->user->id,
            'User',
            $event->user->id,
            [
                'alvo_user_id' => $event->user->id,
                'alvo_rg'      => $event->user->rg,
                'alvo_nome'    => $event->user->name,
                'alvo_email'   => $event->user->email,
            ]
        );
    }
}
