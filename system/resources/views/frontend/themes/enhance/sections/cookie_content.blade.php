@php
    $d = $section->data ?? [];

    $title = $d['title'] ?? 'Cookie usage';
    $content = $d['content'] ?? '<p>PixEnhance uses cookies and similar technologies to run the website, remember choices, protect accounts and understand how the product is used.</p>';
@endphp

<section class="section legal-content" aria-labelledby="cookie-content-title">
    <div class="shell legal-content__shell">
        <article class="legal-document card card-glass card-pad-lg" data-reveal="up">
            <h2 id="cookie-content-title">{{ $title }}</h2>
            {!! $content !!}
        </article>
    </div>
</section>
