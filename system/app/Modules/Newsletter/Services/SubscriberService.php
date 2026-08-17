<?php

namespace App\Modules\Newsletter\Services;

use App\Modules\Newsletter\Models\Subscriber;
use App\Modules\Shared\Traits\HasCrudOperations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriberService
{
    use HasCrudOperations;

    protected string $model = Subscriber::class;

    /** @var array<string> */
    protected array $searchable = ['email'];

    /** @var array<string> */
    protected array $filterable = ['active'];

    /** @var array<string> */
    protected array $sortable = ['email', 'created_at'];

    protected string $defaultSortBy = 'created_at';

    protected string $defaultSortOrder = 'desc';

    public function subscribe(string $email): Subscriber
    {
        $email = Str::lower(trim($email));

        return Subscriber::query()->updateOrCreate(
            ['email' => $email],
            ['active' => true],
        );
    }

    public function toggleStatus(Model $record): Model
    {
        $record->update(['active' => ! $record->active]);

        return $record->fresh();
    }
}
