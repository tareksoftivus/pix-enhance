@php
    $sectionData = old('data', $section?->data ?? []);
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <input type="hidden" name="type" value="{{ $selectedType }}">

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="section-card space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-500">{{ __('Section Details') }}</h4>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-forms.input :label="__('Name')" name="name" :value="old('name', $section?->name)" required />
                    <x-forms.input :label="__('Slug')" name="slug" :value="old('slug', $section?->slug)" :hint="__('Leave empty to auto-generate from name')" />
                </div>

                <div>
                    <x-media.picker
                        :label="__('Preview Image')"
                        name="preview_image_media_id"
                        :value="old('preview_image_media_id', $section?->preview_image_media_id)"
                        accept="image"
                        :hint="__('Internal thumbnail for this section in admin listings. Recommended size: 1200 x 675 px. Use JPG, PNG, or WebP.')"
                    />
                </div>

                <x-forms.textarea :label="__('Description')" name="description" :value="old('description', $section?->description)" :hint="__('Internal note for editors and developers.')" rows="3" />

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-forms.select
                        :label="__('Status')"
                        name="status"
                        :selected="old('status', $section?->status ?? 'draft')"
                        :options="['draft' => __('Draft'), 'published' => __('Published'), 'archived' => __('Archived')]"
                    />
                </div>
            </div>

            <div class="section-card space-y-5">
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-500">{{ __('Section Schema') }}</h4>
                    <p class="mt-1 text-sm text-neutral-500">{{ $definition['description'] ?? __('Fill the values required by this section type.') }}</p>
                </div>

                <div class="space-y-5">
                    @foreach(($definition['fields'] ?? []) as $fieldKey => $field)
                        <x-forms.schema-field
                            :field="$field"
                            :name="'data[' . $fieldKey . ']'"
                            :error-key="'data.' . $fieldKey"
                            :value="$sectionData[$fieldKey] ?? ($field['default'] ?? null)"
                        />
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="section-card space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-500">{{ __('Compatibility') }}</h4>
                <p class="text-sm text-neutral-500">{{ __('This section type is currently supported by the following themes.') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(($definition['supported_themes'] ?? []) as $themeKey)
                        <x-ui.badge variant="primary">{{ $themeLabels[$themeKey] ?? ucfirst($themeKey) }}</x-ui.badge>
                    @endforeach
                </div>
            </div>

            @if($section)
                <div class="section-card space-y-3">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-500">{{ __('Usage') }}</h4>
                    <p class="text-sm text-neutral-600">{{ trans_choice(':count page currently uses this section.|:count pages currently use this section.', $section->pages()->count(), ['count' => $section->pages()->count()]) }}</p>
                </div>
            @endif

            <div class="section-card">
                <div class="flex items-center gap-3">
                    <x-forms.submit :label="$section ? __('Save Changes') : __('Create Section')" />
                    <x-ui.button variant="ghost" href="{{ route('admin.frontend-sections.index') }}">{{ __('Cancel') }}</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</form>
