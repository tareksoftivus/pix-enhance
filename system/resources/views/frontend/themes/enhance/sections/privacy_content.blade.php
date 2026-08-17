@php
    $d = $section->data ?? [];

    $title = $d['title'] ?? 'Privacy practices';
    $content = $d['content'] ?? '<p>PixEnhance collects the information needed to provide accounts, process images, secure the platform and improve product reliability.</p>';
@endphp

<section class="section legal-content" aria-labelledby="privacy-content-title">
    <div class="shell legal-content__shell">
        <article class="legal-document card card-glass card-pad-lg" data-reveal="up">
            <h2 id="privacy-content-title">{{ $title }}</h2>
            {!! $content !!}
        </article>
    </div>
</section>
