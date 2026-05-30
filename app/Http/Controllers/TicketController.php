<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketNewMessageNotification;
use App\Services\AuditoriaLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;

        $tickets = Ticket::query()
            ->where('user_id', auth()->id())
            ->with(['responsavel:id,name'])
            ->orderByLastActivityDesc()
            ->paginate($perPage)
            ->withQueryString();

        AuditoriaLogger::log(
            'ticket_user_index_aberto',
            auth()->id(),
            'Ticket',
            null,
            ['per_page' => $perPage],
            $request
        );

        return view('tickets.index', compact('tickets'));
    }

    public function create(Request $request)
    {
        AuditoriaLogger::log(
            'ticket_user_create_aberto',
            auth()->id(),
            'Ticket',
            null,
            null,
            $request
        );

        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria'   => 'required|in:' . implode(',', Ticket::CATEGORIES),
            'prioridade'  => 'required|in:' . implode(',', Ticket::PRIORITIES),
            'titulo'      => 'required|string|min:5|max:120',
            'descricao'   => 'required|string|min:10|max:5000',
        ]);

        $now = now();

        $ticket = DB::transaction(function () use ($data, $request, $now) {
            $ticket = Ticket::create([
                ...$data,
                'user_id'            => auth()->id(),
                'assigned_to'        => null,
                'status'             => 'aberto',

                // auditoria
                'ip'                 => $request->ip(),
                'user_agent'         => (string) $request->userAgent(),

                // leitura / UX
                'user_last_read_at'  => $now,
                'admin_last_read_at' => null,
                'last_message_at'    => $now,
            ]);

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => auth()->id(),
                'mensagem'  => $data['descricao'],
                'is_staff'  => false,
            ]);

            return $ticket;
        });

        // Notifica admins nível 9+
        $admins = User::query()
            ->where('nivel', '>=', 9)
            ->where('ativo', true)
            ->get(['id']);

        $preview = mb_strimwidth((string) $data['descricao'], 0, 140, '…');

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new TicketNewMessageNotification($ticket, $preview, false));
        }

        AuditoriaLogger::log(
            'ticket_user_criado',
            auth()->id(),
            'Ticket',
            $ticket->id,
            [
                'categoria'      => $ticket->categoria,
                'prioridade'     => $ticket->prioridade,
                'titulo'         => $ticket->titulo,
                'ticket_user_id' => $ticket->user_id,
                'ticket_status'  => $ticket->status,
                'assigned_to'    => $ticket->assigned_to,
            ],
            $request
        );

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket aberto com sucesso!');
    }

    public function show(Request $request, Ticket $ticket)
    {
        $userId = (int) auth()->id();
        $nivel  = (int) (auth()->user()->nivel ?? 0);

        $isOwner = ((int) $ticket->user_id === $userId);
        $isAdmin = ($nivel >= 9);

        if (!$isOwner && !$isAdmin) {
            AuditoriaLogger::log(
                'ticket_show_negado',
                $userId,
                'Ticket',
                $ticket->id,
                [
                    'ticket_user_id' => (int) $ticket->user_id,
                    'ticket_status'  => (string) $ticket->status,
                    'motivo'         => 'nao_e_dono_e_nao_admin',
                ],
                $request
            );
            abort(403);
        }

        $ticket->load(['user', 'responsavel', 'messages.user']);

        // leitura
        if ($isOwner) {
            $ticket->update(['user_last_read_at' => now()]);
        } else {
            $ticket->update(['admin_last_read_at' => now()]);
        }

        AuditoriaLogger::log(
            $isAdmin && !$isOwner ? 'ticket_admin_show_aberto' : 'ticket_user_show_aberto',
            $userId,
            'Ticket',
            $ticket->id,
            [
                'ticket_user_id' => (int) $ticket->user_id,
                'ticket_status'  => (string) $ticket->status,
                'as_admin'       => $isAdmin && !$isOwner,
                'assigned_to'    => $ticket->assigned_to,
            ],
            $request
        );

        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $userId = (int) auth()->id();

        if ((int) $ticket->user_id !== $userId) {
            AuditoriaLogger::log(
                'ticket_user_reply_negado',
                $userId,
                'Ticket',
                $ticket->id,
                [
                    'ticket_user_id' => (int) $ticket->user_id,
                    'ticket_status'  => (string) $ticket->status,
                    'motivo'         => 'nao_e_dono',
                ],
                $request
            );
            abort(403);
        }

        if (in_array((string) $ticket->status, ['fechado', 'resolvido'], true)) {
            AuditoriaLogger::log(
                'ticket_user_reply_negado',
                $userId,
                'Ticket',
                $ticket->id,
                [
                    'ticket_status' => (string) $ticket->status,
                    'motivo'        => 'ticket_fechado_ou_resolvido',
                ],
                $request
            );
            abort(403);
        }

        $data = $request->validate([
            'mensagem' => 'required|string|min:2|max:4000',
        ]);

        $statusAntes = (string) $ticket->status;
        $preview = mb_strimwidth((string) $data['mensagem'], 0, 140, '…');

        DB::transaction(function () use ($ticket, $userId, $data) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $userId,
                'mensagem'  => $data['mensagem'],
                'is_staff'  => false,
            ]);

            $updates = [
                'last_message_at'   => now(),
                'user_last_read_at' => now(),
            ];

            // se estava aguardando usuário, volta pra em andamento
            if ((string) $ticket->status === 'aguardando_usuario') {
                $updates['status'] = 'em_andamento';
            }

            $ticket->update($updates);
        });

        $ticket->refresh();

        // notifica: responsável se existir; senão admins
        if (!empty($ticket->assigned_to)) {
            $ticket->loadMissing('responsavel');
            $ticket->responsavel?->notify(new TicketNewMessageNotification($ticket, $preview, false));
        } else {
            $admins = User::query()
                ->where('nivel', '>=', 9)
                ->where('ativo', true)
                ->get(['id']);

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new TicketNewMessageNotification($ticket, $preview, false));
            }
        }

        AuditoriaLogger::log(
            'ticket_user_respondeu',
            $userId,
            'Ticket',
            $ticket->id,
            [
                'status_antes'  => $statusAntes,
                'status_depois' => (string) $ticket->status,
                'assigned_to'   => $ticket->assigned_to,
                'chars'         => mb_strlen((string) $data['mensagem']),
            ],
            $request
        );

        return back()->with('success', 'Mensagem enviada!');
    }
}