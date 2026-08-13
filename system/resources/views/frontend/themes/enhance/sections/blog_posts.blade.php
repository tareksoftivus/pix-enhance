@php
    $featuredPost = $posts->currentPage() === 1 ? $posts->first() : null;
    $fallbackImages = [
        asset('assets/frontend/enhance/img/samples/thumb-1.webp'),
        asset('assets/frontend/enhance/img/samples/thumb-2.webp'),
        asset('assets/frontend/enhance/img/samples/thumb-3.webp'),
        asset('assets/frontend/enhance/img/samples/thumb-4.webp'),
    ];
@endphp

<section class="blog-section">
    <div class="shell">
        <div class="blog-toolbar">
            <div>
                <p class="text-eyebrow">{{ __('Explore') }}</p>
                <h2 class="text-display-3">{{ __('Latest articles') }}</h2>
            </div>

            @if($categories->isNotEmpty())
                <nav class="blog-filters" aria-label="{{ __('Blog categories') }}">
                    <a href="{{ route('blog.index') }}" class="blog-filter {{ ! $activeCategory ? 'is-active' : '' }}">
                        {{ __('All') }}
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                           class="blog-filter {{ $activeCategory?->id === $category->id ? 'is-active' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>

        @if($posts->isNotEmpty())
            @if($featuredPost)
                @php
                    $featuredImage = $featuredPost->coverImageUrl() ?: $fallbackImages[0];
                @endphp
                <article class="blog-featured card card-glass card-flush card-hover card-hover-glow">
                    <a class="blog-featured__media" href="{{ route('blog.show', $featuredPost->slug) }}">
                        <img src="{{ $featuredImage }}" alt="{{ $featuredPost->title }}" loading="eager">
                    </a>
                    <div class="blog-featured__content">
                        <div class="blog-meta">
                            @if($featuredPost->category)
                                <a href="{{ route('blog.index', ['category' => $featuredPost->category->slug]) }}">{{ $featuredPost->category->name }}</a>
                            @endif
                            <span>{{ $featuredPost->published_at?->format('M d, Y') }}</span>
                        </div>
                        <h2 class="blog-featured__title">
                            <a href="{{ route('blog.show', $featuredPost->slug) }}">{{ $featuredPost->title }}</a>
                        </h2>
                        @if($featuredPost->excerpt)
                            <p class="text-lead">{{ $featuredPost->excerpt }}</p>
                        @endif
                        <a class="blog-card__action" href="{{ route('blog.show', $featuredPost->slug) }}">
                            {{ __('Read article') }}
                            <i data-lucide="arrow-right"></i>
                        </a>
                    </div>
                </article>
            @endif

            <div class="blog-grid">
                @foreach($posts as $post)
                    @continue($featuredPost && $post->is($featuredPost))
                    @php
                        $image = $post->coverImageUrl() ?: $fallbackImages[$loop->index % count($fallbackImages)];
                    @endphp
                    <article class="blog-card card card-glass card-flush card-hover card-hover-glow">
                        <a class="blog-card__link" href="{{ route('blog.show', $post->slug) }}">
                            <div class="blog-card__media">
                                <img src="{{ $image }}" alt="{{ $post->title }}" loading="lazy">
                            </div>
                            <div class="blog-card__body">
                                <div class="blog-meta">
                                    @if($post->category)
                                        <span>{{ $post->category->name }}</span>
                                    @endif
                                    <span>{{ $post->published_at?->format('M d, Y') }}</span>
                                </div>
                                <h2 class="blog-card__title">{{ $post->title }}</h2>
                                @if($post->excerpt)
                                    <p class="blog-card__excerpt">{{ $post->excerpt }}</p>
                                @endif
                                <span class="blog-card__action">
                                    {{ __('Read more') }}
                                    <i data-lucide="arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="blog-pagination">
                {{ $posts->withQueryString()->links() }}
            </div>
        @else
            <div class="blog-empty">
                <div>
                    <span class="blog-empty__icon" aria-hidden="true"><i data-lucide="newspaper"></i></span>
                    <h2 class="text-title">{{ __('No posts published yet.') }}</h2>
                    <p class="text-body">{{ __('The journal is ready. Published posts will appear here automatically.') }}</p>
                </div>
            </div>
        @endif
    </div>
</section>
