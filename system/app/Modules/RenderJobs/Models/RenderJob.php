<?php

namespace App\Modules\RenderJobs\Models;

use App\Models\User;
use App\Modules\Credits\Models\CreditReservation;
use App\Modules\RenderJobs\Database\Factories\RenderJobFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RenderJob extends Model
{
    /** @use HasFactory<RenderJobFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'render_jobs';

    protected $fillable = [
        'uuid',
        'user_id',
        'credit_reservation_id',
        'tool',
        'status',
        'progress',
        'model',
        'scale',
        'output_format',
        'settings',
        'credits_cost',
        'source_disk',
        'source_path',
        'source_name',
        'source_mime',
        'source_size',
        'source_width',
        'source_height',
        'target_width',
        'target_height',
        'output_disk',
        'output_path',
        'output_name',
        'output_mime',
        'output_size',
        'output_width',
        'output_height',
        'started_at',
        'completed_at',
        'failed_at',
        'cancelled_at',
        'duration_ms',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'scale' => 'integer',
            'progress' => 'integer',
            'credits_cost' => 'integer',
            'source_size' => 'integer',
            'source_width' => 'integer',
            'source_height' => 'integer',
            'target_width' => 'integer',
            'target_height' => 'integer',
            'output_size' => 'integer',
            'output_width' => 'integer',
            'output_height' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function newFactory(): RenderJobFactory
    {
        return RenderJobFactory::new();
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function statuses(): array
    {
        return [
            'queued' => ['label' => __('Queued'), 'badge' => 'badge-warning', 'icon' => 'clock'],
            'processing' => ['label' => __('Processing'), 'badge' => 'badge-primary', 'icon' => 'refresh-cw'],
            'completed' => ['label' => __('Completed'), 'badge' => 'badge-success', 'icon' => 'circle-check'],
            'failed' => ['label' => __('Failed'), 'badge' => 'badge-danger', 'icon' => 'circle-alert'],
            'cancelled' => ['label' => __('Cancelled'), 'badge' => 'badge-neutral', 'icon' => 'circle-slash'],
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creditReservation(): BelongsTo
    {
        return $this->belongsTo(CreditReservation::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeByTool(Builder $query, ?string $tool): Builder
    {
        return $tool ? $query->where('tool', $tool) : $query;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['completed', 'failed', 'cancelled'], true);
    }

    public function sourceUrl(): string
    {
        return Storage::disk($this->source_disk)->url($this->source_path);
    }

    public function outputUrl(): ?string
    {
        if (! $this->output_disk || ! $this->output_path) {
            return null;
        }

        return Storage::disk($this->output_disk)->url($this->output_path);
    }

    public function displayName(): string
    {
        return $this->output_name ?: $this->source_name;
    }

    public function toolLabel(): string
    {
        return config("render-jobs.tools.{$this->tool}.label", Str::headline($this->tool));
    }

    public function outputDimensions(): ?string
    {
        $width = $this->output_width ?: $this->target_width;
        $height = $this->output_height ?: $this->target_height;

        return $width && $height ? number_format($width).' x '.number_format($height) : null;
    }
}
