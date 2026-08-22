<x-layouts.admin :title="__('System Notifications')">
    <div class="space-y-6"
         x-data="{
            unreadCount: {{ (int) $unreadCount }},
            csrf: document.querySelector('meta[name=csrf-token]')?.content || '',
            markRead(url, row) {
                fetch(url, {
                    method: 'POST',
                    keepalive: true,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                }).then(() => {
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    row?.classList.remove('bg-primary/5');
                    row?.querySelector('[data-notification-unread]')?.remove();
                    document.dispatchEvent(new CustomEvent('system-notifications:unread-count', { detail: { count: this.unreadCount } }));
                });
            },
            markAllRead() {
                fetch('{{ route('admin.system-notifications.mark-all-read') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                }).then((response) => {
                    if (response.ok) {
                        this.unreadCount = 0;
                        document.querySelectorAll('[data-notification-row]').forEach((row) => row.classList.remove('bg-primary/5'));
                        document.querySelectorAll('[data-notification-unread]').forEach((dot) => dot.remove());
                        document.dispatchEvent(new CustomEvent('system-notifications:unread-count', { detail: { count: 0 } }));
                    }
                });
            },
         }">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('System Notifications') }}</h1>
            <div class="flex items-center gap-2">
                <button type="button"
                        class="btn btn-outline btn-sm"
                        x-show="unreadCount > 0"
                        x-cloak
                        @click="markAllRead()">
                    <i class="ph ph-checks"></i> {{ __('Mark all read') }}
                </button>
                @can('system-notifications.send')
                <x-ui.button variant="primary" href="{{ route('admin.notification-send.create') }}">
                    <i class="ph ph-megaphone"></i> {{ __('Send Notification') }}
                </x-ui.button>
                @endcan
            </div>
        </div>

        <div class="section-card">
            <div class="mb-4 flex items-center gap-2 border-b border-neutral-100 px-4 pt-4">
                <a href="{{ route('admin.system-notifications.index') }}"
                   class="border-b-2 px-3 pb-3 text-sm font-medium transition-colors {{ !request('status') ? 'border-primary text-primary' : 'border-transparent text-neutral-500 hover:text-neutral-700' }}">
                    {{ __('All') }}
                </a>
                <a href="{{ route('admin.system-notifications.index', ['status' => 'unread']) }}"
                   class="border-b-2 px-3 pb-3 text-sm font-medium transition-colors {{ request('status') === 'unread' ? 'border-primary text-primary' : 'border-transparent text-neutral-500 hover:text-neutral-700' }}">
                    {{ __('Unread') }}
                </a>
                <a href="{{ route('admin.system-notifications.index', ['status' => 'read']) }}"
                   class="border-b-2 px-3 pb-3 text-sm font-medium transition-colors {{ request('status') === 'read' ? 'border-primary text-primary' : 'border-transparent text-neutral-500 hover:text-neutral-700' }}">
                    {{ __('Read') }}
                </a>
            </div>

            <div class="divide-y divide-neutral-100">
                @forelse($notifications as $notification)
                <a @if($notification->getUrl()) href="{{ $notification->getUrl() }}" @endif
                   data-notification-row
                   @if(! $notification->isRead())
                       @click="markRead('{{ route('admin.system-notifications.mark-read', $notification) }}', $event.currentTarget)"
                   @endif
                   class="flex gap-4 p-4 transition-colors hover:bg-neutral-50 {{ !$notification->isRead() ? 'bg-primary/5' : '' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ match($notification->getType()) {
                        'success' => 'bg-success/10 text-success',
                        'warning' => 'bg-warning/10 text-warning',
                        'danger' => 'bg-error/10 text-error',
                        default => 'bg-primary/10 text-primary',
                    } }}">
                        <i class="ph {{ $notification->getIcon() }} text-xl"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium text-neutral-900 {{ !$notification->isRead() ? 'font-bold' : '' }}">
                                {{ $notification->getTitle() }}
                            </p>
                            <div class="flex shrink-0 items-center gap-2">
                                @if(!$notification->isRead())
                                <span class="h-2 w-2 rounded-full bg-primary" data-notification-unread></span>
                                @endif
                                <span class="text-xs text-neutral-400">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="mt-0.5 text-sm text-neutral-500">{{ $notification->getBody() }}</p>
                    </div>
                </a>
                @empty
                <div class="p-12 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-neutral-100">
                        <i class="ph ph-bell-slash text-2xl text-neutral-400"></i>
                    </div>
                    <p class="text-sm text-neutral-500">{{ __('No notifications found.') }}</p>
                </div>
                @endforelse
            </div>

            <x-tables.pagination :paginator="$notifications" />
        </div>
    </div>
</x-layouts.admin>
