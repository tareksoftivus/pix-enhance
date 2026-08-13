<?php

namespace App\Modules\Support\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Support\Http\Requests\ReplyTicketRequest;
use App\Modules\Support\Http\Requests\StoreTicketRequest;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Services\SupportTicketService;
use App\Modules\Support\Tables\SupportTicketsTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SupportTicketsController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SupportTicketService $service
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:support-tickets.view', only: ['index', 'show', 'messages']),
            new Middleware('permission:support-tickets.create', only: ['create', 'store']),
            new Middleware('permission:support-tickets.reply', only: ['reply']),
        ];
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        $tickets = $this->service->listForUser($request->user()->id, $filters, $request->integer('per_page') ?: null);
        $table = SupportTicketsTable::forUser();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.tables.resource-rows', ['definition' => $table, 'items' => $tickets])->render(),
                'pagination' => view('components.tables.pagination', ['paginator' => $tickets])->render(),
                'total' => $tickets->total(),
            ]);
        }

        return view('support::user.index', [
            'tickets' => $tickets,
            'table' => $table,
        ]);
    }

    public function create(): View
    {
        return view('support::user.create', [
            'priorities' => SupportTicket::priorityOptions(),
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = $this->service->openTicket($request->user()->id, $request->validated());

        return redirect()
            ->route('user.support-tickets.show', $ticket)
            ->with('success', __('Your ticket has been submitted.'));
    }

    public function show(Request $request, SupportTicket $ticket): View
    {
        $this->authorizeOwnership($request, $ticket);

        return view('support::user.show', [
            'ticket' => $ticket,
            'firstPage' => $this->service->messagesPage($ticket, 1),
        ]);
    }

    public function messages(Request $request, SupportTicket $ticket): JsonResponse
    {
        $this->authorizeOwnership($request, $ticket);

        $page = max(1, $request->integer('page', 1));
        $result = $this->service->messagesPage($ticket, $page);

        return response()->json([
            'html' => view('support::components.message-list', ['messages' => $result['messages']])->render(),
            'has_more' => $result['has_more'],
            'next_page' => $result['next_page'],
        ]);
    }

    public function reply(ReplyTicketRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorizeOwnership($request, $ticket);

        abort_if($ticket->isClosed(), 403, __('This ticket is closed.'));

        $this->service->addReply($ticket, $request->user(), $request->validated()['message']);

        return redirect()
            ->route('user.support-tickets.show', $ticket)
            ->with('success', __('Reply sent.'));
    }

    /**
     * Ensure the signed-in user owns the ticket, otherwise 404.
     */
    protected function authorizeOwnership(Request $request, SupportTicket $ticket): void
    {
        abort_unless($ticket->user_id === $request->user()->id, 404);
    }
}
