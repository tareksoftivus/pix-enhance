/**
 * Card hover glow
 * Tracks the pointer inside `.card-hover-glow` elements and publishes its
 * position as `--mx` / `--my` custom properties. The glow itself is pure CSS.
 */

'use strict';

export function initSpotlight(selector = '.card-hover-glow') {
    const cards = Array.from(document.querySelectorAll(selector));
    if (!cards.length) return;

    if (window.matchMedia('(hover: none)').matches) return;

    cards.forEach((card) => {
        let raf = null;

        card.addEventListener('pointermove', (event) => {
            if (raf) return;

            raf = window.requestAnimationFrame(() => {
                const rect = card.getBoundingClientRect();
                card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
                card.style.setProperty('--my', `${event.clientY - rect.top}px`);
                raf = null;
            });
        });

        card.addEventListener('pointerleave', () => {
            card.style.removeProperty('--mx');
            card.style.removeProperty('--my');
        });
    });
}

export default { initSpotlight };
