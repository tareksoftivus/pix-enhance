@if($related->isNotEmpty())
    <section class="blog-related">
        <div class="shell">
            <div class="blog-related__head">
                <div>
                    <p class="text-eyebrow">{{ __('Keep reading') }}</p>
                    <h2 class="text-display-3">{{ __('Related articles') }}</h2>
                </div>
                <a class="btn-link" href="{{ route('blog.index') }}">
                    {{ __('View all') }}
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            <div class="blog-related__grid">
                @foreach($related as $item)
                    <a href="{{ route('blog.show', $item->slug) }}" class="related-card card card-glass card-hover card-hover-glow">
                        <div class="blog-meta">
                            @if($item->category)
                                <span>{{ $item->category->name }}</span>
                            @endif
                            <span>{{ $item->published_at?->format('M d, Y') }}</span>
                        </div>
                        <h3 class="related-card__title">{{ $item->title }}</h3>
                        @if($item->excerpt)
                            <p class="blog-card__excerpt">{{ $item->excerpt }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
