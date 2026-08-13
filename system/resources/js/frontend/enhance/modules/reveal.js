/**
 * Scroll reveal
 * Adds `.is-revealed` when an element enters the viewport. All motion is
 * declared in CSS (utilities/animations.css) — this module only flips a class.
 */

'use strict';

export function initReveal(selector = '[data-reveal]') {
    const targets = Array.from(document.querySelectorAll(selector));
    if (!targets.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduced || !('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('is-revealed');

                // Reveal once, then stop observing — keeps scrolling cheap.
                if (entry.target.dataset.revealRepeat === undefined) {
                    obs.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
    );

    targets.forEach((el) => observer.observe(el));
}

export default { initReveal };
