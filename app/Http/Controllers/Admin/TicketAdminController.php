<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketNewMessageNotification;
use App\Notifications\TicketStatusChangedNotification;
use App\Services\AuditoriaLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketAdminController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int)($request->get('per_page', 20));
        $perPage = max(10, min(100, $perPage));

        $q          = trim((string) $request->get('q', ''));
        $status     = trim((string) $request->get('status', ''));
        $categoria  = trim((string) $request->get('categoria', ''));
        $prioridade = trim((string) $request->get('prioridade', ''));
        $assigned   = trim((string) $request->get('assigned', '')); // me | none | {id}

        $tickets = Ticket::query()
            ->with(['user:id,name', 'responsavel:id,name'])
            ->when($status !== '' && in_array($status, Ticket::STATUSES, true), fn($qq) => $qq->where('status', $status))
            ->when($categoria !== '' && in_array($categoria, Ticket::CATEGORIES, true), fn($qq) => $qq->where('categoria', $categoria))
            ->when($prioridade !== '' && in_array($prioridade, Ticket::PRIORITIES, true), fn($qq) => $qq->where('prioridade', $prioridade))
            ->when($assigned !== '', function ($qq) use ($assigned) {
                if ($assigned === 'me') {
                    $qq->where('assigned_to', auth()->id());
                    return;
                }
                if ($assigned === 'none') {
                    $qq->whereNull('assigned_to');
                    return;
                }
                if (ctype_digit($assigned)) {
                    $qq->where('assigned_to', (int) $assigned);
                }
            })
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) {
                        $w->orWhere('id', (int) $q);
                    }

                    $w->orWhere('titulo', 'like', "%{$q}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%"));
                });
            })
            ->orderByLastActivityDesc()
            ->paginate($perPage)
            ->withQueryString();

        AuditoriaLogger::log(
            'ticket_admin_index_aberto',
            (int) auth()->id(),
            'Ticket',
            null,
            [
                'per_page' => $perPage,
                'q' => $q,
                'status' => $status,
                'categoria' => $categoria,
                'prioridade' => $prioridade,
                'assigned' => $assigned,
            ],
            $request
        );

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['user', 'responsavel', 'messages.user']);

        $ticket->update(['admin_last_read_at' => now()]);

        AuditoriaLogger::log(
            'ticket_admin_show_aberto',
            (int) auth()->id(),
            'Ticket',
            $ticket->id,
            [
                'ticket_status'    => (string) $ticket->status,
                'ticket_user_id'   => (int) $ticket->user_id,
                'assigned_to'      => $ticket->assigned_to ? (int) $ticket->assigned_to : null,
                'has_unread_admin' => $ticket->adminHasUnread(),
            ],
            $request
        );

        return view('admin.tickets.show', compact('ticket'));
    }

    /** Botão assumir */
    public function assume(Request $request, Ticket $ticket)
    {
        $adminId   = (int) auth()->id();
        $respAntes = $ticket->assigned_to ? (int) $ticket->assigned_to : null;

        if ($respAntes === $adminId) {
            AuditoriaLogger::log(
                'ticket_admin_assume_ignorado',
                $adminId,
                'Ticket',
                $ticket->id,
                [
                    'motivo' => 'ja_atribuido_ao_mesmo_admin',
                    'assigned_to' => $respAntes,
                    'ticket_user_id' => (int) $ticket->user_id,
                    'ticket_status' => (string) $ticket->status,
                ],
                $request
            );

            return back()->with('success', 'Você já é o responsável por este ticket.');
        }

        DB::transaction(function () use ($ticket, $adminId) {
            $ticket->update([
                'assigned_to'        => $adminId,
                'admin_last_read_at' => now(),
            ]);
        });

        $ticket->refresh()->loadMissing(['user', 'responsavel']);

        // notifica usuário + admin (sininho)
        auth()->user()?->notify(new TicketAssignedNotification($ticket));
        $ticket->user?->notify(new TicketAssignedNotification($ticket));

        AuditoriaLogger::log(
            'ticket_admin_assumiu',
            $adminId,
            'Ticket',
            $ticket->id,
            [
                'assigned_to_antes'  => $respAntes,
                'assigned_to_depois' => $ticket->assigned_to ? (int) $ticket->assigned_to : null,
                'ticket_user_id'     => (int) $ticket->user_id,
                'ticket_status'      => (string) $ticket->status,
            ],
            $request
        );

        return back()->with('success', 'Ticket assumido com sucesso.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', Ticket::STATUSES),
        ]);

        $adminId     = (int) auth()->id();
        $statusNovo  = (string) $data['status'];
        $statusAntes = (string) $ticket->status;
        $respAntes   = $ticket->assigned_to ? (int) $ticket->assigned_to : null;

        DB::transaction(function () use ($ticket, $adminId, $statusNovo) {
            $updates = [
                'status'             => $statusNovo,
                'assigned_to'        => $adminId,
                'admin_last_read_at' => now(),
            ];

            if (in_array($statusNovo, ['fechado', 'resolvido'], true)) {
                $updates['fechado_em'] = now();
            } else {
                $updates['fechado_em'] = null;
            }

            $ticket->update($updates);
        });

        $ticket->refresh()->loadMissing(['user', 'responsavel']);

        // status mudou? notifica usuário
        if ($statusAntes !== (string) $ticket->status) {
            $ticket->user?->notify(
                new TicketStatusChangedNotification($ticket, $statusAntes, (string) $ticket->status)
            );
        }

        // responsável mudou? notifica
        if ((int) $respAntes !== (int) ($ticket->assigned_to ?? 0)) {
            auth()->user()?->notify(new TicketAssignedNotification($ticket));
            $ticket->user?->notify(new TicketAssignedNotification($ticket));
        }

        AuditoriaLogger::log(
            'ticket_admin_status_alterado',
            $adminId,
            'Ticket',
            $ticket->id,
            [
                'status_antes'       => $statusAntes,
                'status_depois'      => (string) $ticket->status,
                'assigned_to_antes'  => $respAntes,
                'assigned_to_depois' => $ticket->assigned_to ? (int) $ticket->assigned_to : null,
                'ticket_user_id'     => (int) $ticket->user_id,
            ],
            $request
        );

        return back()->with('success', 'Status do ticket atualizado com sucesso.');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'mensagem' => 'required|string|min:2|max:5000',
        ]);

        $adminId     = (int) auth()->id();
        $statusAntes = (string) $ticket->status;
        $respAntes   = $ticket->assigned_to ? (int) $ticket->assigned_to : null;

        $preview = mb_strimwidth((string) $data['mensagem'], 0, 140, '…');

        DB::transaction(function () use ($ticket, $adminId, $data) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $adminId,
                'mensagem'  => $data['mensagem'],
                'is_staff'  => true,
            ]);

            $updates = [
                'last_message_at'    => now(),
                'admin_last_read_at' => now(),
                'assigned_to'        => $adminId,
            ];

            // Ao responder, joga pra em_andamento se não estiver fechado
            if ((string) $ticket->status !== 'fechado') {
                $updates['status'] = 'em_andamento';
                $updates['fechado_em'] = null; // se estava resolvido, reabre fluxo
            }

            $ticket->update($updates);
        });

        $ticket->refresh()->loadMissing(['user', 'responsavel']);

        // notifica usuário
        $ticket->user?->notify(new TicketNewMessageNotification($ticket, $preview, true));

        // se assumiu agora, notifica
        if ((int) $respAntes !== (int) ($ticket->assigned_to ?? 0)) {
            auth()->user()?->notify(new TicketAssignedNotification($ticket));
            $ticket->user?->notify(new TicketAssignedNotification($ticket));
        }

        AuditoriaLogger::log(
            'ticket_admin_respondeu',
            $adminId,
            'Ticket',
            $ticket->id,
            [
                'status_antes'       => $statusAntes,
                'status_depois'      => (string) $ticket->status,
                'assigned_to_antes'  => $respAntes,
                'assigned_to_depois' => $ticket->assigned_to ? (int) $ticket->assigned_to : null,
                'ticket_user_id'     => (int) $ticket->user_id,
                'chars'              => mb_strlen((string) $data['mensagem']),
            ],
            $request
        );

        return back()->with('success', 'Resposta enviada com sucesso.');
    }
}