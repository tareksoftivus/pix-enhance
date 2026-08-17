<?php

namespace App\Modules\Testimonials\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Shared\Traits\HasCrudActions;
use App\Modules\Testimonials\Http\Requests\StoreTestimonialRequest;
use App\Modules\Testimonials\Http\Requests\UpdateTestimonialRequest;
use App\Modules\Testimonials\Services\TestimonialsService;
use App\Modules\Testimonials\Tables\TestimonialsTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Storage;

class TestimonialsController extends Controller implements HasMiddleware
{
    use HasCrudActions;

    protected string $viewPath = 'testimonials::admin.testimonials';

    protected string $routePrefix = 'admin.testimonials';

    protected string $resourceName = 'testimonials';

    public static function middleware(): array
    {
        return static::crudMiddleware('testimonials');
    }

    public function __construct(
        protected TestimonialsService $service
    ) {}

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['avatar'] = $this->resolveAvatar($request);

        $this->service->create($data);

        return redirect()
            ->route($this->routePrefix.'.index')
            ->with('success', __('Testimonial created successfully.'));
    }

    public function update(UpdateTestimonialRequest $request, $record): RedirectResponse
    {
        $record = $this->resolveRecord($record);
        $data = $request->validated();

        $data['avatar'] = $this->resolveAvatar($request, $record->avatar);

        $this->service->update($record, $data);

        return redirect()
            ->route($this->routePrefix.'.index')
            ->with('success', __('Testimonial updated successfully.'));
    }

    protected function formData(): array
    {
        return [];
    }

    protected function getFilters(Request $request): array
    {
        return [
            'search' => $request->input('search'),
            'active' => $request->input('active'),
            'sort_by' => $request->input('sort_by'),
            'sort_order' => $request->input('sort_order'),
        ];
    }

    protected function tableDefinition(Request $request): ?TableDefinition
    {
        return TestimonialsTable::make();
    }

    /**
     * Resolve avatar from media picker (ID) or file upload (path).
     */
    protected function resolveAvatar(Request $request, ?string $currentAvatar = null): ?string
    {
        if ($request->hasFile('avatar')) {
            if ($currentAvatar && ! is_numeric($currentAvatar)) {
                Storage::disk('public')->delete($currentAvatar);
            }

            return $request->file('avatar')->store('testimonials/avatars', 'public');
        }

        if ($request->filled('avatar') && is_numeric($request->input('avatar'))) {
            return $request->input('avatar');
        }

        return $currentAvatar;
    }
}
