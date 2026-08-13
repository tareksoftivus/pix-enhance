<?php

namespace App\Modules\Support\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupportTicketReply extends Model
{
    protected $table = 'support_ticket_replies';

    protected $fillable = [
        'support_ticket_id',
        'author_type',
        'author_id',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    /**
     * The reply author — either a User (web guard) or an Admin.
     */
    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Whether this reply was written by a staff member (Admin), used to
     * align/label the message differently from the ticket owner's replies.
     */
    public function isFromStaff(): bool
    {
        return $this->author_type === Admin::class;
    }

    /**
     * Display name of the author, degrading gracefully if the account is gone.
     */
    public function authorName(): string
    {
        return $this->author?->name ?? __('Unknown');
    }
}
