@php
    // JSON-LD BlogPosting structured data for search engines.
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->seoTitle(),
        'description' => $post->seoDescription(),
        'url' => route('blog.show', $post->slug),
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at?->toIso8601String(),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.show', $post->slug)],
        'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
    ];

    if ($post->author) {
        $structuredData['author'] = ['@type' => 'Person', 'name' => $post->author->name];
    }

    if ($post->coverImageUrl()) {
        $structuredData['image'] = url($post->coverImageUrl());
    }

    if ($post->category) {
        $structuredData['articleSection'] = $post->category->name;
    }
@endphp

<x-blog::layout
    :title="$post->seoTitle()"
    :description="$post->seoDescription()"
    :canonical="route('blog.show', $post->slug)"
    og-type="article"
    :og-image="$post->coverImageUrl() ? url($post->coverImageUrl()) : null"
    :published-time="$post->published_at?->toIso8601String()"
    :modified-time="$post->updated_at?->toIso8601String()"
    :author="$post->author?->name"
>
    <x-slot:head>
        <script type="application/ld+json"><?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    </x-slot:head>
    <article class="mx-auto max-w-3xl">
        <a href="{{ route('blog.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-neutral-500 hover:text-primary">
            <i class="ph ph-arrow-left"></i> {{ __('Back to Blog') }}
        </a>

        <header class="mb-6">
            @if($post->category)
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                   class="text-xs font-semibold uppercase tracking-wide text-primary hover:underline">
                    {{ $post->category->name }}
                </a>
            @endif
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl">{{ $post->title }}</h1>
            <p class="mt-3 text-sm text-neutral-400">
                @if($post->author)
                    {{ __('By :author', ['author' => $post->author->name]) }} &middot;
                @endif
                {{ $post->published_at?->format('F d, Y') }}
            </p>
        </header>

        @if($post->coverImageUrl())
            <img src="{{ $post->coverImageUrl() }}" alt="{{ $post->title }}"
                 class="mb-8 aspect-video w-full rounded-2xl object-cover" />
        @endif

        {{-- Body is trusted, admin-authored rich text from the WYSIWYG editor. --}}
        <div class="blog-content max-w-none text-neutral-700">
            <?php echo $post->body; ?>
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="mx-auto mt-16 max-w-5xl border-t border-neutral-100 pt-10">
            <h2 class="mb-6 text-xl font-bold text-neutral-950">{{ __('Related Posts') }}</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach($related as $item)
                    <a href="{{ route('blog.show', $item->slug) }}"
                       class="group rounded-2xl border border-neutral-100 bg-neutral-0 p-4 transition-shadow hover:shadow-md">
                        <h3 class="font-semibold text-neutral-950 group-hover:text-primary">{{ $item->title }}</h3>
                        <p class="mt-1 text-xs text-neutral-400">{{ $item->published_at?->format('M d, Y') }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-blog::layout>
