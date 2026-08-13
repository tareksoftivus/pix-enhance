<x-layouts.user :title="__('New Ticket')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('New Ticket') }}</h1>
            <x-ui.button variant="outline" href="{{ route('user.support-tickets.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <form method="POST" action="{{ route('user.support-tickets.store') }}" class="max-w-2xl space-y-4">
                @csrf
                <x-forms.input :label="__('Subject')" name="subject" :value="old('subject')" required />
                <x-forms.select
                    :label="__('Priority')"
                    name="priority"
                    :options="$priorities"
                    :selected="old('priority', 'medium')"
                    :placeholder="null"
                    required
                />
                <x-forms.input :label="__('Category')" name="category" :value="old('category')" :hint="__('Optional')" />
                <x-forms.textarea :label="__('Message')" name="body" :value="old('body')" rows="6" required />
                <div class="flex items-center gap-3 border-t border-neutral-100 pt-4">
                    <x-forms.submit :label="__('Submit Ticket')" />
                    <x-ui.button variant="ghost" href="{{ route('user.support-tickets.index') }}">{{ __('Cancel') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.user>
