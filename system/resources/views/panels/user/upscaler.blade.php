<x-layouts.user :title="__('Image Upscaler')" :search-placeholder="__('Search upscaled images')">
    @php
        $sample = fn (string $file) => asset("assets/frontend/enhance/img/samples/{$file}");
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Image Upscaler') }}</h1>
            <p class="dash__subtitle">
                {{ __('Enlarge up to 16× with reconstructed texture — not stretched pixels.') }}
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

    <div class="studio"
         x-data="enhanceStudio({
            endpoint: @js(route('user.render-jobs.store')),
            tool: 'upscaler',
            scale: '8',
            acceptedTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
            stages: [
                'Reading the source',
                'Removing compression artefacts',
                'Reconstructing texture',
                'Upscaling tiles',
                'Stitching and writing the output'
            ]
         })">
        <section class="studio__stage" aria-label="{{ __('Preview') }}">
            <input class="sr-only" type="file" id="studio-file" name="source"
                   accept="image/jpeg,image/png,image/webp,image/avif"
                   @change="onChange($event)">

            <div class="studio__bar">
                <span class="studio__file" x-show="status !== 'empty'" x-cloak>
                    <i data-lucide="image"></i>
                    <span class="studio__file-name" x-text="source.name">valley-ridge.jpg</span>
                </span>

                <span class="studio__file-meta" x-show="status !== 'empty'" x-cloak
                      x-text="source.meta">3.1 MB · 1024 × 768</span>

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
                    <span class="dropzone__title">{{ __('Drop an image to upscale') }}</span>
                    <span class="dropzone__text">{{ __('JPG, PNG, WEBP or AVIF up to 50 MB') }}</span>

                    <span class="format-list" aria-hidden="true">
                        <span class="format-pill">jpg</span>
                        <span class="format-pill">png</span>
                        <span class="format-pill">webp</span>
                        <span class="format-pill">avif</span>
                    </span>
                </label>

                <p class="field__error" x-show="error" x-cloak>
                    <i data-lucide="circle-alert"></i>
                    <span x-text="error"></span>
                </p>

                <div class="studio__running" x-show="status === 'running'" x-cloak>
                    <div class="studio__running-preview">
                        <img :src="source.url" alt="" width="960" height="720">
                        <span class="scanline" aria-hidden="true"></span>
                    </div>

                    <p class="studio__stage-name" x-text="stage" aria-live="polite">{{ __('Reading the source') }}</p>

                    <div class="studio__progress">
                        <div class="progress">
                            <div class="progress__bar progress__bar-striped"
                                 x-effect="$el.style.setProperty('--progress', progress + '%')"></div>
                        </div>
                        <div class="studio__progress-meta">
                            <span x-text="`${Math.round(progress)}%`">0%</span>
                            <span x-text="outputSize">8192 × 6144</span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline btn-sm" @click="cancel()">
                        {{ __('Cancel') }}
                    </button>
                </div>

                <div class="studio__preview" x-show="status === 'ready'" x-cloak>
                    <img :src="source.url" alt="{{ __('The image waiting to be upscaled') }}" decoding="async">
                    <p class="studio__preview-hint">
                        <i data-lucide="sparkles"></i>
                        {{ __('Ready — pick your settings and hit Enhance.') }}
                    </p>
                </div>

                <div class="studio__result" x-show="status === 'done'" x-cloak>
                    <div class="compare" data-compare data-compare-start="52">
                        <div class="compare__frame">
                            <img class="compare__layer" x-ref="sourceLayer"
                                 src="{{ $sample('valley-before.webp') }}"
                                 :src="source.url || $el.src"
                                 alt="{{ __('Mountain valley at low resolution, soft and flat in colour') }}"
                                 width="1200" height="900" decoding="async">
                            <img class="compare__layer compare__layer-after"
                                 src="{{ $sample('valley-after.webp') }}"
                                 :src="result.url || $el.src"
                                 alt="{{ __('The same valley after 8x upscaling, with recovered detail') }}"
                                 width="1200" height="900" decoding="async">
                        </div>

                        <label class="sr-only" for="upscaler-compare">{{ __('Reveal the processed image') }}</label>
                        <input class="compare__range" type="range" id="upscaler-compare" data-compare-range
                               min="0" max="100" value="52" step="0.1"
                               aria-label="{{ __('Compare the original and the result') }}">

                        <span class="compare__tag compare__tag-before">{{ __('Original') }}</span>
                        <span class="compare__tag compare__tag-after">
                            <i data-lucide="sparkles"></i>
                            {{ __('Upscaled') }}
                        </span>

                        <span class="compare__meta compare__meta-before">1024 × 768</span>
                        <span class="compare__meta compare__meta-after" x-text="outputSize">8192 × 6144</span>

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
                    <span>{{ __('Output') }} <strong x-text="outputSize">8192 × 6144</strong></span>
                    <span>{{ __('Format') }} <strong x-text="format.toUpperCase()">PNG</strong></span>
                    <span x-show="status === 'done' && renderedIn" x-cloak>{{ __('Rendered in') }} <strong x-text="renderedIn">0s</strong></span>
                </div>

                <div class="studio__foot-actions">
                    <button type="button" class="btn btn-outline btn-sm"
                            :class="!canDownload && 'is-disabled'" :aria-disabled="!canDownload" :disabled="!canDownload"
                            @click="shareResult()">
                        <i data-lucide="share-2"></i>
                        {{ __('Share') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm"
                            :class="!canDownload && 'is-disabled'" :aria-disabled="!canDownload" :disabled="!canDownload"
                            @click="downloadResult()"
                            data-ripple>
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
                    <i data-lucide="maximize-2"></i>
                    {{ __('Upscale') }}
                </span>
                <span class="badge badge-sm badge-primary" x-text="model === 'auto' ? '{{ __('Auto') }}' : model">{{ __('Auto') }}</span>
            </div>

            <div class="control-stack">
                <label class="control-row__label" for="up-model">
                    <i data-lucide="cpu"></i>
                    {{ __('Model') }}
                </label>
                <select class="select" id="up-model" name="model" x-model="model">
                    <option value="auto">{{ __('Auto — choose for this image') }}</option>
                    @foreach ($imageModels ?? [] as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="control-row">
                <span class="control-row__label">
                    <i data-lucide="maximize-2"></i>
                    {{ __('Scale') }}
                </span>
                <span class="radio-group">
                    @foreach (['2', '4', '8', '16'] as $scale)
                        <span class="radio-group__option">
                            <input class="radio-group__input" type="radio" id="up-scale-{{ $scale }}" name="scale" value="{{ $scale }}" x-model="scale">
                            <label class="radio-group__label" for="up-scale-{{ $scale }}">{{ $scale }}×</label>
                        </span>
                    @endforeach
                </span>
            </div>

            <div class="control-stack">
                <span class="cluster cluster-between">
                    <label class="control-row__label" for="up-detail">
                        <i data-lucide="focus"></i>
                        {{ __('Detail strength') }}
                    </label>
                    <span class="control-row__value" x-text="`${detail}%`">72%</span>
                </span>
                <input class="range" type="range" id="up-detail" name="detail" min="0" max="100" x-model="detail">
            </div>

            <div class="control-row">
                <label class="control-row__label" for="up-denoise">
                    <i data-lucide="eraser"></i>
                    {{ __('Denoise first') }}
                </label>
                <span class="switch-field">
                    <input class="switch-field__input" type="checkbox" id="up-denoise" name="denoise" x-model="denoise">
                    <span class="switch-field__track"></span>
                </span>
            </div>

            <div class="control-row">
                <label class="control-row__label" for="up-face">
                    <i data-lucide="scan-face"></i>
                    {{ __('Face restoration') }}
                </label>
                <span class="switch-field">
                    <input class="switch-field__input" type="checkbox" id="up-face" name="face" x-model="face">
                    <span class="switch-field__track"></span>
                </span>
            </div>

            <div class="control-row">
                <label class="control-row__label" for="up-format">
                    <i data-lucide="file-text"></i>
                    {{ __('Output') }}
                </label>
                <select class="select input-sm" id="up-format" name="format" x-model="format">
                    <option value="png">PNG</option>
                    <option value="jpg">JPG</option>
                    <option value="webp">WEBP</option>
                </select>
            </div>

            <div class="studio__cost">
                <span>{{ __('This render') }}</span>
                <strong>
                    <i data-lucide="coins"></i>
                    <span x-text="`${cost} credit${cost === 1 ? '' : 's'}`">4 credits</span>
                </strong>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block studio__rail-submit"
                    :class="(status === 'empty' || busy) && 'is-disabled'"
                    :aria-disabled="status === 'empty' || busy" data-ripple>
                <i data-lucide="maximize-2"></i>
                <span x-text="busy ? '{{ __('Upscaling...') }}' : '{{ __('Upscale image') }}'">{{ __('Upscale image') }}</span>
            </button>
        </form>
    </div>

    <section class="mt-xl" aria-labelledby="tool-recent-title">
        <div class="dash__section-head">
            <h2 class="dash__section-title" id="tool-recent-title">
                <i data-lucide="clock"></i>
                {{ __('Recent upscales') }}
            </h2>

            <a class="btn-link btn-link-sm" href="{{ route('user.projects', ['tool' => 'upscaler']) }}">
                {{ __('View all') }}
                <i data-lucide="arrow-right"></i>
            </a>
        </div>

        <div class="job-grid">
            @forelse ($recentEnhancements ?? collect() as $project)
                @include('panels.user.partials.project-card', ['project' => $project, 'statusMeta' => \App\Modules\RenderJobs\Models\RenderJob::statuses()])
            @empty
                <div class="empty-state">
                    <span class="empty-state__icon" aria-hidden="true"><i data-lucide="maximize-2"></i></span>
                    <h3>{{ __('No upscales yet') }}</h3>
                    <p>{{ __('Upload an image above to save your first upscale.') }}</p>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.user>
