@php
    $progress = max(0, min(100, (int) $metrics['completion_rate']));

    // Segments feeding the donut. Zero-count segments are dropped so the chart
    // never renders an empty slice.
    $segments = collect([
        ['label' => __('Completed'), 'value' => (int) $metrics['completed_payments'], 'color' => '#22c55e'],
        ['label' => __('Pending'), 'value' => (int) $metrics['pending_payments'], 'color' => '#f59e0b'],
        ['label' => __('Failed'), 'value' => (int) $metrics['failed_payments'], 'color' => '#ef4444'],
        ['label' => __('Other'), 'value' => (int) $metrics['other_payments'], 'color' => '#8e99a4'],
    ])->filter(fn ($s) => $s['value'] > 0)->values();

    $chartData = [
        'series' => $segments->pluck('value')->all(),
        'labels' => $segments->pluck('label')->all(),
        'colors' => $segments->pluck('color')->all(),
        'centerLabel' => __('Paid'),
        'centerValue' => $progress.'%',
    ];
@endphp

<div class="section-card">
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="heading-5 text-neutral-950">{{ __('Payment Health') }}</h2>
        </div>
        <span class="rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">{{ __('Live') }}</span>
    </div>

    <div class="flex flex-col items-center">
        @if($metrics['total_payments'] === 0)
            <div class="flex h-44 w-full items-center justify-center">
                <p class="text-center text-sm text-neutral-500">{{ __('No payment records yet') }}</p>
            </div>
        @else
            {{-- Donut with a custom, absolutely-centered label to avoid ApexCharts' overlapping center text. --}}
            <div class="relative w-full">
                <div
                    class="payment-health-chart w-full"
                    data-payment-health='@json($chartData)'
                ></div>
                <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                    <span class="text-3xl font-bold tracking-tight text-neutral-950">{{ $chartData['centerValue'] }}</span>
                    <span class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-400">{{ $chartData['centerLabel'] }}</span>
                </div>
            </div>

            {{-- Legend --}}
            <div class="mt-5 flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                @foreach($segments as $segment)
                    <span class="flex items-center gap-2 text-xs text-neutral-500">
                        <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $segment['color'] }}"></span>
                        <span>{{ $segment['label'] }}</span>
                        <span class="font-semibold text-neutral-700">{{ number_format($segment['value']) }}</span>
                    </span>
                @endforeach
            </div>
        @endif

        <div class="mt-5 grid w-full grid-cols-2 gap-3">
            <div class="rounded-xl border border-neutral-100 bg-neutral-0/70 p-3">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-neutral-400">{{ __('Completed') }}</p>
                <p class="mt-1 text-lg font-bold text-neutral-950">{{ number_format($metrics['completed_payments']) }}/{{ number_format($metrics['total_payments']) }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 bg-neutral-0/70 p-3">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-neutral-400">{{ __('Pending') }}</p>
                <p class="mt-1 text-lg font-bold text-neutral-950">{{ number_format($metrics['pending_payments']) }}</p>
            </div>
        </div>
    </div>
</div>

{{--
    Inline (not @push): widget HTML is cached and echoed directly into the page,
    so the initializer must travel with the markup rather than a layout stack.
    ApexCharts is exposed globally as window.ApexCharts by resources/js/components/charts.js.
--}}
<script>
    (function () {
        'use strict';

        // Reuse the initializer across repeated widget renders / AJAX reloads.
        if (window.__paymentHealthInit) {
            window.__paymentHealthInit();
            return;
        }

        function renderPaymentHealth(el) {
            if (typeof window.ApexCharts === 'undefined' || el.dataset.rendered) {
                return;
            }
            el.dataset.rendered = '1';

            var cfg = JSON.parse(el.dataset.paymentHealth);
            var isDark = document.documentElement.classList.contains('dark');
            var cardColor = isDark ? '#101214' : '#ffffff';

            var chart = new window.ApexCharts(el, {
                chart: {
                    type: 'donut',
                    height: 220,
                    fontFamily: 'Inter, sans-serif',
                    background: 'transparent',
                    animations: { easing: 'easeinout', speed: 600 },
                },
                series: cfg.series,
                labels: cfg.labels,
                colors: cfg.colors,
                stroke: { width: 3, colors: [cardColor] },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '76%',
                            // Center text is rendered as an HTML overlay in the
                            // Blade markup, so ApexCharts' own labels stay off.
                            labels: { show: false },
                        },
                    },
                },
                dataLabels: { enabled: false },
                legend: { show: false },
                tooltip: {
                    enabled: true,
                    fillSeriesColor: false,
                    y: {
                        formatter: function (val) { return val + ' payments'; },
                        title: { formatter: function (name) { return name + ':'; } },
                    },
                },
            });

            chart.render();
        }

        function initPaymentHealth() {
            document.querySelectorAll('.payment-health-chart').forEach(renderPaymentHealth);
        }

        window.__paymentHealthInit = initPaymentHealth;

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPaymentHealth);
        } else {
            initPaymentHealth();
        }
    })();
</script>
