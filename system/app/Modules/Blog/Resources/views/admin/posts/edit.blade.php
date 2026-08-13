<x-layouts.admin :title="__('Edit Post')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('Edit Post') }}</h1>
            <div class="flex items-center gap-3">
                @if($post->isPublished())
                    <x-ui.button variant="outline" href="{{ route('blog.show', $post->slug) }}" target="_blank">
                        <i class="ph ph-arrow-square-out"></i> {{ __('View') }}
                    </x-ui.button>
                @endif
                <x-ui.button variant="outline" href="{{ route('admin.blog-posts.index') }}">
                    <i class="ph ph-arrow-left"></i> {{ __('Back') }}
                </x-ui.button>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.blog-posts.update', $post) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="section-card space-y-4">
                        <x-forms.input :label="__('Title')" name="title" :value="old('title', $post->title)" required />
                        <x-forms.input :label="__('Slug')" name="slug" :value="old('slug', $post->slug)" />
                        <x-forms.textarea :label="__('Excerpt')" name="excerpt" :value="old('excerpt', $post->excerpt)" rows="3" />
                        <x-forms.editor :label="__('Body')" name="body" :value="old('body', $post->body)" />
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="section-card space-y-4">
                        <x-forms.select
                            :label="__('Status')"
                            name="status"
                            :options="$statuses"
                            :selected="old('status', $post->status)"
                            :placeholder="null"
                            required
                        />
                        <x-forms.select
                            :label="__('Category')"
                            name="blog_category_id"
                            :options="$categories"
                            :selected="old('blog_category_id', $post->blog_category_id)"
                            :placeholder="__('Uncategorized')"
                        />

                        @if($post->coverImageUrl())
                            <div>
                                <span class="form-label">{{ __('Current Cover') }}</span>
                                <img src="{{ $post->coverImageUrl() }}" alt="{{ $post->title }}" class="mt-1 h-32 w-full rounded-xl object-cover" />
                            </div>
                        @endif
                        <x-forms.file-upload :label="__('Replace Cover Image')" name="cover_image" accept="image/*" :hint="__('Leave blank to keep the current image.')" />
                    </div>

                    <div class="section-card space-y-4">
                        <h2 class="heading-6 text-neutral-950">{{ __('SEO') }}</h2>
                        <x-forms.input :label="__('Meta Title')" name="meta_title" :value="old('meta_title', $post->meta_title)" :hint="__('Defaults to the post title. Aim for under 60 characters.')" />
                        <x-forms.textarea :label="__('Meta Description')" name="meta_description" :value="old('meta_description', $post->meta_description)" rows="3" :hint="__('Defaults to the excerpt. Aim for 150–160 characters.')" />
                    </div>

                    <div class="section-card">
                        <div class="flex items-center gap-3">
                            <x-forms.submit :label="__('Update Post')" />
                            <x-ui.button variant="ghost" href="{{ route('admin.blog-posts.index') }}">{{ __('Cancel') }}</x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
