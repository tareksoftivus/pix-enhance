@props([
    'ticket',
    'endpoint',
    'firstPage',
])

{{--
    Conversation with "load older on scroll". The first page (newest messages)
    is rendered server-side; an IntersectionObserver on the sentinel fetches the
    next page from :endpoint and appends it, until the server reports no more.
--}}
<div
    x-data="supportThread({
        url: @js($endpoint),
        nextPage: {{ $firstPage['next_page'] }},
        hasMore: {{ $firstPage['has_more'] ? 'true' : 'false' }},
    })"
    x-init="init()"
>
    <div x-ref="list">
        <x-support::message-list :messages="$firstPage['messages']" />
    </div>

    {{-- Sentinel / status row --}}
    <div x-ref="sentinel" x-show="hasMore" class="flex items-center justify-center py-4">
        <span x-show="loading" class="flex items-center gap-2 text-sm text-neutral-400">
            <i class="ph ph-circle-notch animate-spin"></i> {{ __('Loading earlier messages…') }}
        </span>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function supportThread(config) {
                'use strict';

                return {
                    url: config.url,
                    nextPage: config.nextPage,
                    hasMore: config.hasMore,
                    loading: false,

                    // Minimum time the spinner stays visible before messages
                    // appear, so the loading state is always perceptible.
                    minSpinnerMs: 1000,

                    init() {
                        if (!this.hasMore) {
                            return;
                        }

                        // No rootMargin: only fire once the sentinel itself is
                        // actually reached at the bottom of the thread.
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting) {
                                this.loadMore();
                            }
                        });

                        observer.observe(this.$refs.sentinel);
                    },

                    loadMore() {
                        if (this.loading || !this.hasMore) {
                            return;
                        }

                        this.loading = true;
                        const separator = this.url.includes('?') ? '&' : '?';

                        // Hold the spinner for at least minSpinnerMs, even if the
                        // response returns sooner.
                        const delay = new Promise((resolve) => setTimeout(resolve, this.minSpinnerMs));
                        const request = fetch(this.url + separator + 'page=' + this.nextPage, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                        }).then((response) => response.json());

                        Promise.all([request, delay])
                            .then(([data]) => {
                                this.$refs.list.insertAdjacentHTML('beforeend', data.html);
                                this.hasMore = data.has_more;
                                this.nextPage = data.next_page;
                                this.loading = false;
                            })
                            .catch(() => {
                                // Stop trying on error; the already-loaded messages remain.
                                this.hasMore = false;
                                this.loading = false;
                            });
                    },
                };
            }
        </script>
    @endpush
@endonce
