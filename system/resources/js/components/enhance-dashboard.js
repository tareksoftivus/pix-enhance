import Alpine from 'alpinejs';
import { passwordField, tabs } from '../frontend/enhance/alpine/components.js';
import { renderIcons, observeIcons } from '../frontend/enhance/modules/icons.js';
import { initCompare } from '../frontend/enhance/modules/compare.js';
import { initProgress } from '../frontend/enhance/modules/progress.js';
import { initRange } from '../frontend/enhance/modules/range.js';
import { initRipple } from '../frontend/enhance/modules/ripple.js';

let toastId = 0;

Alpine.store('toasts', {
  items: [],

  push({ type = 'info', title = '', text = '', duration = 4500 } = {}) {
    const id = ++toastId;
    this.items.push({ id, type, title, text, duration });

    if (duration > 0) {
      window.setTimeout(() => this.dismiss(id), duration);
    }

    return id;
  },

  success(title, text) {
    return this.push({ type: 'success', title, text });
  },

  error(title, text) {
    return this.push({ type: 'error', title, text });
  },

  warning(title, text) {
    return this.push({ type: 'warning', title, text });
  },

  info(title, text) {
    return this.push({ type: 'info', title, text });
  },

  dismiss(id) {
    this.items = this.items.filter((item) => item.id !== id);
  },

  icon(type) {
    return {
      success: 'circle-check',
      error: 'circle-alert',
      warning: 'triangle-alert',
      info: 'info',
    }[type] || 'info';
  },
});

Alpine.data('dropdown', () => ({
  open: false,

  toggle() {
    this.open = !this.open;
  },

  close() {
    this.open = false;
  },

  trigger: {
    ['@click']() {
      this.toggle();
    },
    [':aria-expanded']() {
      return this.open;
    },
  },
}));

Alpine.data('tabs', tabs);
Alpine.data('passwordField', passwordField);

// The real enhanceStudio (server-wired: uploads to render-jobs.store and
// renders the actual RenderJob response) is registered in app.js from
// ./frontend/enhance/alpine/components.js — this file used to define its
// own client-only fake version, which has been removed in favour of that.

document.addEventListener('DOMContentLoaded', () => {
  document.documentElement.classList.remove('no-js');
  renderIcons();
  observeIcons();
  initCompare();
  initProgress();
  initRange();
  initRipple();
});

document.addEventListener('alpine:initialized', () => {
  renderIcons();
});
