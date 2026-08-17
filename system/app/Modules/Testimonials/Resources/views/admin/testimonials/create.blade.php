<x-layouts.admin :title="__('Add Testimonial')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Add Testimonial') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.testimonials.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-6 2xl:grid-cols-2">
            <div class="section-card">
                <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data" class="space-y-4 max-w-4xl">
                    @csrf
                    <x-forms.input :label="__('Client Name')" name="client_name" required :placeholder="__('e.g. Maya Chen')" />
                    <x-forms.input :label="__('Company')" name="company_name" :placeholder="__('e.g. Northstar Labs')" />
                    <x-forms.input :label="__('Designation')" name="designation" :placeholder="__('e.g. Head of Product')" />
                    <x-forms.textarea :label="__('Quote')" name="quote" required :placeholder="__('Write the testimonial quote shown to visitors')" />
                    <x-media.picker :label="__('Avatar')" name="avatar" accept="image" :hint="__('Upload or choose an avatar from the media library. Square image recommended.')" />
                    <x-forms.input :label="__('Rating')" name="rating" type="number" :value="5" min="1" max="5"
                        :placeholder="__('e.g. 5')" hint="{{ __('Optional. Use a value from 1 to 5.') }}" />
                    <x-forms.input :label="__('Sort Order')" name="sort_order" type="number" :value="0"
                        :placeholder="__('e.g. 0')" />
                    <x-forms.toggle :label="__('Active')" name="active" :checked="true" />
                    <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
                        <x-forms.submit :label="__('Create Testimonial')" />
                        <x-ui.button variant="ghost" href="{{ route('admin.testimonials.index') }}">{{ __('Cancel') }}</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
