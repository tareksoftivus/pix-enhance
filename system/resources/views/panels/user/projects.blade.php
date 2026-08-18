<x-layouts.user :title="__('Projects')" :search-placeholder="__('Search projects')">
    @php
        $jobs = $projects['jobs'];
        $cards = $projects['cards'];
        $counts = collect($projects['counts']);
        $summary = $projects['summary'];
        $filters = $projects['filters'];
        $statusMeta = \App\Modules\RenderJobs\Models\RenderJob::statuses();
        $tools = config('render-jobs.tools', []);
        $formatBytes = function (int $bytes): string {
            if ($bytes < 1024) {
                return $bytes.' B';
            }

            $units = ['KB', 'MB', 'GB', 'TB'];
            $value = $bytes / 1024;

            foreach ($units as $unit) {
                if ($value < 1024) {
                    return number_format($value, $value >= 10 ? 0 : 1).' '.$unit;
                }

                $value /= 1024;
            }

            return number_format($value, 1).' PB';
        };
        $filterUrl = function (array $overrides = []) use ($filters) {
            $query = array_merge($filters, $overrides);
            $query = collect($query)
                ->reject(fn ($value) => $value === null || $value === '' || $value === 'all')
                ->all();

            return route('user.projects', $query);
        };
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Projects') }}</h1>
            <p class="dash__subtitle">
                {{ __('Every saved render from your Enhance workspace, ready to open or download.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.history', ['type' => 'render']) }}">
                <i data-lucide="clock"></i>
                {{ __('History') }}
            </a>
            <a class="btn btn-primary btn-sm" href="{{ route('user.dashboard') }}#studio" data-ripple>
                <i data-lucide="cloud-upload"></i>
                {{ __('New project') }}
            </a>
        </div>
    </div>

    <div class="dash-stats">
        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="folder"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($summary['total'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Total projects') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="circle-check"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($summary['completed'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Completed') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="refresh-cw"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($summary['in_progress'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('In progress') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="database"></i></span>
            <span>
                <span class="dash-stat__value">{{ $formatBytes((int) ($summary['storage_bytes'] ?? 0)) }}</span>
                <span class="dash-stat__label">{{ __('Storage used') }}</span>
            </span>
        </div>
    </div>

    <section class="panel" aria-labelledby="projects-title">
        <div class="panel__head">
            <div>
                <h2 class="panel__title" id="projects-title">
                    <i data-lucide="folder"></i>
                    {{ __('All projects') }}
                </h2>
                <p class="panel__subtitle">{{ __('Filter by status or tool, then open a project to compare and download it.') }}</p>
            </div>
        </div>

        <div class="panel__body">
            <form class="cluster cluster-between mb-lg" method="GET" action="{{ route('user.projects') }}">
                <div class="input-group">
                    <span class="input-group__icon" aria-hidden="true"><i data-lucide="search"></i></span>
                    <input class="input" type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('Search file, model or tool') }}">
                </div>

                <select class="select input-sm" name="tool">
                    <option value="all">{{ __('All tools') }}</option>
                    @foreach ($tools as $tool => $definition)
                        <option value="{{ $tool }}" @selected($filters['tool'] === $tool)>{{ $definition['label'] ?? \Illuminate\Support\Str::headline($tool) }}</option>
                    @endforeach
                </select>

                <input type="hidden" name="status" value="{{ $filters['status'] }}">

                <button class="btn btn-outline btn-sm" type="submit">
                    <i data-lucide="list-filter"></i>
                    {{ __('Filter') }}
                </button>
            </form>

            <div class="tabs tabs-underline">
                <div class="tabs__list" role="tablist" aria-label="{{ __('Filter projects by status') }}">
                    @foreach (array_merge(['all' => ['label' => __('All'), 'icon' => 'layout-grid']], $statusMeta) as $status => $tab)
                        <a class="tabs__tab {{ $filters['status'] === $status ? 'is-active' : '' }}"
                           href="{{ $filterUrl(['status' => $status, 'page' => null]) }}"
                           @if ($filters['status'] === $status) aria-current="page" @endif>
                            <i data-lucide="{{ $tab['icon'] }}"></i>
                            {{ $tab['label'] }}
                            @if ($counts->get($status, 0) > 0)
                                <span class="tabs__count">{{ $counts->get($status, 0) }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="tabs__panel">
                    @if ($cards->isEmpty())
                        <div class="empty-state">
                            <span class="empty-state__icon" aria-hidden="true"><i data-lucide="image-plus"></i></span>
                            <h3>{{ __('No projects found') }}</h3>
                            <p>{{ __('Create a new render or adjust the current filters.') }}</p>
                        </div>
                    @else
                        <div class="job-grid">
                            @foreach ($cards as $project)
                                @include('panels.user.partials.project-card', ['project' => $project, 'statusMeta' => $statusMeta])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($jobs->hasPages() || $jobs->total() > 0)
            <div class="panel__foot">
                <p class="pagination__meta">
                    {{ __('Showing :first-:last of :total projects', [
                        'first' => number_format($jobs->firstItem() ?? 0),
                        'last' => number_format($jobs->lastItem() ?? 0),
                        'total' => number_format($jobs->total()),
                    ]) }}
                </p>

                {{ $jobs->links() }}
            </div>
        @endif
    </section>
</x-layouts.user>
