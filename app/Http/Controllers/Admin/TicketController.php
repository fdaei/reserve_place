<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TicketReplyRequest;
use App\Services\Admin\ActivityLogService;
use App\Models\Ticket;
use App\Models\TicketChat;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('tickets-manage'), 403);

        $query = Ticket::with('user')->withCount([
            'chats',
            'chats as unseen_count' => fn ($builder) => $builder->where('seen', 0),
        ]);

        if ($request->filled('search')) {
            $query->search($request->query('search'));
        }

        if ($request->query('status') !== null && $request->query('status') !== '') {
            $query->where('status', $request->query('status'));
        }

        return view('admin.tickets.index', [
            'tickets' => $query->latest('id')->paginate(12)->withQueryString(),
        ]);
    }

    public function show(Ticket $ticket)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('tickets-manage'), 403);

        $ticket->load(['user', 'chats.user']);
        $ticket->chats()->where('user_id', '!=', auth()->id())->update(['seen' => 1]);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(TicketReplyRequest $request, Ticket $ticket)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('tickets-manage'), 403);

        TicketChat::create([
            'ticket_id' => $ticket->id,
            'message' => $request->validated('message'),
            'seen' => 1,
            'user_id' => auth()->id(),
        ]);

        $ticket->update(['status' => 1]);
        app(ActivityLogService::class)->log('ticket_reply', $ticket, $request, description: 'ثبت پاسخ برای تیکت');

        return back()->with('admin_success', 'پاسخ تیکت ثبت شد.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('tickets-manage'), 403);

        $ticket->update([
            'status' => $request->input('status', $ticket->status ? 0 : 1),
        ]);

        app(ActivityLogService::class)->log('ticket_status_update', $ticket, $request, [
            'status' => $ticket->status,
        ], 'تغییر وضعیت تیکت');

        return back()->with('admin_success', 'وضعیت تیکت بروزرسانی شد.');
    }
}
