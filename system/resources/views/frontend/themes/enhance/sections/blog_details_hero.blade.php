<article class="blog-article">
    <div class="grid-overlay grid-overlay-fine" aria-hidden="true"></div>

    <div class="shell blog-article__shell">
        <a href="{{ route('blog.index') }}" class="blog-back">
            <i data-lucide="arrow-left"></i>
            {{ __('Back to blog') }}
        </a>

        <header class="blog-article__header">
            <div class="blog-meta">
                @if($post->category)
                    <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a>
                @endif
                <span>{{ $post->published_at?->format('F d, Y') }}</span>
                <span>{{ trans_choice(':count min read', $readingMinutes, ['count' => $readingMinutes]) }}</span>
                @if($post->author)
                    <span>{{ __('By :author', ['author' => $post->author->name]) }}</span>
                @endif
            </div>

            <h1 class="blog-article__title">{{ $post->title }}</h1>

            @if($post->excerpt)
                <p class="text-lead blog-article__dek">{{ $post->excerpt }}</p>
            @endif
        </header>

        <figure class="blog-article__media">
            <img src="{{ $articleImage }}" alt="{{ $post->title }}" loading="eager">
        </figure>
    </div>
</article>
