{{-- Favicon --}}
@if(setting('site_favicon') && media_url(setting('site_favicon')))
<link rel="icon" href="{{ media_url(setting('site_favicon')) }}">
@else
<link rel="icon" type="image/png" href="{{ asset('assets/uploads/brand/favicon.png') }}">
@endif

{{-- Dynamic Theme Colors (overrides compiled Tailwind defaults) --}}
@if(setting('primary_color', '#5096f2') !== '#5096f2' || setting('secondary_color', '#6366f1') !== '#6366f1')
<style>
    :root, .dark {
        @if(setting('primary_color', '#5096f2') !== '#5096f2')--color-primary: {{ setting('primary_color') }};@endif
        @if(setting('secondary_color', '#6366f1') !== '#6366f1')--color-secondary: {{ setting('secondary_color') }};@endif
    }
</style>
@endif
