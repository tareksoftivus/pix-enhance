/**
 * Button ripple
 * Delegated at the document level so buttons rendered later (Alpine, AJAX)
 * pick the effect up automatically. Opt in with `data-ripple` on the button.
 */

'use strict';

const RIPPLE_LIFETIME = 640;

export function initRipple() {
    document.addEventListener('pointerdown', (event) => {
        const target = event.target.closest('[data-ripple]');
        if (!target || target.disabled) return;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const rect = target.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const ripple = document.createElement('span');

        ripple.className = 'btn-ripple';
        ripple.style.width = `${size}px`;
        ripple.style.height = `${size}px`;
        ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
        ripple.style.top = `${event.clientY - rect.top - size / 2}px`;

        target.appendChild(ripple);
        window.setTimeout(() => ripple.remove(), RIPPLE_LIFETIME);
    });
}

export default { initRipple };
