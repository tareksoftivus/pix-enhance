@php
    /**
     * Renders a "continue with …" button for each enabled + configured social
     * provider. Mirrors SocialLoginController::isEnabled() so buttons only show
     * for providers that will actually work.
     */
    $providers = collect([
        ['key' => 'google',   'label' => 'Google',   'icon' => 'ph ph-google-logo',   'color' => '#EA4335'],
        ['key' => 'facebook', 'label' => 'Facebook', 'icon' => 'ph ph-facebook-logo', 'color' => '#1877F2'],
        ['key' => 'github',   'label' => 'GitHub',   'icon' => 'ph ph-github-logo',   'color' => '#181717'],
    ])->filter(function ($p) {
        return (bool) setting("social_{$p['key']}_enabled", false)
            && (string) config("services.{$p['key']}.client_id") !== ''
            && (string) config("services.{$p['key']}.client_secret") !== '';
    });
@endphp

@if($providers->isNotEmpty())
    <div class="my-6 flex items-center gap-3">
        <span class="h-px flex-1 bg-neutral-100"></span>
        <span class="text-xs font-medium uppercase tracking-wide text-neutral-400">{{ __('or continue with') }}</span>
        <span class="h-px flex-1 bg-neutral-100"></span>
    </div>

    <div class="flex flex-col gap-3">
        @foreach($providers as $provider)
            <a href="{{ route('social.redirect', $provider['key']) }}"
               class="inline-flex items-center justify-center gap-2.5 rounded-xl border border-neutral-200 bg-neutral-0 px-4 py-2.5 text-sm font-semibold text-neutral-700 transition-colors duration-150 hover:bg-neutral-50">
                <i class="{{ $provider['icon'] }} text-lg" style="color: {{ $provider['color'] }};"></i>
                {{ __('Continue with :provider', ['provider' => $provider['label']]) }}
            </a>
        @endforeach
    </div>
@endif
