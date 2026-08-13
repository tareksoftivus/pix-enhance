/**
 * Animated number counters
 *
 * <span data-counter="128400" data-counter-suffix="+" data-counter-decimals="0">0</span>
 * Counts up once the element scrolls into view.
 */

'use strict';

const EASE = (t) => 1 - Math.pow(1 - t, 3);

function format(value, decimals, compact) {
    if (compact) {
        const units = [
            { limit: 1e9, suffix: 'B' },
            { limit: 1e6, suffix: 'M' },
            { limit: 1e3, suffix: 'K' },
        ];

        for (const unit of units) {
            if (value >= unit.limit) {
                return (value / unit.limit).toFixed(decimals || 1).replace(/\.0$/, '') + unit.suffix;
            }
        }
    }

    return value.toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

function run(el) {
    const target = Number(el.dataset.counter || 0);
    const decimals = Number(el.dataset.counterDecimals || 0);
    const duration = Number(el.dataset.counterDuration || 1800);
    const prefix = el.dataset.counterPrefix || '';
    const suffix = el.dataset.counterSuffix || '';
    const compact = el.dataset.counterCompact !== undefined;
    const begin = performance.now();

    const step = (now) => {
        const t = Math.min((now - begin) / duration, 1);
        const value = target * EASE(t);

        el.textContent = `${prefix}${format(value, decimals, compact)}${suffix}`;

        if (t < 1) window.requestAnimationFrame(step);
    };

    window.requestAnimationFrame(step);
}

export function initCounters(selector = '[data-counter]') {
    const els = Array.from(document.querySelectorAll(selector));
    if (!els.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduced || !('IntersectionObserver' in window)) {
        els.forEach((el) => {
            const decimals = Number(el.dataset.counterDecimals || 0);
            const compact = el.dataset.counterCompact !== undefined;
            el.textContent = `${el.dataset.counterPrefix || ''}${format(
                Number(el.dataset.counter || 0),
                decimals,
                compact
            )}${el.dataset.counterSuffix || ''}`;
        });
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                obs.unobserve(entry.target);
                run(entry.target);
            });
        },
        { threshold: 0.5 }
    );

    els.forEach((el) => observer.observe(el));
}

export default { initCounters };
