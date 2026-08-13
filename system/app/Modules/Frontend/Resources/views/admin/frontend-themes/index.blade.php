<x-layouts.admin :title="__('Frontend Themes')">
    <form method="POST" action="{{ route('admin.frontend-themes.update') }}" id="settingsForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="_active_tab" id="activeTab" value="{{ array_key_first($groups) }}">

        <div class="settings-layout">
            <nav class="settings-nav">
                <div class="settings-nav-header">
                    <h2>{{ __('Frontend Themes') }}</h2>
                    <p>{{ __('Manage active themes and theme-specific settings') }}</p>
                </div>

                <div class="settings-nav-search">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" placeholder="{{ __('Search themes...') }}" id="settingsNavSearch" autocomplete="off">
                </div>

                <div class="settings-nav-group" id="settingsNavList">
                    @foreach($groups as $groupKey => $group)
                        <a href="#"
                           class="settings-nav-item{{ $loop->first ? ' active' : '' }}"
                           data-settings-nav="{{ $groupKey }}"
                           data-search-label="{{ strtolower(__($group['label'])) }}">
                            <i class="{{ $group['icon'] }}"></i>
                            {{ __($group['label']) }}
                        </a>
                    @endforeach
                </div>

            </nav>

            <div class="settings-content">
                <div class="settings-content-scroll" id="settingsScroll">
                    <div class="settings-page-header">
                        <h1>{{ __('Frontend Themes') }}</h1>
                        <p>{{ __('Keep themes code-defined, switch active rendering safely, and manage theme-scoped settings without touching shared content.') }}</p>
                    </div>

                    @foreach($groups as $groupKey => $group)
                        <div class="settings-section" id="settings-{{ $groupKey }}" data-settings-group="{{ $groupKey }}"@unless($loop->first) style="display:none"@endunless>
                            <div class="section-card">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div>
                                        <h4 class="settings-section-title">{{ __($group['label']) }}</h4>
                                        @if(!empty($group['description']))
                                            <p class="settings-section-desc">{{ __($group['description']) }}</p>
                                        @endif
                                    </div>

                                    @if(!empty($group['theme']))
                                        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-600 lg:w-[320px]">
                                            <div class="mb-2 flex items-center gap-2 font-semibold text-neutral-900">
                                                <i class="ph ph-layout"></i>
                                                {{ __('Theme Contract') }}
                                            </div>
                                            <p>{{ __('Supported sections') }}: {{ implode(', ', array_map(fn ($type) => config('frontend-sections.' . $type . '.label', $type), $group['theme']['supported_section_types'] ?? [])) }}</p>
                                            <p class="mt-1">{{ __('Layouts') }}: {{ implode(', ', array_map(fn ($layout) => $layout['label'], $group['theme']['page_layouts'] ?? [])) }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="settings-section-body mt-6">
                                    @foreach($group['settings'] as $settingKey => $setting)
                                        @php
                                            $segments = explode('.', $settingKey);
                                            $inputName = 'settings';
                                            foreach ($segments as $segment) {
                                                $inputName .= '[' . $segment . ']';
                                            }
                                            $errorKey = 'settings.' . $settingKey;
                                        @endphp
                                        <div class="setting-row">
                                            <div class="setting-label">
                                                <span class="label">{{ __($setting['label']) }}</span>
                                                @if(!empty($setting['hint']))
                                                    <span class="hint">{{ __($setting['hint']) }}</span>
                                                @endif
                                            </div>

                                            <div>
                                                <x-forms.schema-field :field="$setting" :name="$inputName" :value="$setting['value'] ?? null" :error-key="$errorKey" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="settings-footer">
                    <x-forms.submit :label="__('Save Changes')" />
                    <x-ui.button type="button" variant="ghost" onclick="window.location.reload()">
                        <i class="ph ph-arrow-counter-clockwise"></i> {{ __('Reset') }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    </form>

    <div class="settings-sheet-overlay" id="settingsSheetOverlay"></div>
    <button type="button" class="settings-mobile-nav-trigger" id="settingsMobileNavBtn" title="{{ __('Navigate themes') }}">
        <i class="ph ph-paint-brush"></i>
    </button>
    <div class="settings-sheet" id="settingsSheet">
        <div class="settings-sheet-handle"><span></span></div>
        <div class="settings-sheet-header">
            <h3>{{ __('Frontend Themes') }}</h3>
            <p>{{ __('Manage theme configuration') }}</p>
        </div>
        <div class="settings-sheet-search">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" placeholder="{{ __('Search themes...') }}" id="settingsSheetSearch" autocomplete="off">
        </div>
        <div class="settings-sheet-list" id="settingsSheetList">
            @foreach($groups as $groupKey => $group)
                <a href="#"
                   class="settings-sheet-item{{ $loop->first ? ' active' : '' }}"
                   data-sheet-nav="{{ $groupKey }}"
                   data-search-label="{{ strtolower(__($group['label'])) }}">
                    <i class="{{ $group['icon'] }}"></i>
                    {{ __($group['label']) }}
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
