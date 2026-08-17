@php
    $d = $section->data ?? [];

    $title = $d['title'] ?? 'Using PixEnhance';
    $content = $d['content'] ?? '<p>By accessing PixEnhance, you agree to use the service responsibly, keep your account credentials secure and follow applicable laws when uploading or processing images.</p>';
@endphp

<section class="section legal-content" aria-labelledby="terms-content-title">
    <div class="shell legal-content__shell">
        <article class="legal-document card card-glass card-pad-lg" data-reveal="up">
            <h2 id="terms-content-title">{{ $title }}</h2>
            {!! $content !!}
        </article>
    </div>
</section>
