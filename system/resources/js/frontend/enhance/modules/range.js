/**
 * Range inputs
 * ---------------------------------------------------------------------------
 * A native range input paints its track in one flat colour, so `.range` draws
 * the filled portion itself with a background gradient sized by the
 * `--range-fill` custom property. Nothing sets that property on its own, which
 * leaves the fill parked at its 50% default while the thumb sits at the real
 * value — this module keeps the two in sync, on load and while dragging.
 *
 * Works for decorative sliders too (e.g. inside an `inert` product mock):
 * reading `value` and writing a custom property needs no interaction.
 * ---------------------------------------------------------------------------
 */

'use strict';

function paint(el) {
    const min = Number(el.min === '' ? 0 : el.min);
    const max = Number(el.max === '' ? 100 : el.max);
    const span = max - min;
    const ratio = span === 0 ? 0 : (Number(el.value) - min) / span;

    el.style.setProperty('--range-fill', `${Math.min(Math.max(ratio, 0), 1) * 100}%`);
}

export function initRange(selector = '.range') {
    const inputs = Array.from(document.querySelectorAll(selector));
    if (!inputs.length) return;

    inputs.forEach((el) => {
        paint(el);
        el.addEventListener('input', () => paint(el));
    });
}

export default { initRange };
