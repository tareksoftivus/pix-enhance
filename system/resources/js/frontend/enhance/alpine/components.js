/**
 * Alpine.js components
 * ---------------------------------------------------------------------------
 * Every interactive widget in the template is registered here as a named
 * Alpine data component, so markup stays declarative and portable:
 *
 *   <div x-data="accordion(0)"> … </div>
 *
 * Nothing leaks to `window` — Alpine owns the registry.
 * ---------------------------------------------------------------------------
 */

'use strict';

/**
 * Accordion / FAQ. Pass the index that should start open, or -1 for none.
 * `multiple: true` lets several panels stay open at once.
 */
export function accordion(openIndex = 0, options = {}) {
    return {
        open: options.multiple ? [openIndex].filter((i) => i >= 0) : openIndex,
        multiple: Boolean(options.multiple),

        isOpen(index) {
            return this.multiple ? this.open.includes(index) : this.open === index;
        },

        toggle(index) {
            if (this.multiple) {
                this.open = this.isOpen(index)
                    ? this.open.filter((i) => i !== index)
                    : [...this.open, index];
                return;
            }

            this.open = this.open === index ? -1 : index;
        },
    };
}

/**
 * Tabs. `initial` is the key of the tab shown first.
 */
export function tabs(initial = '') {
    return {
        active: initial,

        select(key) {
            this.active = key;
        },

        isActive(key) {
            return this.active === key;
        },

        /** Roving focus for arrow-key navigation across the tablist. */
        onKeydown(event) {
            const keys = ['ArrowRight', 'ArrowLeft', 'Home', 'End'];
            if (!keys.includes(event.key)) return;

            event.preventDefault();

            const items = Array.from(this.$el.querySelectorAll('[role="tab"]'));
            const index = items.indexOf(document.activeElement);
            let next = index;

            if (event.key === 'ArrowRight') next = (index + 1) % items.length;
            if (event.key === 'ArrowLeft') next = (index - 1 + items.length) % items.length;
            if (event.key === 'Home') next = 0;
            if (event.key === 'End') next = items.length - 1;

            items[next]?.focus();
            items[next]?.click();
        },
    };
}

/**
 * Dropdown / popover with outside-click and Escape handling.
 */
export function dropdown() {
    return {
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
    };
}

/**
 * Modal / dialog. Locks body scroll and restores focus to the opener.
 */
export function modal(startOpen = false) {
    return {
        open: startOpen,
        opener: null,

        show(event) {
            this.opener = event?.currentTarget || document.activeElement;
            this.open = true;
            document.body.classList.add('is-locked');

            this.$nextTick(() => {
                this.$refs.dialog?.querySelector('[data-autofocus]')?.focus();
            });
        },

        hide() {
            this.open = false;
            document.body.classList.remove('is-locked');
            this.opener?.focus();
        },
    };
}

/**
 * Mobile navigation drawer.
 */
export function mobileNav() {
    return {
        open: false,
        submenu: null,

        toggle() {
            this.open ? this.close() : this.show();
        },

        show() {
            this.open = true;
            document.body.classList.add('is-locked');
        },

        close() {
            this.open = false;
            this.submenu = null;
            document.body.classList.remove('is-locked');
        },

        toggleSubmenu(key) {
            this.submenu = this.submenu === key ? null : key;
        },
    };
}

/**
 * Pricing billing-period switch.
 * Prices are declared in markup as `data-monthly` / `data-yearly`.
 */
export function pricingToggle(initial = 'monthly') {
    return {
        period: initial,

        get yearly() {
            return this.period === 'yearly';
        },

        setPeriod(period) {
            this.period = period;
        },

        toggle() {
            this.period = this.yearly ? 'monthly' : 'yearly';
        },

        price(monthly, yearly) {
            return this.yearly ? yearly : monthly;
        },
    };
}

/**
 * Drag & drop upload zone with client-side preview.
 * Purely front-end — wire `submit()` to your Laravel endpoint later.
 */
