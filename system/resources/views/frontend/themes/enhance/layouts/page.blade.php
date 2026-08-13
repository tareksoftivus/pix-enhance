@php
    $pageTitle = $page->meta_title ?: $page->title ?: __('PixEnhance — AI Image Upscaler & Enhancer');
    $pageDescription = $page->meta_description ?: __('Upscale, restore and enhance any image up to 16K with state-of-the-art AI models.');
    $canonicalUrl = $canonicalUrl ?? url()->current();
    $openGraphType = $openGraphType ?? 'website';
    $openGraphImage = $openGraphImage ?? asset('assets/frontend/enhance/img/og-image.svg');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#09090b">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="PixEnhance">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="{{ $openGraphType }}">
    <meta property="og:site_name" content="PixEnhance">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $openGraphImage }}">
    @if($openGraphType === 'article')
        @if(! empty($publishedTime))
            <meta property="article:published_time" content="{{ $publishedTime }}">
        @endif
        @if(! empty($modifiedTime))
            <meta property="article:modified_time" content="{{ $modifiedTime }}">
        @endif
        @if(! empty($authorName))
            <meta property="article:author" content="{{ $authorName }}">
        @endif
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $openGraphImage }}">
    @if(! empty($structuredData))
        <script type="application/ld+json"><?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    @endif
    <link rel="icon" href="{{ asset('assets/frontend/enhance/favicon.png') }}" type="image/png" sizes="96x96">
    <link rel="apple-touch-icon" href="{{ asset('assets/frontend/enhance/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap">
    @if(file_exists(public_path('assets/build/manifest.json')))
        @vite('resources/js/frontend/enhance/main.js')
    @endif
    <x-plugins.head-scripts />
</head>
<body>
    <a class="skip-link" href="#main">{{ __('Skip to content') }}</a>
    @include('frontend.themes.enhance.components.svg-defs')
    @includeFirst([$theme['view_namespace'].'.navigation.header', 'frontend.shared.navigation.header'], ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])
    <main id="main" class="{{ $mainClass ?? '' }}">
        @foreach($resolvedSections as $resolved)
            @include($resolved['view'], ['section' => $resolved['section'], 'themeKey' => $themeKey, 'themeVars' => $themeVars, 'supported' => $resolved['supported']])
        @endforeach
    </main>
    @includeFirst([$theme['view_namespace'].'.navigation.footer', 'frontend.shared.navigation.footer'], ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])
    @include('frontend.themes.enhance.components.toast-region')
</body>
</html>
