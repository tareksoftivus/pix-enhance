<x-layouts.admin :title="__('New Post')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('New Post') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.blog-posts.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <form method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="section-card space-y-4">
                        <x-forms.input :label="__('Title')" name="title" :value="old('title')" required />
                        <x-forms.input :label="__('Slug')" name="slug" :value="old('slug')" :hint="__('Optional — generated from the title if left blank.')" />
                        <x-forms.textarea :label="__('Excerpt')" name="excerpt" :value="old('excerpt')" rows="3" :hint="__('Short summary shown in listings.')" />
                        <x-forms.editor :label="__('Body')" name="body" :value="old('body')" />
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="section-card space-y-4">
                        <x-forms.select
                            :label="__('Status')"
                            name="status"
                            :options="$statuses"
                            :selected="old('status', 'draft')"
                            :placeholder="null"
                            required
                        />
                        <x-forms.select
                            :label="__('Category')"
                            name="blog_category_id"
                            :options="$categories"
                            :selected="old('blog_category_id')"
                            :placeholder="__('Uncategorized')"
                        />
                        <x-forms.file-upload :label="__('Cover Image')" name="cover_image" accept="image/*" :hint="__('JPG, PNG or WebP up to 4MB. Also used as the social share image.')" />
                    </div>

                    <div class="section-card space-y-4">
                        <h2 class="heading-6 text-neutral-950">{{ __('SEO') }}</h2>
                        <x-forms.input :label="__('Meta Title')" name="meta_title" :value="old('meta_title')" :hint="__('Defaults to the post title. Aim for under 60 characters.')" />
                        <x-forms.textarea :label="__('Meta Description')" name="meta_description" :value="old('meta_description')" rows="3" :hint="__('Defaults to the excerpt. Aim for 150–160 characters.')" />
                    </div>

                    <div class="section-card">
                        <div class="flex items-center gap-3">
                            <x-forms.submit :label="__('Create Post')" />
                            <x-ui.button variant="ghost" href="{{ route('admin.blog-posts.index') }}">{{ __('Cancel') }}</x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
