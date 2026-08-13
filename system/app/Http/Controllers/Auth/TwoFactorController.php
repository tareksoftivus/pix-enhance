<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(
        protected Google2FA $google2fa,
    ) {}

    public function setup(Request $request): View|RedirectResponse
    {
        $panel = $this->getPanel();
        $user = $this->getUser($panel);

        if (! $this->is2faEnabled($panel)) {
            return redirect()->route("{$panel['key']}.profile.edit");
        }

        $secret = $this->google2fa->generateSecretKey();
        session(['2fa_setup_secret' => $secret]);

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'Admin Panel'),
            $user->email,
            $secret,
        );

        $qrCodeSvg = $this->generateQrCodeSvg($qrCodeUrl);

        return view("panels.{$panel['key']}.profile.two-factor-setup", [
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $secret,
            'user' => $user,
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $panel = $this->getPanel();

        if (! $this->is2faEnabled($panel)) {
            return redirect()->route("{$panel['key']}.profile.edit");
        }

        $request->validate([
            'code' => 'required|string|digits:6',
        ]);

        $secret = session('2fa_setup_secret');

        if (! $secret) {
            return back()->withErrors(['code' => __('Two-factor setup session has expired. Please try again.')]);
        }

        $isValid = $this->google2fa->verifyKey($secret, $request->input('code'));

        if (! $isValid) {
            return back()->withErrors(['code' => __('The provided code is invalid. Please try again.')]);
        }

        $panel = $this->getPanel();
        $user = $this->getUser($panel);
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        session()->forget('2fa_setup_secret');
        session(['2fa_verified' => true]);

        return redirect()->route("{$panel['key']}.profile.edit")
            ->with('success', __('Two-factor authentication has been enabled.'))
            ->with('recovery_codes', $recoveryCodes);
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $panel = $this->getPanel();
        $user = $this->getUser($panel);

        if (! Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => __('The provided password is incorrect.')]);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        session()->forget('2fa_verified');

        return redirect()->route("{$panel['key']}.profile.edit")
            ->with('success', __('Two-factor authentication has been disabled.'));
    }

    public function challenge(): View|RedirectResponse
    {
        $panel = $this->getPanel();
        $user = $this->getUser($panel);

        if (! $user) {
            return redirect()->route("{$panel['key']}.login");
        }

        if (! $user->hasTwoFactorEnabled() || ! $user->hasConfirmedTwoFactor()) {
            return redirect()->route("{$panel['key']}.dashboard");
        }

        if (session('2fa_verified')) {
            return redirect()->route("{$panel['key']}.dashboard");
        }

        $panelKey = $panel['key'];

        return view('auth.two-factor-challenge', compact('panelKey'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|digits:6',
        ]);

        $panel = $this->getPanel();
        $user = $this->getUser($panel);

        if (! $user) {
            return redirect()->route("{$panel['key']}.login");
        }

        $isValid = $this->google2fa->verifyKey(
            $user->two_factor_secret,
            $request->input('code'),
        );

        if (! $isValid) {
            return back()->withErrors(['code' => __('The provided two-factor code is invalid.')]);
        }

        session(['2fa_verified' => true]);

        return redirect()->intended(route("{$panel['key']}.dashboard"));
    }

    public function verifyRecoveryCode(Request $request): RedirectResponse
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $panel = $this->getPanel();
        $user = $this->getUser($panel);

        if (! $user) {
            return redirect()->route("{$panel['key']}.login");
        }

        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        $inputCode = $request->input('recovery_code');

        if (! in_array($inputCode, $recoveryCodes)) {
            return back()->withErrors(['recovery_code' => __('The provided recovery code is invalid.')]);
        }

        $remainingCodes = array_values(array_filter($recoveryCodes, fn ($code) => $code !== $inputCode));

        $user->update([
            'two_factor_recovery_codes' => $remainingCodes,
        ]);

        session(['2fa_verified' => true]);

        return redirect()->intended(route("{$panel['key']}.dashboard"));
    }

    /**
     * Get current panel configuration.
     *
     * @return array{key: string, guard: string}
     */
    protected function getPanel(): array
    {
        $panel = app('current.panel');

        if (! $panel) {
            return ['key' => 'admin', 'guard' => 'admin'];
        }

        return $panel;
    }

    /**
     * Get authenticated user for the current panel guard.
     */
    protected function getUser(array $panel): mixed
    {
        return Auth::guard($panel['guard'] ?? 'web')->user();
    }

    /**
     * Check if 2FA is enabled for the given panel.
     */
    protected function is2faEnabled(array $panel): bool
    {
        if (($panel['guard'] ?? 'web') === 'admin') {
            return true; // Always available for admins
        }

        return (bool) setting('enable_2fa_for_users', true);
    }

    protected function generateQrCodeSvg(string $qrCodeUrl): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(192),
            new SvgImageBackEnd,
        );

        $writer = new Writer($renderer);

        return $writer->writeString($qrCodeUrl);
    }

    /**
     * @return array<int, string>
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4));
        }

        return $codes;
    }
}
