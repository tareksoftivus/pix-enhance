@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'ogType' => 'website',
    'ogImage' => null,
    'publishedTime' => null,
    'modifiedTime' => null,
    'author' => null,
])

{{--
    Self-contained public layout for the blog. Uses the app's compiled Tailwind
    build so it stays styled and consistent without depending on the active
    frontend theme's section pipeline. Emits the full SEO head: description,
    canonical, robots, Open Graph, Twitter cards, plus a {{ $head }} slot for
    page-specific structured data (JSON-LD).
--}}
@php
    $pageTitle = $title ? $title.' — '.config('app.name') : config('app.name').' — '.__('Blog');
    $canonicalUrl = $canonical ?: url()->current();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>

    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="robots" content="index, follow">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $title ?: config('app.name').' — '.__('Blog') }}">
    @if($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @if($ogType === 'article')
        @if($publishedTime)
            <meta property="article:published_time" content="{{ $publishedTime }}">
        @endif
        @if($modifiedTime)
            <meta property="article:modified_time" content="{{ $modifiedTime }}">
        @endif
        @if($author)
            <meta property="article:author" content="{{ $author }}">
        @endif
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title ?: config('app.name').' — '.__('Blog') }}">
    @if($description)
        <meta name="twitter:description" content="{{ $description }}">
    @endif
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    {{-- Page-specific head additions (e.g. JSON-LD) --}}
    {{ $head ?? '' }}

    <link rel="stylesheet" href="{{ asset('assets/global/fonts/phosphor/regular/style.css') }}">
    @vite(['resources/css/app.css'])
    <x-plugins.head-scripts />
</head>
<body class="min-h-screen bg-neutral-10 text-neutral-900 antialiased">
    <header class="border-b border-neutral-100 bg-neutral-0">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-bold text-neutral-950">
                <i class="ph ph-article text-primary"></i>
                {{ config('app.name') }}
            </a>
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-neutral-500 hover:text-primary">{{ __('Blog') }}</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10">
        {{ $slot }}
    </main>

    <footer class="border-t border-neutral-100 py-8">
        <div class="mx-auto max-w-5xl px-4 text-center text-sm text-neutral-400">
            &copy; {{ now()->year }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
        </div>
    </footer>
</body>
</html>
