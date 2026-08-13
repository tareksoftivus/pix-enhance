<x-layouts.admin :title="__('HomePageSettings')">

    <form method="POST" action="{{ route('admin.home-page-settings.update') }}" id="settingsForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="_active_tab" id="activeTab" value="{{ array_key_first($groups) }}">

        <div class="settings-layout">

            <nav class="settings-nav">
                <div class="settings-nav-header">
                    <h2>{{ __('HomePageSettings') }}</h2>
                    <p>{{ __('Manage your settings') }}</p>
                </div>

                <div class="settings-nav-search">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" placeholder="{{ __('Search settings...') }}" id="settingsNavSearch" autocomplete="off">
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
                        <h1>{{ __('HomePageSettings') }}</h1>
                        <p>{{ __('Configure your home-page-settings settings.') }}</p>
                    </div>

                    @foreach($groups as $groupKey => $group)
                        <div class="settings-section" id="settings-{{ $groupKey }}" data-settings-group="{{ $groupKey }}"@unless($loop->first) style="display:none"@endunless>
                            <div class="section-card">
                                <h4 class="settings-section-title">{{ __($group['label']) }}</h4>
                                @if($group['description'])
                                    <p class="settings-section-desc">{{ __($group['description']) }}</p>
                                @endif

                                @php
                                    $featureSettings = collect($group['settings'])->where('type', 'feature');
                                    $regularSettings = collect($group['settings'])->where('type', '!=', 'feature');
                                @endphp

                                @if($regularSettings->isNotEmpty())
                                <div class="settings-section-body">
                                    @foreach($regularSettings as $key => $setting)
                                        <div class="setting-row" data-setting-label="{{ strtolower(__($setting['label'])) }}">
                                            <div class="setting-label">
                                                <span class="label">{{ __($setting['label']) }}</span>
                                                @if(!empty($setting['hint']))
                                                    <span class="hint">{{ __($setting['hint']) }}</span>
                                                @endif
                                            </div>

                                            <div>
                                                @if($setting['type'] === 'boolean')
                                                    <x-forms.toggle
                                                        name="settings[{{ $key }}]"
                                                        :checked="(bool) $setting['value']"
                                                    />
                                                @elseif($setting['type'] === 'textarea')
                                                    <x-forms.textarea
                                                        name="settings[{{ $key }}]"
                                                        :value="$setting['value']"
                                                        :placeholder="__('Enter') . ' ' . strtolower(__($setting['label']))"
                                                        rows="3"
                                                    />
                                                @elseif($setting['type'] === 'select')
                                                    @php
                                                        $options = $setting['options'] ?? [];
                                                        $searchable = !empty($setting['options_resolver']) || count($options) > 10;
                                                        if (($setting['options_resolver'] ?? null) === 'timezones') {
                                                            $options = collect(timezone_identifiers_list())
                                                                ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                                                ->toArray();
                                                        }
                                                    @endphp
                                                    @if($searchable)
                                                        <x-forms.tom-select name="settings[{{ $key }}]" :selected="$setting['value']">
                                                            @foreach($options as $optValue => $optLabel)
                                                                <option value="{{ $optValue }}" @selected($setting['value'] == $optValue)>{{ __($optLabel) }}</option>
                                                            @endforeach
                                                        </x-forms.tom-select>
                                                    @else
                                                        <x-forms.select name="settings[{{ $key }}]" :value="$setting['value']">
                                                            @foreach($options as $optValue => $optLabel)
                                                                <option value="{{ $optValue }}" @selected($setting['value'] == $optValue)>{{ __($optLabel) }}</option>
                                                            @endforeach
                                                        </x-forms.select>
                                                    @endif
                                                @elseif($setting['type'] === 'media')
                                                    <x-media.picker
                                                        :name="'settings[' . $key . ']'"
                                                        :value="$setting['value']"
                                                        :accept="$setting['accept'] ?? 'image'"
                                                    />
                                                @elseif($setting['type'] === 'color')
                                                    <div class="setting-color-field">
                                                        <input type="color"
                                                               value="{{ $setting['value'] ?? '#000000' }}"
                                                               class="setting-color-swatch"
                                                               oninput="this.nextElementSibling.value = this.value">
                                                        <input type="text"
                                                               name="settings[{{ $key }}]"
                                                               value="{{ $setting['value'] ?? '#000000' }}"
                                                               class="setting-color-hex"
                                                               maxlength="7"
                                                               pattern="^#[0-9A-Fa-f]{6}$"
                                                               oninput="this.previousElementSibling.value = this.value">
                                                    </div>
                                                    @error("settings.{$key}")
                                                        <p class="form-error">{{ $message }}</p>
                                                    @enderror
                                                @else
                                                    <x-forms.input
                                                        name="settings[{{ $key }}]"
                                                        :type="$setting['type']"
                                                        :value="$setting['value']"
                                                        :placeholder="__('Enter') . ' ' . strtolower(__($setting['label']))"
                                                    />
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($featureSettings->isNotEmpty())
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 mt-6">
                                    @foreach($featureSettings as $key => $setting)
                                        @php $isOn = (bool) $setting['value']; @endphp
                                        <div class="config-tile group bg-neutral-0 relative flex flex-col overflow-hidden rounded-2xl border border-neutral-100 p-5 transition-all duration-300 hover:-translate-y-1 hover:border-neutral-200 hover:shadow-xl"
                                             data-state="{{ $isOn ? 'on' : 'off' }}">

                                            <input type="hidden" name="settings[{{ $key }}]" value="{{ $isOn ? '1' : '0' }}" data-feature-input />

                                            <div class="state-strip absolute start-0 top-3.5 bottom-3.5 w-[3.5px] rounded-e transition-all duration-500 {{ $isOn ? 'bg-success opacity-100 shadow-[0_0_8px_var(--color-success)]' : 'bg-error opacity-20' }}"></div>

                                            <p class="mb-1 text-[15px] font-bold text-neutral-950">{{ __($setting['label']) }}</p>

                                            @if(!empty($setting['hint']))
                                                <p class="mb-4 text-[13px] leading-relaxed text-neutral-500">{{ __($setting['hint']) }}</p>
                                            @endif

                                            <div class="mt-auto flex items-center justify-between border-t border-neutral-100 pt-3.5">
                                                <span class="status-text text-xs font-bold tracking-widest uppercase transition-colors duration-300 {{ $isOn ? 'text-success' : 'text-error' }}">{{ $isOn ? __('Enabled') : __('Disabled') }}</span>

                                                <button type="button"
                                                        class="relative flex h-8 w-14 shrink-0 cursor-pointer items-center rounded-full border-[1.5px] p-1 transition-colors duration-300 {{ $isOn ? 'bg-success/10 border-success/30' : 'bg-error/10 border-error/30' }}"
                                                        data-action="toggle-feature">
                                                    <div class="knob absolute flex h-6 w-6 items-center justify-center rounded-full shadow-md transition-all duration-300 {{ $isOn ? 'start-7 bg-success' : 'start-1 bg-error' }}">
                                                        <i class="ph {{ $isOn ? 'ph-check' : 'ph-x' }} text-sm text-white transition-transform duration-300"></i>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif

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

    <button type="button" class="settings-mobile-nav-trigger" id="settingsMobileNavBtn" title="{{ __('Navigate settings') }}">
        <i class="ph ph-gear"></i>
    </button>

    <div class="settings-sheet" id="settingsSheet">
        <div class="settings-sheet-handle"><span></span></div>
        <div class="settings-sheet-header">
            <h3>{{ __('HomePageSettings') }}</h3>
            <p>{{ __('Manage your settings') }}</p>
        </div>
        <div class="settings-sheet-search">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" placeholder="{{ __('Search settings...') }}" id="settingsSheetSearch" autocomplete="off">
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
