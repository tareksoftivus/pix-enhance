<x-layouts.admin :title="__('Audit Logs')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Audit Logs') }}</h1>
        </div>

        <div class="section-card">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="mb-6">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-36 flex-1">
                        <label class="block text-sm font-medium text-neutral-700 mb-1">{{ __('Action') }}</label>
                        <x-forms.select name="action">
                            <option value="">{{ __('All Actions') }}</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="min-w-36 flex-1">
                        <label class="block text-sm font-medium text-neutral-700 mb-1">{{ __('Type') }}</label>
                        <x-forms.select name="auditable_type">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('auditable_type') == $type ? 'selected' : '' }}>
                                    {{ class_basename($type) }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="min-w-36 flex-1">
                        <label class="block text-sm font-medium text-neutral-700 mb-1">{{ __('Date From') }}</label>
                        <x-forms.input type="date" name="date_from" :value="request('date_from')" />
                    </div>

                    <div class="min-w-36 flex-1">
                        <label class="block text-sm font-medium text-neutral-700 mb-1">{{ __('Date To') }}</label>
                        <x-forms.input type="date" name="date_to" :value="request('date_to')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn btn-primary py-3">
                            <i class="ph ph-funnel"></i> {{ __('Apply Filters') }}
                        </button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-ghost py-3">
                            <i class="ph ph-x"></i> {{ __('Clear') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="border-t border-neutral-100 pt-4 pb-6">
            <x-tables.table>
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('IP Address') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td data-th="{{ __('User') }}">
                            @if($log->user)
                                <div>
                                    <p class="text-sm font-medium text-neutral-900">{{ $log->user->name }}</p>
                                    <p class="text-xs text-neutral-400">{{ $log->user->email }}</p>
                                </div>
                            @else
                                <span class="text-sm text-neutral-400">{{ __('System') }}</span>
                            @endif
                        </td>
                        <td data-th="{{ __('Action') }}">
                            <div class="flex justify-end lg:justify-start rtl:justify-start">
                                <x-ui.badge :variant="match($log->action) {
                                    'created' => 'success',
                                    'updated' => 'info',
                                    'deleted' => 'danger',
                                    default => 'neutral'
                                }">
                                    {{ ucfirst($log->action) }}
                                </x-ui.badge>
                            </div>
                        </td>
                        <td data-th="{{ __('Type') }}" class="text-sm text-neutral-600">{{ class_basename($log->auditable_type) }}</td>
                        <td data-th="{{ __('IP Address') }}" class="text-sm text-neutral-600">{{ $log->ip_address ?? __('N/A') }}</td>
                        <td data-th="{{ __('Date') }}" class="text-sm text-neutral-400">{{ format_date($log->created_at, true) }}</td>
                        <td data-th="{{ __('Actions') }}" class="text-right">
                            <x-tables.actions>
                                <x-tables.action icon="eye" :href="route('admin.audit-logs.show', $log)" :label="__('View Details')" />
                            </x-tables.actions>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-5 text-center text-neutral-400">{{ __('No audit logs found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </x-tables.table>

            <x-tables.pagination :paginator="$logs" />
            </div>
        </div>
    </div>
</x-layouts.admin>
