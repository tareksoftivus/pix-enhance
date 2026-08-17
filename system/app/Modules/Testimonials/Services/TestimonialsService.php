<?php

namespace App\Modules\Testimonials\Services;

use App\Modules\Shared\Traits\HasCrudOperations;
use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TestimonialsService
{
    use HasCrudOperations;

    protected string $model = Testimonial::class;

    /** @var array<string> */
    protected array $searchable = ['client_name', 'company_name', 'designation', 'quote'];

    /** @var array<string> */
    protected array $filterable = ['active'];

    /** @var array<string> */
    protected array $sortable = ['client_name', 'rating', 'sort_order', 'created_at'];

    protected string $defaultSortBy = 'sort_order';

    protected string $defaultSortOrder = 'asc';

    public function create(array $data): Model
    {
        return ($this->model)::create($this->payload($data));
    }

    public function update(Model $record, array $data): Model
    {
        $record->update($this->payload($data));

        return $record->fresh();
    }

    public function toggleStatus(Model $record): Model
    {
        $record->update(['active' => ! $record->active]);

        return $record->fresh();
    }

    /**
     * @return Collection<int, Testimonial>
     */
    public function activeTestimonials(): Collection
    {
        return Testimonial::query()
            ->active()
            ->orderBy('sort_order')
            ->orderByDesc('rating')
            ->orderBy('client_name')
            ->get();
    }

    protected function payload(array $data): array
    {
        return [
            'client_name' => $data['client_name'],
            'company_name' => $data['company_name'] ?? null,
            'designation' => $data['designation'] ?? null,
            'quote' => $data['quote'],
            'rating' => $data['rating'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'active' => (bool) ($data['active'] ?? true),
            'avatar' => $data['avatar'] ?? null,
        ];
    }
}
