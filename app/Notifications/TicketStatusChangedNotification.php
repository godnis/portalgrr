<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $from,
        public string $to
    ) {}

    /**
     * ✅ Somente sininho (database)
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isAdmin = (int)($notifiable->nivel ?? 0) >= 9;

        $url = $isAdmin
            ? route('admin.tickets.show', $this->ticket)
            : route('tickets.show', $this->ticket);

        $ticketId = (int) $this->ticket->id;

        return [
            'type'      => 'ticket_status_changed',
            'ticket_id' => $ticketId,
            'title'     => "Status do Ticket #{$ticketId} atualizado",
            'body'      => "Status: {$this->from} → {$this->to}",
            'url'       => $url,

            // UI helpers (opcional)
            'level'     => 'info',
            'icon'      => 'arrow-repeat',

            // contexto
            'from'      => (string) $this->from,
            'to'        => (string) $this->to,
            'assigned_to'=> (int)($this->ticket->assigned_to ?? 0),
            'categoria' => (string)($this->ticket->categoria ?? ''),
            'prioridade'=> (string)($this->ticket->prioridade ?? 'normal'),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
