<x-layouts.user :title="__('Dashboard')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Dashboard') }}</h1>
        </div>

        @php
            $registry = app(\App\Services\WidgetRegistry::class);
            $groups = [];
            $currentGrid = [];

            foreach ($widgets as $widget) {
                if ($widget->width() === 'full') {
                    if (!empty($currentGrid)) {
                        $groups[] = ['type' => 'grid', 'widgets' => $currentGrid];
                        $currentGrid = [];
                    }
                    $groups[] = ['type' => 'full', 'widget' => $widget];
                } else {
                    $currentGrid[] = $widget;
                }
            }

            if (!empty($currentGrid)) {
                $groups[] = ['type' => 'grid', 'widgets' => $currentGrid];
            }
        @endphp

        @foreach($groups as $group)
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
</x-layouts.user>
