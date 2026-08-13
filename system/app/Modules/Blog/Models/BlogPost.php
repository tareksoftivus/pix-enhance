<?php

namespace App\Modules\Blog\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    protected $table = 'blog_posts';

    protected $fillable = [
        'blog_category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'meta_title',
        'meta_description',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
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
            'draft' => ['label' => __('Draft'), 'variant' => 'default'],
            'published' => ['label' => __('Published'), 'variant' => 'success'],
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Public URL to the cover image, or null when none is set. Covers are
     * stored on the 'public' disk, so the URL must come from that disk —
     * the default disk would emit a broken /storage/... path.
     */
    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    /**
     * SEO title, falling back to the post title.
     */
    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    /**
     * SEO description, falling back to the excerpt, then a stripped body snippet.
     */
    public function seoDescription(): ?string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->excerpt) {
            return $this->excerpt;
        }

        return $this->body ? str(strip_tags($this->body))->squish()->limit(157)->toString() : null;
    }
}
