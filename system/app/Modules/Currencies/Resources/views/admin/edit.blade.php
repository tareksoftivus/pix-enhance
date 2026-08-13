<x-layouts.admin :title="__('Edit Currency')">
    <div class="max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Edit Currency') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.currencies.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <form method="POST" action="{{ route('admin.currencies.update', $currency) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-forms.input :label="__('Code')" name="code" :value="$currency->code" required maxlength="3" :placeholder="__('e.g. USD')" :hint="__('ISO 4217 three-letter code.')" class="uppercase" />
                    <x-forms.input :label="__('Symbol')" name="symbol" :value="$currency->symbol" required :placeholder="__('e.g. $')" :hint="__('Shown before amounts, e.g. S$ for Singapore Dollar.')" />
                </div>
                <x-forms.input :label="__('Name')" name="name" :value="$currency->name" required :placeholder="__('e.g. US Dollar')" />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-forms.input :label="__('Exchange Rate')" name="exchange_rate" type="number" step="0.000001" :value="$currency->exchange_rate" :placeholder="__('e.g. 1.000000')" :hint="__('Rate relative to the base currency.')" />
                    <x-forms.input :label="__('Sort Order')" name="sort_order" type="number" :value="$currency->sort_order" :placeholder="__('e.g. 0')" :hint="__('Lower numbers appear first.')" />
                </div>
                <x-forms.toggle :label="__('Active')" name="is_active" :checked="$currency->is_active" />
                <div class="flex items-center gap-3 border-t border-neutral-100 pt-5">
                    <x-forms.submit :label="__('Update Currency')" />
                    <x-ui.button variant="ghost" href="{{ route('admin.currencies.index') }}">{{ __('Cancel') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
