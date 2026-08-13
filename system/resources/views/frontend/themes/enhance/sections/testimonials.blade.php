<section class="section section-surface" id="testimonials" aria-labelledby="testimonials-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-success">
                <i data-lucide="star"></i>
                4.9 average from 2,480 reviews
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

        <!--
            Every quote is written to roughly the same length so the rows land
            evenly. Uneven copy is what produces a ragged wall of cards.
        -->
        <div class="testimonial-grid mt-xl" data-reveal="fade">
            <figure class="testimonial testimonial-featured">
                @include('frontend.themes.enhance.components.rating', ['label' => __('Rated 5 out of 5')])

                <blockquote class="testimonial__text">
                    <p>
                        We reshot an entire catalogue because the originals were 1200px.
                        PixEnhance would have saved us <strong>$40,000</strong> and three weeks.
                    </p>
                </blockquote>

                <figcaption class="testimonial__author">
                    <img class="testimonial__avatar" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-1.svg') }}" alt="" width="40" height="40" loading="lazy">
                    <span>
                        <span class="testimonial__name">Marta Kovač</span>
                        <span class="testimonial__role">Head of Content, Northwind</span>
                    </span>
                    <span class="badge badge-sm badge-success testimonial__badge">
                        <i data-lucide="badge-check"></i>
                        Verified
                    </span>
                </figcaption>
            </figure>

            <figure class="testimonial">
                @include('frontend.themes.enhance.components.rating', ['label' => __('Rated 5 out of 5')])

                <blockquote class="testimonial__text">
                    <p>
                        We push 12,000 listing photos a day through the API. Latency stays
                        under three seconds and we've had one failed job all quarter.
                    </p>
                </blockquote>

                <figcaption class="testimonial__author">
                    <img class="testimonial__avatar" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-3.svg') }}" alt="" width="40" height="40" loading="lazy">
                    <span>
                        <span class="testimonial__name">Priya Raghunathan</span>
                        <span class="testimonial__role">Staff Engineer, Orbitly</span>
                    </span>
                </figcaption>
            </figure>

            <figure class="testimonial">
                @include('frontend.themes.enhance.components.rating', ['label' => __('Rated 5 out of 5')])

                <blockquote class="testimonial__text">
                    <p>
                        Print clients ask for 300 DPI on files shot for Instagram. PixEnhance
                        turns an impossible request into a two-minute job.
                    </p>
                </blockquote>

                <figcaption class="testimonial__author">
                    <img class="testimonial__avatar" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-4.svg') }}" alt="" width="40" height="40" loading="lazy">
                    <span>
                        <span class="testimonial__name">Jonas Lindqvist</span>
                        <span class="testimonial__role">Creative Director, PixelForge</span>
                    </span>
                </figcaption>
            </figure>

            <figure class="testimonial">
                @include('frontend.themes.enhance.components.rating', ['label' => __('Rated 5 out of 5')])

                <blockquote class="testimonial__text">
                    <p>
                        I restore family archives professionally. The face model doesn't invent
                        a new person — it brings back who was already there.
                    </p>
                </blockquote>

                <figcaption class="testimonial__author">
                    <img class="testimonial__avatar" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-2.svg') }}" alt="" width="40" height="40" loading="lazy">
                    <span>
                        <span class="testimonial__name">Daniel Okafor</span>
                        <span class="testimonial__role">Photo restorer, Lagos</span>
                    </span>
                </figcaption>
            </figure>

            <figure class="testimonial">
                @include('frontend.themes.enhance.components.rating', ['label' => __('Rated 5 out of 5')])

                <blockquote class="testimonial__text">
                    <p>
                        The background removal handles curly hair without a single manual mask.
                        I'm not sure people understand how rare that is.
                    </p>
                </blockquote>

                <figcaption class="testimonial__author">
                    <img class="testimonial__avatar" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-5.svg') }}" alt="" width="40" height="40" loading="lazy">
                    <span>
                        <span class="testimonial__name">Amara Diallo</span>
                        <span class="testimonial__role">Founder, Studio Nimbus</span>
                    </span>
                </figcaption>
            </figure>

            <figure class="testimonial">
                @include('frontend.themes.enhance.components.rating', ['label' => __('Rated 5 out of 5')])

                <blockquote class="testimonial__text">
                    <p>
                        We switched from a self-hosted ESRGAN setup, deleted 900 lines of queue
                        code, and cut our GPU bill by sixty percent.
                    </p>
                </blockquote>

                <figcaption class="testimonial__author">
                    <img class="testimonial__avatar" src="{{ asset('assets/frontend/enhance/img/avatars/avatar-6.svg') }}" alt="" width="40" height="40" loading="lazy">
                    <span>
                        <span class="testimonial__name">Tomás Herrera</span>
                        <span class="testimonial__role">CTO, VertexLab</span>
                    </span>
                </figcaption>
            </figure>
        </div>
    </div>
</section>
