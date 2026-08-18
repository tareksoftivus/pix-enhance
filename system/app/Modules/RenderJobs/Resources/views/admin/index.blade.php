<x-layouts.admin :title="__('Render Jobs')">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Render Jobs') }}</h1>
                <p class="text-sm text-neutral-500">{{ __('Inspect user render activity, credit cost and processing status.') }}</p>
            </div>
        </div>

        <div class="section-card">
            <form method="GET" action="{{ route('admin.render-jobs.index') }}" class="grid gap-3 md:grid-cols-4">
                <label class="space-y-1">
                    <span class="text-xs font-medium text-neutral-600">{{ __('Search') }}</span>
                    <input class="form-input w-full" type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Job, file or user') }}">
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium text-neutral-600">{{ __('Status') }}</span>
                    <select class="form-select w-full" name="status">
                        <option value="">{{ __('All statuses') }}</option>
                        @foreach ($statuses as $status => $meta)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium text-neutral-600">{{ __('Tool') }}</span>
                    <select class="form-select w-full" name="tool">
                        <option value="">{{ __('All tools') }}</option>
                        @foreach ($tools as $tool => $definition)
                            <option value="{{ $tool }}" @selected(request('tool') === $tool)>{{ $definition['label'] ?? \Illuminate\Support\Str::headline($tool) }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="flex items-end gap-2">
                    <button class="btn btn-primary" type="submit">{{ __('Filter') }}</button>
                    <a class="btn btn-secondary" href="{{ route('admin.render-jobs.index') }}">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        <div class="section-card">
            <x-tables.table>
                <thead>
                    <tr>
                        <th>{{ __('Job') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Tool') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Credits') }}</th>
                        <th>{{ __('Created') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobs as $job)
                        @php
                            $status = $statuses[$job->status] ?? ['label' => \Illuminate\Support\Str::headline($job->status), 'badge' => 'badge-primary'];
                        @endphp
                        <tr>
                            <td>
                                <div class="font-medium text-neutral-950">{{ $job->source_name }}</div>
                                <div class="text-xs text-neutral-500">{{ $job->uuid }}</div>
                            </td>
                            <td>
                                <div class="font-medium text-neutral-900">{{ $job->user?->name ?? __('Unknown') }}</div>
                                <div class="text-xs text-neutral-500">{{ $job->user?->email }}</div>
                            </td>
                            <td>{{ $job->toolLabel() }}</td>
                            <td><span class="badge {{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                            <td>{{ number_format($job->credits_cost) }}</td>
                            <td>{{ $job->created_at?->format('M j, Y') }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if ($job->status === 'failed')
                                        <form method="POST" action="{{ route('admin.render-jobs.retry', $job) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary" type="submit">{{ __('Retry') }}</button>
                                        </form>
                                    @endif

                                    @if (! $job->isTerminal())
                                        <form method="POST" action="{{ route('admin.render-jobs.cancel', $job) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary" type="submit">{{ __('Cancel') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-neutral-500">{{ __('No render jobs found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-tables.table>

            <div class="mt-4">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>
