<?php

namespace App\Modules\Support\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Support\Http\Requests\ReplyTicketRequest;
use App\Modules\Support\Http\Requests\UpdateStatusRequest;
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
            new Middleware('permission:support-tickets.reply', only: ['reply']),
            new Middleware('permission:support-tickets.edit', only: ['updateStatus']),
            new Middleware('permission:support-tickets.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        $tickets = $this->service->listPaginated($filters, $request->integer('per_page') ?: null);
        $table = SupportTicketsTable::forAdmin();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.tables.resource-rows', ['definition' => $table, 'items' => $tickets])->render(),
                'pagination' => view('components.tables.pagination', ['paginator' => $tickets])->render(),
                'total' => $tickets->total(),
            ]);
        }

        return view('support::admin.index', [
            'tickets' => $tickets,
            'table' => $table,
        ]);
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->loadMissing('user');

        return view('support::admin.show', [
            'ticket' => $ticket,
            'statuses' => SupportTicket::statusOptions(),
            'firstPage' => $this->service->messagesPage($ticket, 1),
        ]);
    }

    public function messages(SupportTicket $ticket, Request $request): JsonResponse
    {
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
        $this->service->addReply($ticket, $request->user(), $request->validated()['message']);

        return redirect()
            ->route('admin.support-tickets.show', $ticket)
            ->with('success', __('Reply sent.'));
    }

    public function updateStatus(UpdateStatusRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $this->service->changeStatus($ticket, $request->validated()['status']);

        return redirect()
            ->route('admin.support-tickets.show', $ticket)
            ->with('success', __('Status updated successfully'));
    }

    public function destroy(SupportTicket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()
            ->route('admin.support-tickets.index')
            ->with('success', __('Deleted successfully'));
    }
}
