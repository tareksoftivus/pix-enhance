<?php

namespace App\Modules\PaymentGateways\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\PaymentGateways\Services\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class RefundsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:refunds.view', only: ['index', 'show']),
        ];
    }

    public function __construct(
        protected RefundService $service
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
        ];

        $perPage = $request->integer('per_page') ?: null;
        $refunds = $this->service->listPaginated($filters, $perPage);

        if ($request->ajax()) {
            $html = view('payment-gateways::admin.refunds._table-rows', compact('refunds'))->render();
            $pagination = view('components.tables.pagination', ['paginator' => $refunds])->render();

            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'total' => $refunds->total(),
            ]);
        }

        return view('payment-gateways::admin.refunds.index', compact('refunds'));
    }

    public function show(Refund $refund): View
    {
        $refund->load('payment');

        return view('payment-gateways::admin.refunds.show', compact('refund'));
    }
}
