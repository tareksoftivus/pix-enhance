<?php

namespace App\Modules\Blog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Http\Requests\StorePostRequest;
use App\Modules\Blog\Http\Requests\UpdatePostRequest;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Services\BlogPostService;
use App\Modules\Blog\Tables\BlogPostsTable;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Shared\Traits\HasCrudActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Storage;

class BlogPostsController extends Controller implements HasMiddleware
{
    use HasCrudActions;

    protected string $viewPath = 'blog::admin.posts';

    protected string $routePrefix = 'admin.blog-posts';

    protected string $resourceName = 'blog-posts';

    public static function middleware(): array
    {
        return static::crudMiddleware('blog-posts');
    }

    public function __construct(
        protected BlogPostService $service
    ) {}

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        $data['cover_image'] = $this->storeCover($request);

        $this->service->create($data);

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Created successfully'));
    }

    public function update(UpdatePostRequest $request, $record): RedirectResponse
    {
        $record = $this->resolveRecord($record);
        $data = $request->validated();

        if ($newCover = $this->storeCover($request)) {
            $this->deleteCover($record);
            $data['cover_image'] = $newCover;
        } else {
            unset($data['cover_image']);
        }

        $this->service->update($record, $data);

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Updated successfully'));
    }

    public function destroy($record): RedirectResponse
    {
        $record = $this->resolveRecord($record);
        $this->deleteCover($record);
        $this->service->delete($record);

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Deleted successfully'));
    }

    protected function tableDefinition(Request $request): ?TableDefinition
    {
        return BlogPostsTable::make();
    }

    protected function formData(): array
    {
        return [
            'categories' => BlogCategory::optionsForSelect(),
            'statuses' => BlogPost::statusOptions(),
        ];
    }

    /**
     * Store an uploaded cover image and return its public path, or null.
     */
    protected function storeCover(Request $request): ?string
    {
        if (! $request->hasFile('cover_image')) {
            return null;
        }

        return $request->file('cover_image')->store('blog/covers', 'public');
    }

    protected function deleteCover(BlogPost $post): void
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }
    }

    protected function getResourceVariable(): string
    {
        return 'posts';
    }

    protected function getSingularVariable(): string
    {
        return 'post';
    }
}
