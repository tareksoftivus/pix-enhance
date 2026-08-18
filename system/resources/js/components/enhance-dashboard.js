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

Alpine.data('enhanceStudio', (options = {}) => ({
  source: {
    name: options.name || '',
    meta: options.meta || '',
    url: '',
  },
  result: { url: '' },
  owned: false,
  model: options.model || 'enhance-xl',
  scale: options.scale || '4',
  detail: 72,
  fidelity: 65,
  edge: 60,
  backdrop: 'transparent',
  face: true,
  denoise: true,
  colour: true,
  hair: true,
  shadow: false,
  format: options.format || 'png',
  status: options.demo ? 'done' : 'empty',
  progress: 0,
  stageIndex: 0,
  dragging: false,
  error: '',
  timer: null,
  stages: options.stages || [
    'Analysing the source',
    'Removing compression artefacts',
    'Reconstructing detail',
    'Restoring faces',
    'Writing the output',
  ],

  get stage() {
    return this.stages[this.stageIndex];
  },

  get cost() {
    if (options.fixedCost) return options.fixedCost;
    return { 1: 1, 2: 1, 4: 2, 8: 4, 16: 8 }[this.scale] || 1;
  },

  get outputSize() {
    const [w, h] = options.baseSize || [960, 720];
    const factor = options.noScale ? 1 : Number(this.scale);
    return `${w * factor} × ${h * factor}`;
  },

  get busy() {
    return this.status === 'running';
  },

  onDragOver() {
    this.dragging = true;
  },

  onDragLeave(event) {
    if (event?.currentTarget?.contains(event.relatedTarget)) return;
    this.dragging = false;
  },

  onDrop(event) {
    this.dragging = false;
    this.accept(event.dataTransfer?.files?.[0]);
  },

  onChange(event) {
    this.accept(event.target.files?.[0]);
    event.target.value = '';
  },

  accept(file) {
    if (!file) return;

    if (!file.type.startsWith('image/')) {
      this.error = `${file.name} is not an image.`;
      return;
    }

    if (file.size > 50 * 1024 * 1024) {
      this.error = `${file.name} is over the 50 MB limit.`;
      return;
    }

    this.error = '';
    if (this.source.url.startsWith('blob:')) URL.revokeObjectURL(this.source.url);

    this.source = {
      name: file.name,
      meta: `${(file.size / (1024 * 1024)).toFixed(1)} MB`,
      url: URL.createObjectURL(file),
    };

    this.owned = true;
    this.result = { url: '' };
    this.status = 'ready';
    this.progress = 0;
  },

  run() {
    if (this.status === 'empty' || this.busy) return;

    if (!this.source.url && this.$refs.sourceLayer) {
      this.source.url = this.$refs.sourceLayer.src;
    }

    this.status = 'running';
    this.progress = 0;
    this.stageIndex = 0;

    const tick = () => {
      this.progress = Math.min(this.progress + Math.random() * 9 + 4, 100);
      this.stageIndex = Math.min(
        Math.floor((this.progress / 100) * this.stages.length),
        this.stages.length - 1,
      );

      if (this.progress >= 100) {
        this.finish();
        return;
      }

      this.timer = window.setTimeout(tick, 260);
    };

    this.timer = window.setTimeout(tick, 300);
  },

  finish() {
    if (this.owned) this.result = { url: this.source.url };
    this.status = 'done';
    this.$store.toasts.success(
      'Enhancement complete',
      `${this.source.name || 'beach-cove.jpg'} is ready at ${this.outputSize}.`,
    );
    this.$nextTick(() => initCompare());
  },

  cancel() {
    window.clearTimeout(this.timer);
    this.status = this.result.url ? 'done' : 'ready';
    this.progress = 0;
  },

  reset() {
    window.clearTimeout(this.timer);
    if (this.source.url.startsWith('blob:')) URL.revokeObjectURL(this.source.url);

    this.source = { name: '', meta: '', url: '' };
    this.result = { url: '' };
    this.owned = false;
    this.status = 'empty';
    this.progress = 0;
    this.error = '';
  },
}));

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
