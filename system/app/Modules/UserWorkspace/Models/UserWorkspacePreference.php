<?php

namespace App\Modules\UserWorkspace\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWorkspacePreference extends Model
{
    protected $table = 'user_workspace_preferences';

    protected $fillable = [
        'user_id',
        'notification_preferences',
        'render_defaults',
        'source_retention_days',
        'desktop_notifications_enabled',
        'completion_sound_enabled',
    ];

    protected function casts(): array
    {
        return [
            'notification_preferences' => 'array',
            'render_defaults' => 'array',
            'source_retention_days' => 'integer',
            'desktop_notifications_enabled' => 'boolean',
            'completion_sound_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