export function dropzone(options = {}) {
    return {
        dragging: false,
        files: [],
        accept: options.accept || ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
        maxSize: options.maxSize || 25 * 1024 * 1024,
        multiple: options.multiple !== false,
        error: '',

        onDragOver() {
            this.dragging = true;
        },

        onDragLeave() {
            this.dragging = false;
        },

        onDrop(event) {
            this.dragging = false;
            this.add(event.dataTransfer?.files);
        },

        onChange(event) {
            this.add(event.target.files);
            event.target.value = '';
        },

        add(fileList) {
            if (!fileList) return;

            this.error = '';
            const incoming = Array.from(fileList);

            for (const file of incoming) {
                if (!this.accept.includes(file.type)) {
                    this.error = `${file.name} is not a supported image format.`;
                    continue;
                }

                if (file.size > this.maxSize) {
                    this.error = `${file.name} exceeds the ${this.humanSize(this.maxSize)} limit.`;
                    continue;
                }

                const entry = {
                    id: `${file.name}-${file.size}-${this.files.length}`,
                    name: file.name,
                    size: this.humanSize(file.size),
                    preview: URL.createObjectURL(file),
                    progress: 0,
                    status: 'queued',
                };

                if (this.multiple) {
                    this.files.push(entry);
                } else {
                    this.clear();
                    this.files = [entry];
                }

                this.simulate(entry);
            }
        },

        /** Demo-only progress simulation; replace with a real XHR/fetch upload. */
        simulate(entry) {
            entry.status = 'uploading';

            const tick = () => {
                entry.progress = Math.min(entry.progress + Math.random() * 18 + 6, 100);

                if (entry.progress >= 100) {
                    entry.status = 'done';
                    this.$dispatch('upload-complete', { id: entry.id });
                    return;
                }

                window.setTimeout(tick, 220);
            };

            window.setTimeout(tick, 260);
        },

        remove(id) {
            const entry = this.files.find((file) => file.id === id);
            if (entry) URL.revokeObjectURL(entry.preview);
            this.files = this.files.filter((file) => file.id !== id);
        },

        clear() {
            this.files.forEach((file) => URL.revokeObjectURL(file.preview));
            this.files = [];
        },

        humanSize(bytes) {
            if (bytes < 1024) return `${bytes} B`;
            if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`;
            return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
        },
    };
}

/**
 * Copy-to-clipboard helper for API keys, code blocks, share links.
 */
export function copyable(value = '') {
    return {
        copied: false,
        value,

        async copy() {
            try {
                await navigator.clipboard.writeText(this.value || this.$refs.source?.textContent || '');
                this.copied = true;
                window.setTimeout(() => {
                    this.copied = false;
                }, 1800);
            } catch {
                this.copied = false;
            }
        },
    };
}

/**
 * Password field — visibility toggle plus an optional strength read-out.
 *
 * The score is exposed as a number and written to a `data-score` attribute in
 * markup, so every colour in the meter stays in the stylesheet rather than
 * being assembled here.
 */
export function passwordField() {
    return {
        value: '',
        visible: false,

        toggle() {
            this.visible = !this.visible;
        },

        get rules() {
            return {
                length: this.value.length >= 8,
                case: /[a-z]/.test(this.value) && /[A-Z]/.test(this.value),
                digit: /\d/.test(this.value),
                symbol: /[^A-Za-z0-9]/.test(this.value),
            };
        },

        /** 0 (empty) through 4 (strong). */
        get score() {
            if (!this.value) return 0;
            return Object.values(this.rules).filter(Boolean).length;
        },

        get label() {
            return ['', 'Weak', 'Fair', 'Good', 'Strong'][this.score];
        },
    };
}

/**
 * One-time code input.
 *
 * Each digit is its own <input> so password managers and mobile keyboards
 * behave, with focus advancing automatically. Pasting a full code into any
 * box fills the whole row rather than dropping six characters into one.
 */
export function otpInput(length = 6) {
    return {
        length,
        digits: Array.from({ length }, () => ''),

        get code() {
            return this.digits.join('');
        },

        get complete() {
            return this.code.length === this.length;
        },

        boxes() {
            return Array.from(this.$refs.row.querySelectorAll('input'));
        },

        focusAt(index) {
            const box = this.boxes()[index];
            if (!box) return;
            box.focus();
            box.select();
        },

        onInput(index, event) {
            const digit = event.target.value.replace(/\D/g, '').slice(-1);
            this.digits[index] = digit;
            event.target.value = digit;

            if (digit && index < this.length - 1) this.focusAt(index + 1);
        },

        onKeydown(index, event) {
            if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                event.preventDefault();
                this.digits[index - 1] = '';

                // The boxes are not bound with x-model — their value is managed
                // here so a paste can rewrite the whole row — so clearing the
                // state has to clear the element too.
                const previous = this.boxes()[index - 1];
                if (previous) previous.value = '';

                this.focusAt(index - 1);
                return;
            }

            if (event.key === 'ArrowLeft' && index > 0) {
                event.preventDefault();
                this.focusAt(index - 1);
                return;
            }

            if (event.key === 'ArrowRight' && index < this.length - 1) {
                event.preventDefault();
                this.focusAt(index + 1);
            }
        },

        onPaste(event) {
            event.preventDefault();

            const pasted = (event.clipboardData?.getData('text') || '')
                .replace(/\D/g, '')
                .slice(0, this.length);

            if (!pasted) return;

            pasted.split('').forEach((digit, i) => {
                this.digits[i] = digit;
            });

            this.$nextTick(() => {
                this.boxes().forEach((box, i) => {
                    box.value = this.digits[i] || '';
                });
                this.focusAt(Math.min(pasted.length, this.length - 1));
            });
        },
    };
}

/**
 * Enhancement studio — the working version of the landing page's hero mock.
 *
 * Front-end only. `run()` walks a simulated pipeline so the states a real
 * backend produces (queued → running → done) are all reachable and styleable;
 * swap `run()` for your upload + poll and set `result.url` from the response.
 *
 * The page opens on a finished demo job so the workspace reads as a product
 * rather than an empty form. `reset()` returns it to the drop state.
 */
export function enhanceStudio(options = {}) {
    return {
        /**
         * Image URLs are deliberately NOT passed in here. The demo images are
         * plain <img src> in the markup so the build rewrites them to their
         * hashed filenames; a path hard-coded in JS would survive the build
         * unrewritten and 404. The bindings fall back to each element's own
         * resolved `src`, and `run()` reads it back when it needs one.
         */
        source: {
            name: options.name || '',
            meta: options.meta || '',
            url: '',
        },
        result: { url: '' },

        /** True once the visitor has supplied their own file. */
        owned: false,

        /**
         * Settings. Each tool page binds the subset it needs — the upscaler
         * uses `scale`, face restoration uses `fidelity`, background removal
         * uses `backdrop` — and ignores the rest.
         */
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

        /* --- Pipeline ---------------------------------------------------- */
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

        /**
         * Credits scale with the output area, so each step up doubles. Tools
         * that do not resize (background removal) charge a flat rate instead.
         */
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

        /* --- File intake -------------------------------------------------- */
        onDragOver() {
            this.dragging = true;
        },

        /**
         * `dragleave` also fires when the pointer crosses into a child element,
         * which would flicker the overlay off while the file is still over the
         * canvas. Only clear when the pointer has genuinely left.
         */
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

        /* --- Pipeline ----------------------------------------------------- */
        run() {
            if (this.status === 'empty' || this.busy) return;

            // The demo document has no URL in state — its image lives in the
            // markup. Read it back so the running preview has something to show.
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
            // A real integration assigns the URL the API returns here. With no
            // backend, an uploaded image doubles as its own result so the
            // slider still has two layers. The demo pair is left alone — its
            // "after" image is already in the markup.
            if (this.owned) this.result = { url: this.source.url };
            this.status = 'done';

            this.$store.toasts.success(
                'Enhancement complete',
                `${this.source.name} is ready at ${this.outputSize}.`,
            );
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
    };
}

/**
 * Registers every component with an Alpine instance.
 */
export function registerComponents(Alpine) {
    Alpine.data('accordion', accordion);
    Alpine.data('tabs', tabs);
    Alpine.data('dropdown', dropdown);
    Alpine.data('modal', modal);
    Alpine.data('mobileNav', mobileNav);
    Alpine.data('pricingToggle', pricingToggle);
    Alpine.data('dropzone', dropzone);
    Alpine.data('copyable', copyable);
    Alpine.data('passwordField', passwordField);
    Alpine.data('otpInput', otpInput);
    Alpine.data('enhanceStudio', enhanceStudio);
}

export default { registerComponents };
