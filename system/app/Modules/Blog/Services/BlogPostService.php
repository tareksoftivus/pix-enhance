<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\BlogPost;
use App\Modules\Shared\Traits\HasCrudOperations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPostService
{
    use HasCrudOperations;

    protected string $model = BlogPost::class;

    /** @var array<string> */
    protected array $searchable = ['title', 'slug', 'excerpt'];

    /** @var array<string> */
    protected array $filterable = ['status', 'blog_category_id'];

    /** @var array<string> */
    protected array $sortable = ['title', 'status', 'published_at', 'created_at'];

    protected string $defaultSortBy = 'created_at';

    protected string $defaultSortOrder = 'desc';

    protected function applyEagerLoads(Builder $query): Builder
    {
        return $query->with(['category', 'author']);
    }

    public function create(array $data): Model
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title']);
        $data['published_at'] = $this->resolvePublishedAt($data['status'] ?? 'draft', null);

        return BlogPost::create($data);
    }

    public function update(Model $record, array $data): Model
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $record->id);
        $data['published_at'] = $this->resolvePublishedAt($data['status'] ?? $record->status, $record->published_at);

        $record->update($data);

        return $record->fresh();
    }

    /**
     * Set published_at to now on first publish, clear it when unpublished,
     * and preserve the existing timestamp on re-save.
     */
    protected function resolvePublishedAt(string $status, mixed $current): mixed
    {
        if ($status !== 'published') {
            return null;
        }

        return $current ?: now();
    }

    /**
     * Build a unique slug from the provided value (or the title).
     */
    protected function uniqueSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $title);
        $candidate = $base;
        $suffix = 2;

        while (BlogPost::where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }

    /**
     * Paginated published posts for the public blog listing, newest first.
     */
    public function publishedForPublic(?int $categoryId = null, int $perPage = 9)
    {
        return BlogPost::query()
            ->published()
            ->with('category')
            ->when($categoryId, fn ($q) => $q->where('blog_category_id', $categoryId))
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }
}
