{{--
    Third-party plugin scripts for public-facing pages (frontend + guest/auth).
    Rendered only when the corresponding plugin is enabled in Settings → Plugins.
--}}
@php
    $ga4Enabled = (bool) setting('plugin_ga4_enabled', false);
    $ga4Id = trim((string) setting('plugin_ga4_measurement_id', ''));

    $tawkEnabled = (bool) setting('plugin_tawk_enabled', false);
    $tawkProperty = trim((string) setting('plugin_tawk_property_id', ''));
    $tawkWidget = trim((string) setting('plugin_tawk_widget_id', 'default')) ?: 'default';
@endphp

@if($ga4Enabled && $ga4Id !== '')
    {{-- Google Analytics 4 --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json($ga4Id));
    </script>
@endif

@if($tawkEnabled && $tawkProperty !== '')
    {{-- Tawk.to Live Chat --}}
    <script>
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/' + @json($tawkProperty) + '/' + @json($tawkWidget);
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
@endif
