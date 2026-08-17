<?php

namespace App\Modules\Newsletter\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Newsletter\Services\SubscriberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(
        protected SubscriberService $subscribers
    ) {}

    public function subscribe(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $this->subscribers->subscribe($data['email']);

        $message = __('Thanks for subscribing. We will send product updates and image workflow tips.');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('newsletter_success', $message);
    }
}
