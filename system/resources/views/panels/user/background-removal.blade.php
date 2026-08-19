<x-layouts.user :title="__('Background Removal')" :search-placeholder="__('Search cut-outs')">
    @php
        $sample = fn(string $file) => asset("assets/frontend/enhance/img/samples/{$file}");
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Background Removal') }}</h1>
            <p class="dash__subtitle">
                {{ __('Alpha-accurate cut-outs that survive hair, fur, glass and motion blur.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.dashboard') }}">
                <i data-lucide="wand-sparkles"></i>
                {{ __('All tools') }}
            </a>
            <label class="btn btn-primary btn-sm" for="studio-file" data-ripple>
                <i data-lucide="cloud-upload"></i>
                {{ __('Upload image') }}
            </label>
        </div>
    </div>

    <div class="studio" x-data="enhanceStudio({
            endpoint: @js(route('user.render-jobs.store')),
            tool: 'background-removal',
            demo: true,
            name: 'jack-russell.jpg',
            meta: '2.2 MB · 1200 × 900',
            model: 'auto',
            subject: 'auto',
            noScale: true,
            fixedCost: 1,
            baseSize: [1200, 900],
            stages: [
                'Detecting the subject',
                'Tracing the boundary',
                'Refining hair and fur',
                'Cutting the alpha channel',
                'Writing the transparent output'
            ]
         })">
        <section class="studio__stage" aria-label="{{ __('Preview') }}">
            <input class="sr-only" type="file" id="studio-file" name="source"
                accept="image/jpeg,image/png,image/webp,image/avif" @change="onChange($event)">

            <div class="studio__bar">
                <span class="studio__file" x-show="status !== 'empty'" x-cloak>
                    <i data-lucide="image"></i>
                    <span class="studio__file-name" x-text="source.name">jack-russell.jpg</span>
                </span>

                <span class="studio__file-meta" x-show="status !== 'empty'" x-cloak x-text="source.meta">2.2 MB · 1200 ×
                    900</span>

                <span class="studio__file" x-show="status === 'empty'" x-cloak>
                    <i data-lucide="image-plus"></i>
                    <span class="studio__file-name">{{ __('No image loaded') }}</span>
                </span>

                <span class="studio__bar-actions">
                    <span class="badge badge-sm badge-success" x-show="status === 'done'" x-cloak>
                        <i data-lucide="circle-check"></i>
                        {{ __('Done') }}
                    </span>
                    <span class="badge badge-sm badge-primary" x-show="status === 'running'" x-cloak>
                        <i data-lucide="refresh-cw"></i>
                        {{ __('Rendering') }}
                    </span>

                    <label class="btn btn-outline btn-sm" for="studio-file" x-show="status !== 'running'" x-cloak>
                        <i data-lucide="image-plus"></i>
                        {{ __('Replace') }}
                    </label>

                    <button type="button" class="icon-btn" @click="reset()"
                        x-show="status === 'done' || status === 'ready'" x-cloak
                        aria-label="{{ __('Clear this image') }}">
                        <i data-lucide="x"></i>
                    </button>
                </span>
            </div>

            <div class="studio__canvas" @dragover.prevent="onDragOver()" @dragleave.prevent="onDragLeave($event)"
                @drop.prevent="onDrop($event)">
                <div class="studio__veil" x-show="dragging" x-cloak aria-hidden="true">
                    <span class="dropzone__icon"><i data-lucide="cloud-upload"></i></span>
                    <span class="dropzone__title">{{ __('Drop to load this image') }}</span>
                </div>

                <label class="dropzone studio__dropzone" for="studio-file" x-show="status === 'empty'" x-cloak>
                    <span class="dropzone__icon" aria-hidden="true"><i data-lucide="cloud-upload"></i></span>
                    <span class="dropzone__title">{{ __('Drop an image to cut out') }}</span>
                    <span class="dropzone__text">{{ __('JPG, PNG, WEBP or AVIF up to 50 MB') }}</span>

                    <span class="format-list" aria-hidden="true">
                        <span class="format-pill">jpg</span>
                        <span class="format-pill">png</span>
                        <span class="format-pill">webp</span>
                        <span class="format-pill">avif</span>
                    </span>
                </label>

                <div class="studio__running" x-show="status === 'running'" x-cloak>
                    <div class="studio__running-preview">
                        <img :src="source.url" alt="" width="960" height="720">
                        <span class="scanline" aria-hidden="true"></span>
                    </div>

                    <p class="studio__stage-name" x-text="stage" aria-live="polite">{{ __('Detecting the subject') }}
                    </p>

                    <div class="studio__progress">
                        <div class="progress">
                            <div class="progress__bar progress__bar-striped"
                                x-effect="$el.style.setProperty('--progress', progress + '%')"></div>
                        </div>
                        <div class="studio__progress-meta">
                            <span x-text="`${Math.round(progress)}%`">0%</span>
                            <span x-text="outputSize">1200 × 900</span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline btn-sm" @click="cancel()">
                        {{ __('Cancel') }}
                    </button>
                </div>

                <div class="studio__preview" x-show="status === 'ready'" x-cloak>
                    <img :src="source.url" alt="{{ __('The image waiting for background removal') }}" decoding="async">
                    <p class="studio__preview-hint">
                        <i data-lucide="sparkles"></i>
                        {{ __('Ready — pick your settings and hit Enhance.') }}
                    </p>
                </div>

                <div class="studio__result" x-show="status === 'done'" x-cloak>
                    <div class="compare" data-compare data-compare-start="52">
                        <div class="compare__frame">
                            <img class="compare__layer" x-ref="sourceLayer" src="{{ $sample('bgr-before.webp') }}"
                                :src="source.url || $el.src"
                                alt="{{ __('A terrier photographed against a wooded background') }}" width="1200"
                                height="900" decoding="async">
                            <img class="compare__layer compare__layer-after" src="{{ $sample('bgr-after.webp') }}"
                                :src="result.url || $el.src"
                                alt="{{ __('The same terrier cut out, shown against a transparency checkerboard') }}"
                                width="1200" height="900" decoding="async">
                        </div>

                        <label class="sr-only" for="bgr-compare">{{ __('Reveal the processed image') }}</label>
                        <input class="compare__range" type="range" id="bgr-compare" data-compare-range min="0" max="100"
                            value="52" step="0.1" aria-label="{{ __('Compare the original and the result') }}">

                        <span class="compare__tag compare__tag-before">{{ __('Original') }}</span>
                        <span class="compare__tag compare__tag-after">
                            <i data-lucide="sparkles"></i>
                            {{ __('Cut out') }}
                        </span>

                        <span class="compare__meta compare__meta-before">1200 × 900</span>
                        <span class="compare__meta compare__meta-after" x-text="outputSize">1200 × 900</span>

                        <span class="compare__hint">
                            <i data-lucide="move-horizontal"></i>
                            {{ __('Drag to compare') }}
                        </span>

                        <span class="compare__handle" aria-hidden="true">
                            <span class="compare__grip"><i data-lucide="move-horizontal"></i></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="studio__foot">
                <div class="studio__foot-meta">
                    <span>{{ __('Output') }} <strong x-text="outputSize">1200 × 900</strong></span>
                    <span>{{ __('Format') }} <strong x-text="format.toUpperCase()">PNG</strong></span>
                    <span x-show="status === 'done'" x-cloak>{{ __('Rendered in') }} <strong>1.6s</strong></span>
                </div>

                <div class="studio__foot-actions">
                    <button type="button" class="btn btn-outline btn-sm" :class="status !== 'done' && 'is-disabled'"
                        :aria-disabled="status !== 'done'">
                        <i data-lucide="share-2"></i>
                        {{ __('Share') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" :class="status !== 'done' && 'is-disabled'"
                        :aria-disabled="status !== 'done'" data-ripple>
                        <i data-lucide="download"></i>
                        {{ __('Download') }}
                    </button>
                </div>
            </div>
        </section>

        <form class="studio__rail" action="{{ route('user.render-jobs.store') }}" method="post" enctype="multipart/form-data" @submit.prevent="run()">
            @csrf
            <div class="studio__rail-head">
                <span class="studio__rail-title">
                    <i data-lucide="eraser"></i>
                    {{ __('Cut out') }}
                </span>
                <span class="badge badge-sm badge-primary">{{ __('Auto') }}</span>
            </div>

            <div class="control-stack">
                <label class="control-row__label" for="bg-subject">
                    <i data-lucide="scan-search"></i>
                    {{ __('Subject') }}
                </label>
                <select class="select" id="bg-subject" name="subject" x-model="subject">
                    <option value="auto">{{ __('Auto detect') }}</option>
                    <option value="person">{{ __('Person') }}</option>
                    <option value="product">{{ __('Product') }}</option>
                    <option value="animal">{{ __('Animal') }}</option>
                    <option value="car">{{ __('Vehicle') }}</option>
                </select>
            </div>

            <div class="control-stack">
                <label class="control-row__label" for="bg-model">
                    <i data-lucide="cpu"></i>
                    {{ __('Model') }}
                </label>
                <select class="select" id="bg-model" name="model" x-model="model">
                    <option value="auto">{{ __('Auto — choose for this image') }}</option>
                    @foreach ($imageModels ?? [] as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="control-stack">
                <span class="cluster cluster-between">
                    <label class="control-row__label" for="bg-edge">
                        <i data-lucide="focus"></i>
                        {{ __('Edge refinement') }}
                    </label>
                    <span class="control-row__value" x-text="`${edge}%`">60%</span>
                </span>
                <input class="range" type="range" id="bg-edge" name="edge" min="0" max="100" x-model="edge">
            </div>

            <div class="control-row">
                <label class="control-row__label" for="bg-hair">
                    <i data-lucide="wand-sparkles"></i>
                    {{ __('Hair & fur detail') }}
                </label>
                <span class="switch-field">
                    <input class="switch-field__input" type="checkbox" id="bg-hair" name="hair" x-model="hair">
                    <span class="switch-field__track"></span>
                </span>
            </div>

            <div class="control-row">
                <label class="control-row__label" for="bg-shadow">
                    <i data-lucide="layers"></i>
                    {{ __('Keep contact shadow') }}
                </label>
                <span class="switch-field">
                    <input class="switch-field__input" type="checkbox" id="bg-shadow" name="shadow" x-model="shadow">
                    <span class="switch-field__track"></span>
                </span>
            </div>

            <div class="control-row">
                <span class="control-row__label">
                    <i data-lucide="palette"></i>
                    {{ __('Backdrop') }}
                </span>
                <span class="radio-group">
                    <span class="radio-group__option">
                        <input class="radio-group__input" type="radio" id="bg-back-none" name="backdrop"
                            value="transparent" x-model="backdrop">
                        <label class="radio-group__label" for="bg-back-none">{{ __('None') }}</label>
                    </span>
                    <span class="radio-group__option">
                        <input class="radio-group__input" type="radio" id="bg-back-white" name="backdrop" value="white"
                            x-model="backdrop">
                        <label class="radio-group__label" for="bg-back-white">{{ __('White') }}</label>
                    </span>
                    <span class="radio-group__option">
                        <input class="radio-group__input" type="radio" id="bg-back-blur" name="backdrop" value="blur"
                            x-model="backdrop">
                        <label class="radio-group__label" for="bg-back-blur">{{ __('Blur') }}</label>
                    </span>
                </span>
            </div>

            <div class="control-row">
                <label class="control-row__label" for="bg-format">
                    <i data-lucide="file-text"></i>
                    {{ __('Output') }}
                </label>
                <select class="select input-sm" id="bg-format" name="format" x-model="format">
                    <option value="png">PNG</option>
                    <option value="webp">WEBP</option>
                </select>
            </div>

            <div class="studio__cost">
                <span>{{ __('This render') }}</span>
                <strong>
                    <i data-lucide="coins"></i>
                    <span x-text="`${cost} credit${cost === 1 ? '' : 's'}`">1 credit</span>
                </strong>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block studio__rail-submit"
                :class="(status === 'empty' || busy) && 'is-disabled'" :aria-disabled="status === 'empty' || busy"
                data-ripple>
                <i data-lucide="eraser"></i>
                <span
                    x-text="busy ? '{{ __('Cutting out...') }}' : '{{ __('Remove background') }}'">{{ __('Remove background') }}</span>
            </button>
        </form>
    </div>

    <section class="mt-xl" aria-labelledby="tool-recent-title">
        <div class="dash__section-head">
            <h2 class="dash__section-title" id="tool-recent-title">
                <i data-lucide="clock"></i>
                {{ __('Recent cut-outs') }}
            </h2>

            <a class="btn-link btn-link-sm" href="{{ route('user.projects', ['tool' => 'background-removal']) }}">
                {{ __('View all') }}
                <i data-lucide="arrow-right"></i>
            </a>
        </div>

        <div class="job-grid">
            @forelse ($recentEnhancements ?? collect() as $project)
                @include('panels.user.partials.project-card', ['project' => $project, 'statusMeta' => \App\Modules\RenderJobs\Models\RenderJob::statuses()])
            @empty
                <div class="empty-state">
                    <span class="empty-state__icon" aria-hidden="true"><i data-lucide="eraser"></i></span>
                    <h3>{{ __('No cut-outs yet') }}</h3>
                    <p>{{ __('Upload an image above to save your first background removal.') }}</p>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.user>
