<x-layouts.user :title="__('New Ticket')" :search-placeholder="__('Search tickets')">
    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('New ticket') }}</h1>
            <p class="dash__subtitle">
                {{ __('Share the issue, choose the right priority, and the support team will reply in your thread.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.support-tickets.index') }}">
                <i data-lucide="arrow-left"></i>
                {{ __('Back to support') }}
            </a>
        </div>
    </div>

    <div class="form-grid form-grid-2">
        <section class="panel" aria-labelledby="new-ticket-title">
            <div class="panel__head">
                <h2 class="panel__title" id="new-ticket-title">
                    <i data-lucide="message-square-plus"></i>
                    {{ __('Request details') }}
                </h2>
                <p class="panel__subtitle">{{ __('Clear details help us route the ticket to the right person faster.') }}</p>
            </div>

            <form method="POST" action="{{ route('user.support-tickets.store') }}">
                @csrf

                <div class="panel__body">
                    <div class="form-grid form-grid-2">
                        <div class="field form-grid__full">
                            <label class="field__label" for="ticket-subject">
                                {{ __('Subject') }} <span class="required">*</span>
                            </label>
                            <input class="input @error('subject') is-invalid @enderror" type="text" id="ticket-subject"
                                   name="subject" value="{{ old('subject') }}" required autofocus
                                   placeholder="{{ __('Example: Upscale job finished with artifacts') }}">
                            @error('subject')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="field__label" for="ticket-priority">
                                {{ __('Priority') }} <span class="required">*</span>
                            </label>
                            <select class="select @error('priority') is-invalid @enderror" id="ticket-priority" name="priority" required>
                                @foreach ($priorities as $value => $label)
                                    <option value="{{ $value }}" @selected(old('priority', 'medium') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="field__label" for="ticket-category">{{ __('Category') }}</label>
                            <select class="select @error('category') is-invalid @enderror" id="ticket-category" name="category">
                                @foreach ([
                                    '' => __('General'),
                                    'rendering' => __('Rendering'),
                                    'billing' => __('Billing'),
                                    'account' => __('Account'),
                                    'api' => __('API'),
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @else
                                <p class="field__hint">{{ __('Optional, but useful for routing.') }}</p>
                            @enderror
                        </div>

                        <div class="field form-grid__full">
                            <label class="field__label" for="ticket-body">
                                {{ __('Message') }} <span class="required">*</span>
                            </label>
                            <textarea class="textarea @error('body') is-invalid @enderror" id="ticket-body" name="body"
                                      rows="8" required placeholder="{{ __('Tell us what happened, what you expected, and any file or job reference that helps.') }}">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @else
                                <p class="field__hint">{{ __('Maximum 5,000 characters.') }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="panel__foot">
                    <p class="panel__note">{{ __('Replies appear in this dashboard and may also be sent by email.') }}</p>
                    <div class="cluster cluster-sm">
                        <a class="btn btn-ghost btn-sm" href="{{ route('user.support-tickets.index') }}">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary btn-sm" data-ripple>
                            <i data-lucide="send"></i>
                            {{ __('Submit ticket') }}
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <aside class="panel" aria-labelledby="support-guidelines-title">
            <div class="panel__head">
                <h2 class="panel__title" id="support-guidelines-title">
                    <i data-lucide="sparkles"></i>
                    {{ __('Helpful context') }}
                </h2>
            </div>

            <div class="panel__body">
                <div class="setting-row">
                    <span class="setting-row__icon" aria-hidden="true"><i data-lucide="image"></i></span>
                    <span class="setting-row__text">
                        <span class="setting-row__label">{{ __('Render issues') }}</span>
                        <span class="setting-row__hint">{{ __('Include image name, selected tool, scale and output format.') }}</span>
                    </span>
                </div>

                <div class="setting-row">
                    <span class="setting-row__icon setting-row__icon-accent" aria-hidden="true"><i data-lucide="credit-card"></i></span>
                    <span class="setting-row__text">
                        <span class="setting-row__label">{{ __('Billing questions') }}</span>
                        <span class="setting-row__hint">{{ __('Mention invoice number, plan name, or the credit pack you purchased.') }}</span>
                    </span>
                </div>

                <div class="setting-row">
                    <span class="setting-row__icon" aria-hidden="true"><i data-lucide="shield-alert"></i></span>
                    <span class="setting-row__text">
                        <span class="setting-row__label">{{ __('Account access') }}</span>
                        <span class="setting-row__hint">{{ __('Never share your password or recovery codes in a ticket.') }}</span>
                    </span>
                </div>
            </div>

            <div class="panel__foot">
                <p class="panel__note">{{ __('Typical first response time is within one business day.') }}</p>
            </div>
        </aside>
    </div>
</x-layouts.user>
