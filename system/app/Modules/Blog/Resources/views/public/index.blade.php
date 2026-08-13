<x-blog::layout
    :title="$activeCategory ? $activeCategory->name.' — '.__('Blog') : __('Blog')"
    :description="__('News, updates and stories from :name.', ['name' => config('app.name')])"
    :canonical="$activeCategory ? route('blog.index', ['category' => $activeCategory->slug]) : route('blog.index')"
>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-neutral-950">{{ __('Blog') }}</h1>
        <p class="mt-2 text-neutral-500">{{ __('News, updates and stories.') }}</p>
    </div>

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
        <div class="mb-8 flex flex-wrap gap-2">
            <a href="{{ route('blog.index') }}"
               @class([
                   'rounded-full px-4 py-1.5 text-sm font-medium transition-colors',
                   'bg-primary text-white' => ! $activeCategory,
                   'bg-neutral-0 text-neutral-600 hover:bg-neutral-100' => (bool) $activeCategory,
               ])>
                {{ __('All') }}
            </a>
            @foreach($categories as $category)
                <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                   @class([
                       'rounded-full px-4 py-1.5 text-sm font-medium transition-colors',
                       'bg-primary text-white' => $activeCategory?->id === $category->id,
                       'bg-neutral-0 text-neutral-600 hover:bg-neutral-100' => $activeCategory?->id !== $category->id,
                   ])>
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    @endif

    @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                <article class="group overflow-hidden rounded-2xl border border-neutral-100 bg-neutral-0 transition-shadow hover:shadow-md">
                    <a href="{{ route('blog.show', $post->slug) }}" class="block">
                        <div class="aspect-video overflow-hidden bg-neutral-100">
                            @if($post->coverImageUrl())
                                <img src="{{ $post->coverImageUrl() }}" alt="{{ $post->title }}"
                                     class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                            @else
                                <div class="flex h-full w-full items-center justify-center text-neutral-300">
                                    <i class="ph ph-image text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            @if($post->category)
                                <span class="text-xs font-semibold uppercase tracking-wide text-primary">{{ $post->category->name }}</span>
                            @endif
                            <h2 class="mt-1 text-lg font-semibold text-neutral-950 group-hover:text-primary">{{ $post->title }}</h2>
                            @if($post->excerpt)
                                <p class="mt-2 line-clamp-2 text-sm text-neutral-500">{{ $post->excerpt }}</p>
                            @endif
                            <p class="mt-3 text-xs text-neutral-400">{{ $post->published_at?->format('M d, Y') }}</p>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->withQueryString()->links() }}
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-neutral-200 py-16 text-center text-neutral-400">
            {{ __('No posts published yet.') }}
        </div>
    @endif
</x-blog::layout>
