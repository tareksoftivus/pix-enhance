{{--
    Cloudflare Turnstile widget for auth forms. Renders the challenge widget
    and loads the Turnstile script only when the plugin is enabled and a site
    key is configured. Place inside a <form>; it adds the cf-turnstile-response
    field the server verifies.
--}}
@php
    $turnstileEnabled = (bool) setting('plugin_turnstile_enabled', false);
    $turnstileSiteKey = trim((string) setting('plugin_turnstile_site_key', ''));
@endphp

@if($turnstileEnabled && $turnstileSiteKey !== '')
    <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
    @error('cf-turnstile-response')
        <p class="form-error">{{ $message }}</p>
    @enderror
    @once
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endonce
@endif
