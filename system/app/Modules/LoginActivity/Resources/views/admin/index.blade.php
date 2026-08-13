<x-layouts.admin :title="__('Login Activity')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Login Activity') }}</h1>
        </div>

        <div class="section-card">
            <form method="GET" action="{{ route('admin.login-activity.index') }}" class="mb-6">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-36 flex-1">
                        <label class="block text-sm font-medium text-neutral-700 mb-1">{{ __('Event') }}</label>
                        <x-forms.select name="event">
                            <option value="">{{ __('All Events') }}</option>
                            @foreach(['login', 'logout', 'failed', 'lockout'] as $event)
                                <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                    {{ ucfirst($event) }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="min-w-36 flex-1">
                        <label class="block text-sm font-medium text-neutral-700 mb-1">{{ __('User Type') }}</label>
                        <x-forms.select name="user_type">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="App\Models\Admin" {{ request('user_type') == 'App\Models\Admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                            <option value="App\Models\User" {{ request('user_type') == 'App\Models\User' ? 'selected' : '' }}>{{ __('User') }}</option>
                        </x-forms.select>
                    </div>

                    <div class="min-w-36 flex-1">
                        <label class="block text-sm font-medium text-neutral-700 mb-1">{{ __('IP Address') }}</label>
                        <x-forms.input type="text" name="ip_address" :value="request('ip_address')" placeholder="127.0.0.1" />
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
                        <a href="{{ route('admin.login-activity.index') }}" class="btn btn-ghost py-3">
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
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('IP Address') }}</th>
                        <th>{{ __('Browser') }}</th>
                        <th>{{ __('Platform') }}</th>
                        <th>{{ __('Device') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td data-th="{{ __('User') }}">
                            @if($activity->user)
                                <div>
                                    <p class="text-sm font-medium text-neutral-900">{{ $activity->user->name }}</p>
                                    <p class="text-xs text-neutral-400">{{ $activity->user->email }}</p>
                                </div>
                            @else
                                <span class="text-sm text-neutral-400">
                                    {{ $activity->metadata['email'] ?? __('Unknown') }}
                                </span>
                            @endif
                        </td>
                        <td data-th="{{ __('Event') }}">
                            <div class="flex justify-end lg:justify-start rtl:justify-start">
                                <x-ui.badge :variant="$activity->getEventBadgeVariant()">
                                    {{ ucfirst($activity->event) }}
                                </x-ui.badge>
                            </div>
                        </td>
                        <td data-th="{{ __('IP Address') }}" class="text-sm text-neutral-600">
                            {{ $activity->ip_address ?? __('N/A') }}
                        </td>
                        <td data-th="{{ __('Browser') }}" class="text-sm text-neutral-600">
                            {{ $activity->browser ?? __('Unknown') }}
                        </td>
                        <td data-th="{{ __('Platform') }}" class="text-sm text-neutral-600">
                            {{ $activity->platform ?? __('Unknown') }}
                        </td>
                        <td data-th="{{ __('Device') }}" class="text-sm text-neutral-600">
                            {{ $activity->device ?? __('Unknown') }}
                        </td>
                        <td data-th="{{ __('Date') }}" class="text-sm text-neutral-400">
                            {{ format_date($activity->created_at, true) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-neutral-400">{{ __('No login activity found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </x-tables.table>

            <x-tables.pagination :paginator="$activities" />
            </div>
        </div>
    </div>
</x-layouts.admin>
