<!--
    Global toast outlet.
    Trigger from anywhere:  $store.toasts.success('Upscale complete', '4× · 8192 × 8192')
    Icons inside are picked up automatically by the icon observer in modules/icons.js.
-->
<div class="toast-region" role="region" aria-live="polite" aria-label="Notifications" x-data>
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div class="toast"
             :class="`toast-${toast.type}`"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <span class="toast__icon" aria-hidden="true">
                <i :data-lucide="$store.toasts.icon(toast.type)"></i>
            </span>

            <div class="toast__body">
                <p class="toast__title" x-text="toast.title"></p>
                <p class="toast__text" x-show="toast.text" x-text="toast.text"></p>
            </div>

            <button type="button" class="toast__close" @click="$store.toasts.dismiss(toast.id)" aria-label="Dismiss notification">
                <i data-lucide="x"></i>
            </button>

            <span class="toast__timer" x-show="toast.duration > 0"></span>
        </div>
    </template>
</div>
