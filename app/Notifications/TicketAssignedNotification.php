<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket
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

        // Mensagem ajusta conforme quem recebe
        $title = $isAdmin
            ? "Você assumiu o Ticket #{$ticketId}"
            : "Seu Ticket #{$ticketId} foi assumido";

        $body = $isAdmin
            ? "Você foi definido como responsável por este atendimento."
            : "Um administrador assumiu o seu ticket. Acompanhe e responda quando solicitado.";

        return [
            'type'       => 'ticket_assigned',
            'ticket_id'  => $ticketId,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,

            // UI helpers (opcional)
            'level'      => 'success',
            'icon'       => 'person-check',

            // contexto
            'assigned_to'=> (int)($this->ticket->assigned_to ?? 0),
            'status'     => (string)($this->ticket->status ?? 'aberto'),
            'categoria'  => (string)($this->ticket->categoria ?? ''),
            'prioridade' => (string)($this->ticket->prioridade ?? 'normal'),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
