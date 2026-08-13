@php
    $providers = $providers ?? collect([
        ['key' => 'google', 'label' => 'Google'],
        ['key' => 'github', 'label' => 'GitHub'],
    ])->filter(function (array $provider) {
        return (bool) setting("social_{$provider['key']}_enabled", false)
            && (string) config("services.{$provider['key']}.client_id") !== ''
            && (string) config("services.{$provider['key']}.client_secret") !== '';
    });
@endphp

@if($providers->isNotEmpty())
    <div class="auth-social">
        @foreach($providers as $provider)
            <a class="btn btn-outline btn-block" href="{{ route('social.redirect', $provider['key']) }}">
                @if($provider['key'] === 'google')
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="#4285F4" d="M23.06 12.25c0-.85-.08-1.67-.22-2.45H12v4.64h6.2a5.3 5.3 0 0 1-2.3 3.48v2.89h3.72c2.18-2 3.44-4.96 3.44-8.46Z"/>
                        <path fill="#34A853" d="M12 23.5c3.11 0 5.72-1.03 7.62-2.79l-3.72-2.89c-1.03.69-2.35 1.1-3.9 1.1-3 0-5.54-2.03-6.45-4.75H1.7v2.98A11.5 11.5 0 0 0 12 23.5Z"/>
                        <path fill="#FBBC05" d="M5.55 14.17a6.9 6.9 0 0 1 0-4.34V6.85H1.7a11.5 11.5 0 0 0 0 10.3l3.85-2.98Z"/>
                        <path fill="#EA4335" d="M12 5.08c1.69 0 3.21.58 4.4 1.72l3.3-3.3C17.71 1.63 15.1.5 12 .5A11.5 11.5 0 0 0 1.7 6.85l3.85 2.98C6.46 7.11 9 5.08 12 5.08Z"/>
                    </svg>
                @else
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                        <path d="M12 2a10 10 0 0 0-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.89 1.53 2.34 1.09 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.56-1.11-4.56-4.95 0-1.09.39-1.99 1.03-2.69-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.03a9.5 9.5 0 0 1 5 0c1.91-1.3 2.75-1.03 2.75-1.03.55 1.38.2 2.4.1 2.65.64.7 1.03 1.6 1.03 2.69 0 3.85-2.34 4.7-4.57 4.95.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10 10 0 0 0 12 2Z"/>
                    </svg>
                @endif
                {{ $provider['label'] }}
            </a>
        @endforeach
    </div>
@endif
