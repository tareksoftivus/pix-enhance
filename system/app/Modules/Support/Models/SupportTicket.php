<?php

namespace App\Modules\Support\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $table = 'support_tickets';

    protected $fillable = [
        'reference',
        'user_id',
        'subject',
        'body',
        'category',
        'priority',
        'status',
        'last_reply_at',
    ];

    protected function casts(): array
    {
        return [
            'last_reply_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Status values and the badge variant used to render each in tables.
     *
     * @return array<string, array{label: string, variant: string}>
     */
    public static function statuses(): array
    {
        return [
            'open' => ['label' => __('Open'), 'variant' => 'info'],
            'pending' => ['label' => __('Pending'), 'variant' => 'warning'],
            'resolved' => ['label' => __('Resolved'), 'variant' => 'success'],
            'closed' => ['label' => __('Closed'), 'variant' => 'default'],
        ];
    }

    /**
     * Priority values and the badge variant used to render each in tables.
     *
     * @return array<string, array{label: string, variant: string}>
     */
    public static function priorities(): array
    {
        return [
            'low' => ['label' => __('Low'), 'variant' => 'default'],
            'medium' => ['label' => __('Medium'), 'variant' => 'info'],
            'high' => ['label' => __('High'), 'variant' => 'warning'],
            'urgent' => ['label' => __('Urgent'), 'variant' => 'danger'],
        ];
    }

    /**
     * Status options as value => label for select inputs.
     *
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return array_map(fn (array $meta) => $meta['label'], static::statuses());
    }

    /**
     * Priority options as value => label for select inputs.
     *
     * @return array<string, string>
     */
    public static function priorityOptions(): array
    {
        return array_map(fn (array $meta) => $meta['label'], static::priorities());
    }

    /**
     * Statuses a user can no longer reply to.
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed'], true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class)->oldest();
    }

    /**
     * Generate the next human-friendly reference (TKT-000001).
     */
    public static function generateReference(): string
    {
        $next = (int) static::max('id') + 1;

        return 'TKT-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
