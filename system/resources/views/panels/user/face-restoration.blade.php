<x-layouts.user :title="__('Face Restoration')" :search-placeholder="__('Search restored portraits')">
    @php
        $sample = fn (string $file) => asset("assets/frontend/enhance/img/samples/{$file}");
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Face Restoration') }}</h1>
            <p class="dash__subtitle">
                {{ __('Rebuild eyes, teeth and skin texture — while keeping the person themselves.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.dashboard') }}">
                <i data-lucide="wand-sparkles"></i>
                {{ __('All tools') }}
            </a>
            <label class="btn btn-primary btn-sm" for="studio-file" data-ripple>
                <i data-lucide="cloud-upload"></i>
                {{ __('Upload photo') }}
            </label>
        </div>
    </div>

    <div class="studio"
         x-data="enhanceStudio({
            demo: true,
            name: 'family-1927.jpg',
            meta: '1.4 MB · 512 × 590',
            model: 'vintage',
            scale: '4',
            baseSize: [512, 590],
            stages: [
                'Detecting faces',
                'Aligning facial landmarks',
                'Rebuilding eyes and mouth',
                'Reconstructing skin texture',
                'Blending and writing the output'
            ]
         })">
        <section class="studio__stage" aria-label="{{ __('Preview') }}">
            <input class="sr-only" type="file" id="studio-file" name="source"
                   accept="image/jpeg,image/png,image/webp,image/avif,image/tiff"
                   @change="onChange($event)">

            <div class="studio__bar">
                <span class="studio__file" x-show="status !== 'empty'" x-cloak>
                    <i data-lucide="image"></i>
                    <span class="studio__file-name" x-text="source.name">family-1927.jpg</span>
                </span>

                <span class="studio__file-meta" x-show="status !== 'empty'" x-cloak
                      x-text="source.meta">1.4 MB · 512 × 590</span>

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

            <div class="studio__canvas"
                 @dragover.prevent="onDragOver()"
                 @dragleave.prevent="onDragLeave($event)"
                 @drop.prevent="onDrop($event)">
                <div class="studio__veil" x-show="dragging" x-cloak aria-hidden="true">
                    <span class="dropzone__icon"><i data-lucide="cloud-upload"></i></span>
                    <span class="dropzone__title">{{ __('Drop to load this image') }}</span>
                </div>

                <label class="dropzone studio__dropzone" for="studio-file"
                       x-show="status === 'empty'" x-cloak>
                    <span class="dropzone__icon" aria-hidden="true"><i data-lucide="cloud-upload"></i></span>
                    <span class="dropzone__title">{{ __('Drop a portrait or old scan') }}</span>
                    <span class="dropzone__text">{{ __('JPG, PNG, WEBP, AVIF, HEIC or TIFF up to 50 MB') }}</span>

                    <span class="format-list" aria-hidden="true">
                        <span class="format-pill">jpg</span>
                        <span class="format-pill">png</span>
                        <span class="format-pill">webp</span>
                        <span class="format-pill">avif</span>
                        <span class="format-pill">tiff</span>
                    </span>
                </label>

                <div class="studio__running" x-show="status === 'running'" x-cloak>
                    <div class="studio__running-preview">
                        <img :src="source.url" alt="" width="960" height="720">
                        <span class="scanline" aria-hidden="true"></span>
                    </div>

                    <p class="studio__stage-name" x-text="stage" aria-live="polite">{{ __('Detecting faces') }}</p>

                    <div class="studio__progress">
                        <div class="progress">
                            <div class="progress__bar progress__bar-striped"
                                 x-effect="$el.style.setProperty('--progress', progress + '%')"></div>
                        </div>
                        <div class="studio__progress-meta">
                            <span x-text="`${Math.round(progress)}%`">0%</span>
                            <span x-text="outputSize">2048 × 2360</span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline btn-sm" @click="cancel()">
                        {{ __('Cancel') }}
                    </button>
                </div>

                <div class="studio__preview" x-show="status === 'ready'" x-cloak>
                    <img :src="source.url" alt="{{ __('The portrait waiting to be restored') }}" decoding="async">
                    <p class="studio__preview-hint">
                        <i data-lucide="sparkles"></i>
                        {{ __('Ready — pick your settings and hit Enhance.') }}
                    </p>
                </div>

                <div class="studio__result" x-show="status === 'done'" x-cloak>
                    <div class="compare" data-compare data-compare-start="52">
                        <div class="compare__frame">
                            <img class="compare__layer" x-ref="sourceLayer"
                                 src="{{ $sample('family-before.webp') }}"
                                 :src="source.url || $el.src"
                                 alt="{{ __('A 1920s family portrait as a faded, low-resolution scan') }}"
                                 width="1200" height="900" decoding="async">
                            <img class="compare__layer compare__layer-after"
                                 src="{{ $sample('family-after.webp') }}"
                                 :src="result.url || $el.src"
                                 alt="{{ __('The same portrait after restoration, with recovered facial detail') }}"
                                 width="1200" height="900" decoding="async">
                        </div>

                        <label class="sr-only" for="face-compare">{{ __('Reveal the processed image') }}</label>
                        <input class="compare__range" type="range" id="face-compare" data-compare-range
                               min="0" max="100" value="52" step="0.1"
                               aria-label="{{ __('Compare the original and the result') }}">

                        <span class="compare__tag compare__tag-before">{{ __('Original scan') }}</span>
                        <span class="compare__tag compare__tag-after">
                            <i data-lucide="sparkles"></i>
                            {{ __('Restored') }}
                        </span>

                        <span class="compare__meta compare__meta-before">512 × 590</span>
                        <span class="compare__meta compare__meta-after" x-text="outputSize">2048 × 2360</span>

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
                    <span>{{ __('Output') }} <strong x-text="outputSize">2048 × 2360</strong></span>
                    <span>{{ __('Format') }} <strong x-text="format.toUpperCase()">PNG</strong></span>
                    <span x-show="status === 'done'" x-cloak>{{ __('Rendered in') }} <strong>3.2s</strong></span>
                </div>

                <div class="studio__foot-actions">
                    <button type="button" class="btn btn-outline btn-sm"
                            :class="status !== 'done' && 'is-disabled'" :aria-disabled="status !== 'done'">
                        <i data-lucide="share-2"></i>
                        {{ __('Share') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm"
                            :class="status !== 'done' && 'is-disabled'" :aria-disabled="status !== 'done'"
                            data-ripple>
                        <i data-lucide="download"></i>
                        {{ __('Download') }}
                    </button>
                </div>
            </div>
        </section>

        <form class="studio__rail" action="#" method="post" @submit.prevent="run()">
            @csrf
            <div class="studio__rail-head">
                <span class="studio__rail-title">
                    <i data-lucide="scan-face"></i>
                    {{ __('Restore') }}
                </span>
                <span class="badge badge-sm badge-success">{{ __('3 faces') }}</span>
            </div>

            <div class="control-stack">
                <label class="control-row__label" for="fr-model">
                    <i data-lucide="cpu"></i>
                    {{ __('Model') }}
                </label>
                <select class="select" id="fr-model" name="model" x-model="model">
                    <option value="face-v3">{{ __('Face v3 — general portraits') }}</option>
                    <option value="portrait-soft">{{ __('Portrait Soft — modern photography') }}</option>
                    <option value="vintage">{{ __('Vintage Scan — film & prints') }}</option>
                </select>
            </div>

            <div class="control-stack">
                <span class="cluster cluster-between">
                    <label class="control-row__label" for="fr-fidelity">
                        <i data-lucide="fingerprint"></i>
                        {{ __('Identity fidelity') }}
                    </label>
                    <span class="control-row__value" x-text="`${fidelity}%`">65%</span>
                </span>
                <input class="range" type="range" id="fr-fidelity" name="fidelity" min="0" max="100" x-model="fidelity">
                <p class="field__hint">{{ __('Higher keeps the person; lower produces a cleaner face.') }}</p>
            </div>

            <div class="control-row">
                <label class="control-row__label" for="fr-detail-toggle">
                    <i data-lucide="focus"></i>
                    {{ __('Skin texture') }}
                </label>
                <span class="switch-field">
                    <input class="switch-field__input" type="checkbox" id="fr-detail-toggle" name="skin" x-model="colour">
                    <span class="switch-field__track"></span>
                </span>
            </div>

            <div class="control-row">
                <label class="control-row__label" for="fr-denoise">
                    <i data-lucide="eraser"></i>
                    {{ __('Remove scan grain') }}
                </label>
                <span class="switch-field">
                    <input class="switch-field__input" type="checkbox" id="fr-denoise" name="denoise" x-model="denoise">
                    <span class="switch-field__track"></span>
                </span>
            </div>

            <div class="control-row">
                <span class="control-row__label">
                    <i data-lucide="maximize-2"></i>
                    {{ __('Also upscale') }}
                </span>
                <span class="radio-group">
                    @foreach (['1', '2', '4'] as $scale)
                        <span class="radio-group__option">
                            <input class="radio-group__input" type="radio" id="fr-scale-{{ $scale }}" name="scale" value="{{ $scale }}" x-model="scale">
                            <label class="radio-group__label" for="fr-scale-{{ $scale }}">{{ $scale }}×</label>
                        </span>
                    @endforeach
                </span>
            </div>

            <div class="control-row">
                <label class="control-row__label" for="fr-format">
                    <i data-lucide="file-text"></i>
                    {{ __('Output') }}
                </label>
                <select class="select input-sm" id="fr-format" name="format" x-model="format">
                    <option value="png">PNG</option>
                    <option value="jpg">JPG</option>
                    <option value="tiff">TIFF</option>
                </select>
            </div>

            <div class="studio__cost">
                <span>{{ __('This render') }}</span>
                <strong>
                    <i data-lucide="coins"></i>
                    <span x-text="`${cost} credit${cost === 1 ? '' : 's'}`">2 credits</span>
                </strong>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block studio__rail-submit"
                    :class="(status === 'empty' || busy) && 'is-disabled'"
                    :aria-disabled="status === 'empty' || busy" data-ripple>
                <i data-lucide="scan-face"></i>
                <span x-text="busy ? '{{ __('Restoring...') }}' : '{{ __('Restore faces') }}'">{{ __('Restore faces') }}</span>
            </button>
        </form>
    </div>

    <section class="mt-xl" aria-labelledby="tool-recent-title">
        <div class="dash__section-head">
            <h2 class="dash__section-title" id="tool-recent-title">
                <i data-lucide="clock"></i>
                {{ __('Recent restorations') }}
            </h2>

            <a class="btn-link btn-link-sm" href="#">
                {{ __('View all') }}
                <i data-lucide="arrow-right"></i>
            </a>
        </div>

        <div class="job-grid">
            @foreach ([
                ['img' => 'feature-face.webp', 'name' => 'studio-portrait.png', 'meta' => '2048 × 1280', 'time' => __('12 min ago')],
                ['img' => 'family-after.webp', 'name' => 'family-1927.jpg', 'meta' => '2048 × 2360', 'time' => __('1 hr ago')],
                ['img' => 'feature-cutout.webp', 'name' => 'lookbook-03.png', 'meta' => '1800 × 1124', 'time' => __('3 hrs ago')],
                ['img' => 'thumb-2.webp', 'name' => 'reunion-1984.tif', 'meta' => '1280 × 1280', 'time' => __('Yesterday')],
            ] as $job)
                <article class="job-card">
                    <div class="job-card__media">
                        <img src="{{ $sample($job['img']) }}" alt="" width="320" height="320" loading="lazy" decoding="async">
                        <span class="badge badge-sm badge-success job-card__status">
                            <i data-lucide="circle-check"></i>
                            {{ __('Done') }}
                        </span>
                        <div class="job-card__tools">
                            <a class="job-card__tool" href="#" aria-label="{{ __('Open :name', ['name' => $job['name']]) }}">
                                <i data-lucide="eye"></i>
                            </a>
                            <button type="button" class="job-card__tool" aria-label="{{ __('Download :name', ['name' => $job['name']]) }}">
                                <i data-lucide="download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="job-card__body">
                        <h3 class="job-card__name">{{ $job['name'] }}</h3>
                        <div class="job-card__meta">
                            <span>{{ $job['meta'] }}</span>
                            <span class="job-card__dot" aria-hidden="true"></span>
                            <span>{{ $job['time'] }}</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.user>
