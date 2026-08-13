<x-layouts.admin :title="__('Dashboard')">
    <div class="space-y-6">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Dashboard') }}</h1>
                <p class="mt-1 text-sm text-neutral-500">{{ __('Track revenue, payments, refunds, and operational activity from one focused view.') }}</p>
            </div>
        </div>

        @php
            $registry = app(\App\Services\WidgetRegistry::class);
            $widgetsById = $widgets->keyBy(fn ($widget) => $widget->id());
            $overviewWidget = $widgetsById->get('admin-stats');
            $trendWidget = $widgetsById->get('admin-sales-overview');
            $healthWidget = $widgetsById->get('admin-health');
            $activityHubWidget = $widgetsById->get('admin-activity-hub');
            $supportSnapshotWidget = $widgetsById->get('admin-support-snapshot');
            $remainingWidgets = $widgets->reject(fn ($widget) => in_array($widget->id(), [
                'admin-stats',
                'admin-sales-overview',
                'admin-health',
                'admin-user-distribution',
                'admin-activity-hub',
                'admin-support-snapshot',
                'admin-recent-activity',
            ], true));
        @endphp

        @if($overviewWidget)
            {!! $registry->renderWidget($overviewWidget) !!}
        @endif

        @if($trendWidget || $healthWidget)
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                @if($trendWidget)
                    <div class="xl:col-span-2">
                        {!! $registry->renderWidget($trendWidget) !!}
                    </div>
                @endif

                @if($healthWidget)
                    {!! $registry->renderWidget($healthWidget) !!}
                @endif
            </div>
        @endif

        @if($activityHubWidget || $supportSnapshotWidget)
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @if($activityHubWidget)
                    {!! $registry->renderWidget($activityHubWidget) !!}
                @endif

                @if($supportSnapshotWidget)
                    {!! $registry->renderWidget($supportSnapshotWidget) !!}
                @endif
            </div>
        @endif

        @php
            $remainingGroups = [];
            $currentGrid = [];

            foreach ($remainingWidgets as $widget) {
                if ($widget->width() === 'full') {
                    if (!empty($currentGrid)) {
                        $remainingGroups[] = ['type' => 'grid', 'widgets' => $currentGrid];
                        $currentGrid = [];
                    }
                    $remainingGroups[] = ['type' => 'full', 'widget' => $widget];
                    continue;
                }

                $currentGrid[] = $widget;
            }

            if (!empty($currentGrid)) {
                $remainingGroups[] = ['type' => 'grid', 'widgets' => $currentGrid];
            }
        @endphp

        @foreach($remainingGroups as $group)
            @if($group['type'] === 'full')
                {!! $registry->renderWidget($group['widget']) !!}
            @else
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    @foreach($group['widgets'] as $widget)
                        <div>
                            {!! $registry->renderWidget($widget) !!}
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</x-layouts.admin>
