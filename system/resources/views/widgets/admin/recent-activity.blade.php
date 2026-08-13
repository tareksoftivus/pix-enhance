<div class="section-card">
    <h2 class="heading-5 text-neutral-950 mb-4">{{ __('Recent Activity') }}</h2>
    @if($recentActivity->isNotEmpty())
        <div class="space-y-3">
            @foreach($recentActivity as $activity)
            <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <i class="ph ph-clock-counter-clockwise"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-neutral-900 truncate">{{ $activity->action }}</p>
                    <p class="text-xs text-neutral-400">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-neutral-400">{{ __('No recent activity.') }}</p>
    @endif
</div>
