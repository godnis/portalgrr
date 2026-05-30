<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Services\AuditoriaLogger;

class LogLogout
{
    public function handle(Logout $event): void
    {
        if (!$event->user) return;

        AuditoriaLogger::log(
            'logout',
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
