<?php

namespace App\Modules\Credits\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Http\Requests\StoreCreditAdjustmentRequest;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Models\CreditWallet;
use App\Modules\Credits\Services\CreditService;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CreditTransactionsController extends Controller implements HasMiddleware
{
    public function __construct(
        protected CreditService $credits,
        protected UserSystemNotificationService $systemNotifications
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:credits.view', only: ['index']),
            new Middleware('permission:credits.adjust', only: ['storeAdjustment']),
        ];
    }

    public function index(Request $request): View
    {
        $transactions = $this->credits->listTransactions([
            'search' => $request->input('search'),
            'reason' => $request->input('reason'),
        ], $request->integer('per_page') ?: 15);

        $stats = [
            'wallets' => CreditWallet::query()->count(),
            'available' => CreditWallet::query()->sum('balance'),
            'reserved' => CreditWallet::query()->sum('reserved_balance'),
            'transactions' => CreditTransaction::query()->count(),
        ];

        return view('credits::admin.index', compact('transactions', 'stats'));
    }

    public function storeAdjustment(StoreCreditAdjustmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::query()->findOrFail($data['user_id']);
        $amount = (int) $data['amount'];
        $metadata = [
            'note' => $data['note'] ?? null,
            'admin_id' => auth('admin')->id(),
        ];

        try {
            if ($amount > 0) {
                $transaction = $this->credits->grant($user, $amount, 'admin_adjustment', null, $metadata);
                $this->systemNotifications->creditsGranted($user, $transaction);
            } else {
                $transaction = $this->credits->spend($user, abs($amount), 'admin_adjustment', null, $metadata);
                $this->systemNotifications->creditsRevoked($user, $transaction);
                $this->systemNotifications->creditsLow($user, $this->credits->summaryFor($user)['available']);
            }
        } catch (InsufficientCreditsException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return back()->with('success', __('Credit adjustment saved.'));
    }
}
