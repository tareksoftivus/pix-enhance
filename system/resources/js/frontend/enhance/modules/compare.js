/**
 * Before / After comparison slider
 * ---------------------------------------------------------------------------
 * Progressive enhancement over a real <input type="range">, so the control is
 * keyboard operable and announced correctly by screen readers out of the box.
 * The visual split is written to the `--pos` custom property on the root.
 *
 * Markup contract:
 *   <div class="compare" data-compare>
 *     <div class="compare__frame">
 *       <img class="compare__layer" ...>                <!-- before -->
 *       <img class="compare__layer compare__layer-after" ...>
 *     </div>
 *     <input class="compare__range" type="range" data-compare-range>
 *     <div class="compare__handle"><span class="compare__grip">…</span></div>
 *   </div>
 *
 * Options via data attributes:
 *   data-compare-start="55"   initial split percentage
 *   data-compare-autoplay     sweeps once when scrolled into view
 * ---------------------------------------------------------------------------
 */

'use strict';

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

function createSlider(root) {
    const range = root.querySelector('[data-compare-range]');
    if (!range) return;

    const start = Number(root.dataset.compareStart || range.value || 50);
    let raf = null;

    const paint = (value) => {
        root.style.setProperty('--pos', `${value}%`);
        range.setAttribute('aria-valuetext', `${Math.round(value)}% of the enhanced image visible`);
    };

    const setValue = (value) => {
        const next = clamp(value, 0, 100);
        range.value = String(next);
        paint(next);
    };

    const positionFromEvent = (event) => {
        const rect = root.getBoundingClientRect();
        if (!rect.width) return Number(range.value);
        return ((event.clientX - rect.left) / rect.width) * 100;
    };

    const schedule = (value) => {
        if (raf) window.cancelAnimationFrame(raf);
        raf = window.requestAnimationFrame(() => setValue(value));
    };

    // --- Pointer drag (also handles touch + pen) ---------------------------
    const onPointerMove = (event) => schedule(positionFromEvent(event));

    const endDrag = () => {
        root.classList.remove('is-dragging');
        window.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('pointerup', endDrag);
        window.removeEventListener('pointercancel', endDrag);
    };

    root.addEventListener('pointerdown', (event) => {
        if (event.button !== 0 && event.pointerType === 'mouse') return;

        root.classList.add('is-dragging');
        setValue(positionFromEvent(event));

        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', endDrag);
        window.addEventListener('pointercancel', endDrag);
    });

    // --- Native input (keyboard, assistive tech) ---------------------------
    range.addEventListener('input', () => paint(Number(range.value)));

    // --- Optional intro sweep ---------------------------------------------
    if (root.dataset.compareAutoplay !== undefined && 'IntersectionObserver' in window) {
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!reduced) {
            const observer = new IntersectionObserver(
                (entries, obs) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        obs.disconnect();
                        sweep();
                    });
                },
                { threshold: 0.4 }
            );

            observer.observe(root);
        }
    }

    /** Wipes from "all original" to the resting split, so the gain is obvious. */
    function sweep() {
        const from = 6;
        const to = start;
        const duration = 1400;
        const begin = performance.now();

        setValue(from);

        const step = (now) => {
            const t = clamp((now - begin) / duration, 0, 1);
            // easeOutCubic
            const eased = 1 - Math.pow(1 - t, 3);
            setValue(from + (to - from) * eased);
            if (t < 1) window.requestAnimationFrame(step);
        };

        window.requestAnimationFrame(step);
    }

    setValue(start);
}

export function initCompare(selector = '[data-compare]') {
    document.querySelectorAll(selector).forEach(createSlider);
}

export default { initCompare };
