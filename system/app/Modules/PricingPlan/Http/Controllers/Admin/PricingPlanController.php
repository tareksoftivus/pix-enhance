<?php

namespace App\Modules\PricingPlan\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PricingPlan\Http\Requests\StorePricingPlanRequest;
use App\Modules\PricingPlan\Http\Requests\UpdatePricingPlanRequest;
use App\Modules\PricingPlan\Services\PricingPlanService;
use App\Modules\PricingPlan\Tables\PricingPlansTable;
use App\Modules\PricingPlan\Tables\SubscribersTable;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Shared\Traits\HasCrudActions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PricingPlanController extends Controller implements HasMiddleware
{
    use HasCrudActions;

    protected string $viewPath = 'pricing-plans::admin';

    protected string $routePrefix = 'admin.pricing-plans';

    protected string $resourceName = 'pricing_plans';

    public static function middleware(): array
    {
        return array_merge(
            static::crudMiddleware('pricing-plans'),
            [new Middleware('permission:pricing-plans.view', only: ['subscribers'])]
        );
    }

    public function __construct(protected PricingPlanService $service) {}

    public function create(): View
    {
        return view("{$this->viewPath}.create", array_merge(
            ['pricingPlan' => null],
            $this->formData()
        ));
    }

    public function store(StorePricingPlanRequest $request): RedirectResponse
    {
        $this->service->create($this->withParsedFeatures($request->validated()));

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Pricing plan created successfully.'));
    }

    public function update(UpdatePricingPlanRequest $request, $record): RedirectResponse
    {
        $record = $this->resolveRecord($record);
        $this->service->update($record, $this->withParsedFeatures($request->validated()));

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Pricing plan updated successfully.'));
    }

    public function subscribers(Request $request): View|JsonResponse
    {
        $creditTransactionClass = 'App\\Modules\\Credits\\Models\\CreditTransaction';

        if (! class_exists($creditTransactionClass)) {
            $subscribers = new LengthAwarePaginator(
                items: [],
                total: 0,
                perPage: $request->integer('per_page') ?: 15,
                currentPage: LengthAwarePaginator::resolveCurrentPage(),
                options: ['path' => $request->url(), 'query' => $request->query()]
            );

            $table = SubscribersTable::make();

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('components.tables.resource-rows', ['definition' => $table, 'items' => $subscribers])->render(),
                    'pagination' => view('components.tables.pagination', ['paginator' => $subscribers])->render(),
                    'total' => 0,
                ]);
            }

            return view("{$this->viewPath}.subscribers", [
                'subscribers' => $subscribers,
                'stats' => [
                    'total' => 0,
                    'paid' => 0,
                    'free' => 0,
                    'credits_granted' => 0,
                ],
                'table' => $table,
            ]);
        }

        $latestPlanTransactionIds = $creditTransactionClass::query()
            ->selectRaw('MAX(id)')
            ->where('reason', 'pricing_plan_purchase')
            ->groupBy('user_id');

        $baseQuery = $creditTransactionClass::query()
            ->whereIn('id', $latestPlanTransactionIds)
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = (string) $request->input('search');

                $query->whereHas('user', function ($userQuery) use ($search): void {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'paid' => (clone $baseQuery)->whereHasMorph('reference', [Payment::class])->count(),
            'credits_granted' => (clone $baseQuery)->sum('amount'),
        ];
        $stats['free'] = $stats['total'] - $stats['paid'];

        $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';

        $subscribers = (clone $baseQuery)
            ->with(['user', 'reference'])
            ->orderBy('created_at', $sortOrder)
            ->paginate($request->integer('per_page') ?: 15)
            ->withQueryString();

        $table = SubscribersTable::make();

        if ($request->ajax()) {
            $html = view('components.tables.resource-rows', ['definition' => $table, 'items' => $subscribers])->render();
            $pagination = view('components.tables.pagination', ['paginator' => $subscribers])->render();

            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'total' => $subscribers->total(),
            ]);
        }

        return view("{$this->viewPath}.subscribers", [
            'subscribers' => $subscribers,
            'stats' => $stats,
            'table' => $table,
        ]);
    }

    /**
     * Convert the newline-separated features textarea into an array.
     */
    protected function withParsedFeatures(array $data): array
    {
        $data['cta_label'] = $data['cta_label'] ?: 'Start free';
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['features'] = collect(explode("\n", (string) ($data['features'] ?? '')))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return $data;
    }

    protected function tableDefinition(Request $request): ?TableDefinition
    {
        return PricingPlansTable::make();
    }

    protected function getSingularVariable(): string
    {
        return 'pricingPlan';
    }
}
