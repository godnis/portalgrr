<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketNewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $preview = '',
        public bool $fromStaff = false
    ) {}

    /**
     * ✅ Somente sininho (database)
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * ✅ Sininho (salvo no banco)
     */
    public function toDatabase(object $notifiable): array
    {
        $isAdmin = (int)($notifiable->nivel ?? 0) >= 9;

        $url = $isAdmin
            ? route('admin.tickets.show', $this->ticket)
            : route('tickets.show', $this->ticket);

        $ticketId = (int) $this->ticket->id;

        $title = $this->fromStaff
            ? "Resposta no Ticket #{$ticketId}"
            : "Nova mensagem no Ticket #{$ticketId}";

        $body = trim($this->preview) !== ''
            ? $this->preview
            : ($this->fromStaff
                ? 'Um administrador respondeu seu ticket.'
                : 'Você recebeu uma nova mensagem no ticket.'
            );

        // nível/ícone (útil na UI)
        $level = $this->fromStaff ? 'info' : 'warning';
        $icon  = $this->fromStaff ? 'chat-dots' : 'chat-left-text';

        return [
            'type'       => 'ticket_new_message',
            'ticket_id'  => $ticketId,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,

            // UI helpers (opcional)
            'level'      => $level,
            'icon'       => $icon,

            // contexto útil
            'from_staff' => (bool) $this->fromStaff,
            'status'     => (string)($this->ticket->status ?? 'aberto'),
            'categoria'  => (string)($this->ticket->categoria ?? ''),
            'prioridade' => (string)($this->ticket->prioridade ?? 'normal'),
            'assigned_to'=> (int)($this->ticket->assigned_to ?? 0),
        ];
    }

    /**
     * fallback
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
