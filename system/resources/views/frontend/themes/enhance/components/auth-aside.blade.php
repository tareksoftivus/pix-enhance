<aside class="auth__aside">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-auth"></span>
    </div>

    <div class="auth-aside__inner">
        <span class="badge badge-primary">
            <i data-lucide="sparkles"></i>
            {{ $eyebrow ?? __('Trusted in 90+ countries') }}
        </span>

        <h2 class="auth-aside__title">{{ $title ?? __('Every pixel your camera never captured.') }}</h2>

        <p class="auth-aside__text">
            {{ $text ?? __('Upscale to 16K, restore faces and clear out noise in seconds, from any browser.') }}
        </p>

        <ul class="showcase__list">
            <li class="showcase__list-item">
                <span class="showcase__list-icon" aria-hidden="true"><i data-lucide="check"></i></span>
                <span>
                    <strong>{{ __('10 free credits to start') }}</strong>
                    {{ __('No card, no trial countdown. Spend them whenever you like.') }}
                </span>
            </li>
            <li class="showcase__list-item">
                <span class="showcase__list-icon" aria-hidden="true"><i data-lucide="check"></i></span>
                <span>
                    <strong>{{ __('Your images stay yours') }}</strong>
                    {{ __('Encrypted in transit, deleted from our servers after 24 hours.') }}
                </span>
            </li>
            <li class="showcase__list-item">
                <span class="showcase__list-icon" aria-hidden="true"><i data-lucide="check"></i></span>
                <span>
                    <strong>{{ __('An API when you outgrow the UI') }}</strong>
                    {{ __('One REST call, webhooks on completion, SDKs for PHP and Node.') }}
                </span>
            </li>
        </ul>

        <div class="auth-aside__proof">
            <ul class="avatar-stack avatar-stack-sm">
                <li><img class="avatar-stack__item" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-1.svg') }}" alt="" width="28" height="28" loading="lazy"></li>
                <li><img class="avatar-stack__item" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-2.svg') }}" alt="" width="28" height="28" loading="lazy"></li>
                <li><img class="avatar-stack__item" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-3.svg') }}" alt="" width="28" height="28" loading="lazy"></li>
                <li><img class="avatar-stack__item" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-4.svg') }}" alt="" width="28" height="28" loading="lazy"></li>
                <li><span class="avatar-stack__more">{{ __('+9k') }}</span></li>
            </ul>

            <p class="auth-aside__proof-text">
                <strong>{{ __('120,000+ creators') }}</strong>
                {{ __('Rated 4.9 out of 5 across 2,480 reviews.') }}
            </p>
        </div>
    </div>
</aside>
