<?php

namespace App\Modules\Support\Services;

use App\Modules\Shared\Traits\HasCrudOperations;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Models\SupportTicketReply;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SupportTicketService
{
    use HasCrudOperations;

    /**
     * Number of conversation messages returned per "load older" page.
     */
    public const MESSAGES_PER_PAGE = 5;

    protected string $model = SupportTicket::class;

    /** @var array<string> */
    protected array $searchable = ['reference', 'subject', 'body'];

    /** @var array<string> */
    protected array $filterable = ['status', 'priority'];

    /** @var array<string> */
    protected array $sortable = ['reference', 'subject', 'status', 'priority', 'last_reply_at', 'created_at'];

    protected string $defaultSortBy = 'created_at';

    protected string $defaultSortOrder = 'desc';

    protected function applyEagerLoads(Builder $query): Builder
    {
        return $query->with('user');
    }

    /**
     * Paginated list scoped to a single owner (user panel).
     */
    public function listForUser(int $userId, array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $query = SupportTicket::query()->forUser($userId);

        $query = $this->applySearch($query, $filters);
        $query = $this->applyFilters($query, $filters);
        $query = $this->applySort($query, $filters);

        return $query->paginate($perPage ?? 15);
    }

    /**
     * Open a new ticket for the given user, seeding the thread with the body.
     *
     * @param  array{subject: string, body: string, category?: ?string, priority?: string}  $data
     */
    public function openTicket(int $userId, array $data): SupportTicket
    {
        $ticket = SupportTicket::create([
            'reference' => SupportTicket::generateReference(),
            'user_id' => $userId,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'category' => $data['category'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Append a reply from the given author (User or Admin) and advance status.
     *
     * A staff reply moves an open ticket to "pending" (awaiting the user);
     * a user reply on a pending ticket re-opens it for staff attention.
     */
    public function addReply(SupportTicket $ticket, Authenticatable $author, string $message): SupportTicketReply
    {
        $reply = $ticket->replies()->create([
            'author_type' => $author::class,
            'author_id' => $author->getKey(),
            'message' => $message,
        ]);

        $ticket->forceFill([
            'last_reply_at' => $reply->created_at,
            'status' => $reply->isFromStaff() ? 'pending' : 'open',
        ])->save();

        return $reply;
    }

    /**
     * Change a ticket's status (admin action).
     */
    public function changeStatus(SupportTicket $ticket, string $status): SupportTicket
    {
        $ticket->update(['status' => $status]);

        return $ticket->fresh();
    }

    /**
     * Return one newest-first page of the ticket conversation.
     *
     * The conversation is the ticket body (oldest) plus every reply. Replies
     * are paginated newest-first; the body is the single oldest item, so it is
     * appended once, on the final page, after all replies are exhausted.
     *
     * @return array{messages: array<int, array{name: string, body: string, at: Carbon, staff: bool}>, has_more: bool, next_page: int}
     */
    public function messagesPage(SupportTicket $ticket, int $page): array
    {
        $perPage = self::MESSAGES_PER_PAGE;

        // reorder() clears the relation's default oldest() ordering so we can
        // page strictly newest-first, breaking created_at ties by id.
        $replies = $ticket->replies()
            ->with('author')
            ->reorder()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $messages = $replies->map(fn (SupportTicketReply $reply) => [
            'name' => $reply->authorName(),
            'body' => $reply->message,
            'at' => $reply->created_at,
            'staff' => $reply->isFromStaff(),
        ])->all();

        // The body belongs after the very last reply. It sits on whichever page
        // is the final page of replies (or its own page if replies fill exactly).
        $repliesHaveMore = $replies->hasMorePages();

        if (! $repliesHaveMore) {
            $messages[] = [
                'name' => $ticket->user?->name ?? __('Unknown'),
                'body' => $ticket->body,
                'at' => $ticket->created_at,
                'staff' => false,
            ];
        }

        return [
            'messages' => $messages,
            'has_more' => $repliesHaveMore,
            'next_page' => $page + 1,
        ];
    }
}
