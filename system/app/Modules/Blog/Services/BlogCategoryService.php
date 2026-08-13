<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Shared\Traits\HasCrudOperations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategoryService
{
    use HasCrudOperations;

    protected string $model = BlogCategory::class;

    /** @var array<string> */
    protected array $searchable = ['name', 'slug'];

    /** @var array<string> */
    protected array $filterable = ['is_active'];

    /** @var array<string> */
    protected array $sortable = ['name', 'slug', 'sort_order', 'created_at'];

    protected string $defaultSortBy = 'sort_order';

    protected string $defaultSortOrder = 'asc';

    public function create(array $data): Model
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        return BlogCategory::create($data);
    }

    public function update(Model $record, array $data): Model
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name'], $record->id);
        $record->update($data);

        return $record->fresh();
    }

    /**
     * Build a slug from the provided value (or the name), guaranteeing
     * uniqueness by appending -2, -3, … when a collision exists.
     */
    protected function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name);
        $candidate = $base;
        $suffix = 2;

        while (BlogCategory::where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
