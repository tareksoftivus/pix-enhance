<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use App\Modules\Shared\Services\SessionService;
use App\Panels\User\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected SessionService $sessionService) {}

    public function edit(): View
    {
        $user = auth()->user();
        $sessions = $this->sessionService->getActiveSessions($user->id);

        return view('panels.user.profile.edit', compact('user', 'sessions'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // A changed address or number was never verified — drop the old stamp
        // so the email/SMS verification gates apply to the new value.
        $emailChanged = $request->email !== $user->email;
        $phoneChanged = $request->phone !== $user->phone;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($phoneChanged) {
            $data['phone_verified_at'] = null;
        }

        $passwordChanged = false;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $passwordChanged = true;
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();

            if (setting('require_email_verification', true)) {
                $user->sendEmailVerificationNotification();
            }
        }

        if ($passwordChanged) {
            Mail::to($user)->queue(new PasswordChangedMail($user, $request->ip()));
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function revokeSession(Request $request, string $sessionId): RedirectResponse
    {
        $user = auth()->user();

        $this->sessionService->revokeSession($sessionId, $user->id);

        return back()->with('success', __('Session revoked successfully.'));
    }

    public function revokeAllSessions(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $this->sessionService->revokeAllOtherSessions($user->id, session()->getId());

        return back()->with('success', __('All other sessions have been revoked.'));
    }
}
