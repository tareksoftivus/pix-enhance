<x-layouts.admin :title="__('Refunds')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Refunds') }}</h1>
        </div>

        <div class="section-card">
            <x-tables.datatable :url="route('admin.refunds.index')">
                <x-tables.table>
                    <thead>
                        <tr>
                            <th>{{ __('Payment') }}</th>
                            <x-tables.heading field="amount" sortable>{{ __('Amount') }}</x-tables.heading>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <x-tables.heading field="created_at" sortable>{{ __('Date') }}</x-tables.heading>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody data-datatable-body>
                        @include('payment-gateways::admin.refunds._table-rows')
                    </tbody>
                </x-tables.table>

                <x-slot:pagination>
                    <x-tables.pagination :paginator="$refunds" />
                </x-slot:pagination>
            </x-tables.datatable>
        </div>
    </div>
</x-layouts.admin>
