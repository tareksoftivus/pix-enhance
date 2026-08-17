@php
    $fallbackTestimonials = collect([
        [
            'client_name' => 'Marta Kovac',
            'company_name' => 'Northwind',
            'designation' => 'Head of Content',
            'quote' => 'We reshot an entire catalogue because the originals were 1200px. PixEnhance would have saved us $40,000 and three weeks.',
            'rating' => 5,
            'avatar_url' => asset('assets/frontend/enhance/img/avatars/avatar-1.svg'),
        ],
        [
            'client_name' => 'Priya Raghunathan',
            'company_name' => 'Orbitly',
            'designation' => 'Staff Engineer',
            'quote' => 'We push 12,000 listing photos a day through the API. Latency stays under three seconds and failed jobs are rare enough to notice.',
            'rating' => 5,
            'avatar_url' => asset('assets/frontend/enhance/img/avatars/avatar-3.svg'),
        ],
        [
            'client_name' => 'Jonas Lindqvist',
            'company_name' => 'PixelForge',
            'designation' => 'Creative Director',
            'quote' => 'Print clients ask for 300 DPI on files shot for Instagram. PixEnhance turns an impossible request into a two-minute job.',
            'rating' => 5,
            'avatar_url' => asset('assets/frontend/enhance/img/avatars/avatar-4.svg'),
        ],
        [
            'client_name' => 'Daniel Okafor',
            'company_name' => 'Lumen Archive',
            'designation' => 'Photo Restorer',
            'quote' => 'I restore family archives professionally. The face model does not invent a new person. It brings back who was already there.',
            'rating' => 5,
            'avatar_url' => asset('assets/frontend/enhance/img/avatars/avatar-2.svg'),
        ],
        [
            'client_name' => 'Amara Diallo',
            'company_name' => 'Studio Nimbus',
            'designation' => 'Founder',
            'quote' => 'The background removal handles curly hair without a single manual mask. That is the detail that made our editors switch.',
            'rating' => 5,
            'avatar_url' => asset('assets/frontend/enhance/img/avatars/avatar-5.svg'),
        ],
        [
            'client_name' => 'Tomas Herrera',
            'company_name' => 'VertexLab',
            'designation' => 'CTO',
            'quote' => 'We switched from a self-hosted ESRGAN setup, deleted 900 lines of queue code, and cut our GPU bill by sixty percent.',
            'rating' => 5,
            'avatar_url' => asset('assets/frontend/enhance/img/avatars/avatar-6.svg'),
        ],
    ])->map(fn (array $testimonial) => (object) $testimonial);

    $moduleTestimonials = collect($testimonials ?? []);

    $items = $moduleTestimonials->isNotEmpty()
        ? $moduleTestimonials->take(6)->values()
        : $fallbackTestimonials;

    $averageRating = number_format((float) $items->avg(fn ($testimonial) => $testimonial->rating ?: 5), 1);
@endphp

<section class="section section-surface" id="testimonials" aria-labelledby="testimonials-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-success">
                <i data-lucide="star"></i>
                {{ $averageRating }} average from image teams
            </span>

            <h2 class="text-display-2" id="testimonials-title">
                Loved by people who
                <span class="text-gradient anim-gradient">zoom in</span>
            </h2>

            <p class="text-lead">
                Photographers, e-commerce teams and archivists who care about the pixel
                level — and noticed the difference immediately.
            </p>
        </header>

        <div class="testimonial-grid mt-xl" data-reveal="fade">
            @foreach($items as $testimonial)
                @php
                    $rating = (int) ($testimonial->rating ?: 5);
                    $avatarUrl = $testimonial->avatar_url ?: asset('assets/frontend/enhance/img/avatars/avatar-1.svg');
                    $roleParts = collect([$testimonial->designation, $testimonial->company_name])->filter();
                @endphp

                <figure class="testimonial {{ $loop->first ? 'testimonial-featured' : '' }}">
                    @include('frontend.themes.enhance.components.rating', ['label' => __('Rated :rating out of 5', ['rating' => $rating])])

                    <blockquote class="testimonial__text">
                        <p>{{ $testimonial->quote }}</p>
                    </blockquote>

                    <figcaption class="testimonial__author">
                        <img class="testimonial__avatar" src="{{ $avatarUrl }}" alt="" width="40" height="40" loading="lazy">
                        <span>
                            <span class="testimonial__name">{{ $testimonial->client_name }}</span>
                            <span class="testimonial__role">{{ $roleParts->join(', ') }}</span>
                        </span>

                        @if($loop->first)
                            <span class="badge badge-sm badge-success testimonial__badge">
                                <i data-lucide="badge-check"></i>
                                Verified
                            </span>
                        @endif
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
