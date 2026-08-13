<!--
    Brand lockup. One place for the logo so swapping the artwork touches a
    single file rather than the navbar, footer, auth shell and dashboard.

    Props: href, label, class (e.g. "brand-sm" in tighter chrome).
-->
<a class="brand {{ $class ?? '' }}" href="{{ $href ?? '/' }}">
    <img class="brand__mark" src="{{ asset('assets/frontend/enhance/img/logo.png') }}"
         alt="{{ $label ?? __('PixEnhance home') }}" width="400" height="85">
</a>
