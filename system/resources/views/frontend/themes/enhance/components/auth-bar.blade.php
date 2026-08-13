<div class="auth__bar">
    @include('frontend.themes.enhance.components.brand')

    <a class="auth__back" href="{{ $backHref ?? route('home') }}">
        <i data-lucide="arrow-left"></i>
        {{ $backLabel ?? __('Back to site') }}
    </a>
</div>
