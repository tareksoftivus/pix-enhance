<?php

namespace App\Modules\Blog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Http\Requests\StoreCategoryRequest;
use App\Modules\Blog\Http\Requests\UpdateCategoryRequest;
use App\Modules\Blog\Services\BlogCategoryService;
use App\Modules\Blog\Tables\BlogCategoriesTable;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Shared\Traits\HasCrudActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class BlogCategoriesController extends Controller implements HasMiddleware
{
    use HasCrudActions;

    protected string $viewPath = 'blog::admin.categories';

    protected string $routePrefix = 'admin.blog-categories';

    protected string $resourceName = 'blog-categories';

    public static function middleware(): array
    {
        return static::crudMiddleware('blog-categories');
    }

    public function __construct(
        protected BlogCategoryService $service
    ) {}

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Created successfully'));
    }

    public function update(UpdateCategoryRequest $request, $record): RedirectResponse
    {
        $record = $this->resolveRecord($record);
        $this->service->update($record, $request->validated());

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Updated successfully'));
    }

    protected function tableDefinition(Request $request): ?TableDefinition
    {
        return BlogCategoriesTable::make();
    }

    /**
     * The trait's singular-variable helper would turn "blog-categories" into an
     * odd value; provide the view variable names explicitly.
     */
    protected function getResourceVariable(): string
    {
        return 'categories';
    }

    protected function getSingularVariable(): string
    {
        return 'category';
    }
}
