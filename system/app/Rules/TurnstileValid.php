<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

/**
 * Validates a Cloudflare Turnstile challenge response against the siteverify API.
 *
 * The rule is a no-op when the Turnstile plugin is disabled or unconfigured,
 * so forms keep working when bot protection is turned off.
 */
class TurnstileValid implements ValidationRule
{
    /**
     * Run even when the field is empty, so a missing challenge response is
     * rejected while Turnstile is enabled.
     */
    public bool $implicit = true;

    protected string $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! (bool) setting('plugin_turnstile_enabled', false)) {
            return;
        }

        $secret = trim((string) setting('plugin_turnstile_secret_key', ''));

        if ($secret === '') {
            return;
        }

        if (! is_string($value) || $value === '') {
            $fail(__('Please complete the verification challenge.'));

            return;
        }

        try {
            $response = Http::asForm()->post($this->verifyUrl, [
                'secret' => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if ($response->json('success') !== true) {
                $fail(__('Verification failed. Please try again.'));
            }
        } catch (Throwable) {
            $fail(__('Could not verify the challenge. Please try again.'));
        }
    }
}
