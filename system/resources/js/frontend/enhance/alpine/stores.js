/**
 * Alpine stores — app-wide state
 *
 *   $store.toasts.push({ type: 'success', title: 'Saved' })
 *   $store.ui.sidebar
 */

'use strict';

let toastId = 0;

export function registerStores(Alpine) {
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
            return (
                {
                    success: 'circle-check',
                    error: 'circle-alert',
                    warning: 'triangle-alert',
                    info: 'info',
                }[type] || 'info'
            );
        },
    });

    Alpine.store('ui', {
        sidebar: true,
        commandOpen: false,

        toggleSidebar() {
            this.sidebar = !this.sidebar;
        },
    });
}

export default { registerStores };
