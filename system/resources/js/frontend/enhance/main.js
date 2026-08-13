/**
 * PixEnhance — Application entry
 * ---------------------------------------------------------------------------
 * Single bootstrap for every page. Vanilla behaviour lives in ./modules,
 * declarative widgets are Alpine components in ./alpine. No global variables
 * are created, and every auto-executing function opens with 'use strict'
 * (ES modules are strict by default — the module-level directive below makes
 * that explicit and survives extraction into a classic script).
 * ---------------------------------------------------------------------------
 */

'use strict';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import '../../../css/frontend/enhance/main.css';

import { renderIcons, observeIcons } from './modules/icons.js';
import { initNavbar, initScrollSpy } from './modules/navbar.js';
import { initReveal } from './modules/reveal.js';
import { initCompare } from './modules/compare.js';
import { initSpotlight } from './modules/spotlight.js';
import { initRipple } from './modules/ripple.js';
import { initCounters } from './modules/counters.js';
import { initProgress } from './modules/progress.js';
import { initRange } from './modules/range.js';
import { registerComponents } from './alpine/components.js';
import { registerStores } from './alpine/stores.js';

/* --- Alpine ------------------------------------------------------------- */
Alpine.plugin(collapse);
Alpine.plugin(focus);
registerComponents(Alpine);
registerStores(Alpine);

// Exposed only because Alpine's own runtime requires it; nothing else reads it.
window.Alpine = Alpine;
Alpine.start();

/* --- Vanilla modules ---------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    document.documentElement.classList.remove('no-js');

    renderIcons();
    observeIcons();
    initNavbar();
    initScrollSpy();
    initReveal();
    initCompare();
    initSpotlight();
    initRipple();
    initCounters();
    initProgress();
    initRange();
});

// Re-render icons once Alpine has hydrated its initial templates.
document.addEventListener('alpine:initialized', function () {
    'use strict';

    renderIcons();
});
