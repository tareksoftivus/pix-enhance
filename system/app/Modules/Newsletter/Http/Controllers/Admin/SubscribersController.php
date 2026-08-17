<?php

namespace App\Modules\Newsletter\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Newsletter\Services\SubscriberService;
use App\Modules\Newsletter\Tables\SubscribersTable;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Shared\Traits\HasCrudActions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class SubscribersController extends Controller implements HasMiddleware
{
    use HasCrudActions;

    protected string $viewPath = 'newsletter::admin.subscribers';

    protected string $routePrefix = 'admin.subscribers';

    protected string $resourceName = 'subscribers';

    public static function middleware(): array
    {
        return static::crudMiddleware('newsletter');
    }

    public function __construct(
        protected SubscriberService $service
    ) {}

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
        return SubscribersTable::make();
    }
}
