@php
    $d = $section->data ?? [];

    $title = $d['title'] ?? 'Getting started';
    $content = $d['content'] ?? '<p>PixEnhance helps you upload an image, choose the right enhancement workflow and export a clean result for web, print or product use.</p><h3 id="quickstart">Quickstart</h3><ol><li>Create an account or sign in to your dashboard.</li><li>Upload a JPG, PNG or WebP image from your workspace.</li><li>Select an enhancement mode such as upscale, restore, denoise or background cleanup.</li><li>Preview the output, then download the version that fits your project.</li></ol><h3 id="models">Enhancement models</h3><p>Use upscaling for small or compressed photos, restoration for damaged images, denoise for grainy captures and cleanup tools for commerce-ready product shots.</p><h3 id="sdks">API and SDKs</h3><p>Teams can connect PixEnhance to their own upload and publishing flows. Keep API credentials private, send source files over HTTPS and store returned asset URLs with your own records.</p><h3>Best practices</h3><ul><li>Start with the highest-quality source image available.</li><li>Use batch processing for repeated catalog or marketplace work.</li><li>Review restored faces, text and product edges before publishing.</li><li>Choose export dimensions that match the final channel instead of oversizing every asset.</li></ul>';
@endphp

<section class="section legal-content" aria-labelledby="docs-content-title">
    <div class="shell legal-content__shell">
        <article class="legal-document card card-glass card-pad-lg prose-enhance" data-reveal="up">
            <h2 id="docs-content-title">{{ $title }}</h2>
            {!! $content !!}
        </article>
    </div>
</section>
