/**
 * Progress bars & rings
 * Reads the target value from data attributes and animates it in when the
 * element scrolls into view, by writing the CSS custom properties the
 * stylesheet already knows how to draw.
 *
 *   <div class="progress" data-progress="72"><span class="progress__bar"></span></div>
 *   <div class="progress-ring" data-progress-ring="86" data-ring-radius="45">…</div>
 */

'use strict';

function paintBar(el) {
    const value = Math.min(Math.max(Number(el.dataset.progress || 0), 0), 100);
    const bar = el.querySelector('.progress__bar');
    if (bar) bar.style.setProperty('--progress', `${value}%`);
}

function paintRing(el) {
    const value = Math.min(Math.max(Number(el.dataset.progressRing || 0), 0), 100);
    const radius = Number(el.dataset.ringRadius || 45);
    const circumference = 2 * Math.PI * radius;

    el.style.setProperty('--ring-circumference', String(circumference));
    el.style.setProperty('--ring-offset', String(circumference * (1 - value / 100)));

    const output = el.querySelector('[data-ring-value]');
    if (output) output.textContent = String(Math.round(value));
}

export function initProgress() {
    const bars = Array.from(document.querySelectorAll('[data-progress]'));
    const rings = Array.from(document.querySelectorAll('[data-progress-ring]'));
    const all = [...bars, ...rings];

    if (!all.length) return;

    const paint = (el) => (el.dataset.progressRing !== undefined ? paintRing(el) : paintBar(el));

    if (!('IntersectionObserver' in window)) {
        all.forEach(paint);
        return;
    }

    // Rings need their dasharray set before the reveal or the first frame jumps.
    rings.forEach((el) => {
        const radius = Number(el.dataset.ringRadius || 45);
        const circumference = 2 * Math.PI * radius;
        el.style.setProperty('--ring-circumference', String(circumference));
        el.style.setProperty('--ring-offset', String(circumference));
    });

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                obs.unobserve(entry.target);
                paint(entry.target);
            });
        },
        { threshold: 0.35 }
    );

    all.forEach((el) => {
        /**
         * An element inside a closed tab panel or collapsed section has no box,
         * so it can never satisfy the observer's threshold and would stay stuck
         * at zero even after it is revealed. There is no scroll reveal worth
         * animating for something that is not laid out yet — paint it now and
         * let it simply be correct when it appears.
         */
        if (!el.getBoundingClientRect().height) {
            paint(el);
            return;
        }

        observer.observe(el);
    });
}

export default { initProgress };
