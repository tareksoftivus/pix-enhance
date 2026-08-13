<x-layouts.admin :title="__('Settings')">

    <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="_active_tab" id="activeTab" value="{{ array_key_first($groups) }}">

        <div class="settings-layout">

            {{-- ── Sidebar Navigation ── --}}
            <nav class="settings-nav">
                <div class="settings-nav-header">
                    <h2>{{ __('Settings') }}</h2>
                    <p>{{ __('Manage your workspace') }}</p>
                </div>

                {{-- Search --}}
                <div class="settings-nav-search">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" placeholder="{{ __('Search settings...') }}" id="settingsNavSearch" autocomplete="off">
                </div>

                {{-- Nav Items --}}
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

            {{-- ── Main Content ── --}}
            <div class="settings-content">

                {{-- Scrollable Area --}}
                <div class="settings-content-scroll" id="settingsScroll">

                    {{-- Page Header --}}
                    <div class="settings-page-header">
                        <h1>{{ __('Settings') }}</h1>
                        <p>{{ __('Configure core system settings, feature flags, and email configuration for your workspace.') }}</p>
                    </div>

                    {{-- Sections (tab panels — only first visible) --}}
                    @foreach($groups as $groupKey => $group)
                        @php
                            $allSettings = collect($group['settings'] ?? []);
                            $hasCardGroups = !empty($group['card_groups']);
                            $sectionClass = ($group['layout'] ?? '') === 'full' ? 'settings-section-full' : 'settings-section';
                        @endphp
                        <div class="{{ $sectionClass }}" id="settings-{{ $groupKey }}" data-settings-group="{{ $groupKey }}"@unless($loop->first) style="display:none"@endunless>
                            @if($hasCardGroups)
                                @php
                                    $cardGroups = [];
                                    foreach ($allSettings as $key => $setting) {
                                        $cgLabel = $setting['card_group']['label'] ?? 'Other';
                                        if (!isset($cardGroups[$cgLabel])) {
                                            $cardGroups[$cgLabel] = [
                                                'label' => $cgLabel,
                                                'icon' => $setting['card_group']['icon'] ?? 'ph ph-gear',
                                                'color' => $setting['card_group']['color'] ?? null,
                                                'description' => $setting['card_group']['description'] ?? '',
                                                'column' => $setting['card_group']['column'] ?? null,
                                                'order' => count($cardGroups),
                                                'fields' => [],
                                            ];
                                        }
                                        $cardGroups[$cgLabel]['fields'][$key] = $setting;
                                    }

                                    $cardGroups = array_values($cardGroups);

                                    // Cards may pin themselves via card_group.column (left/right);
                                    // the rest are split evenly, filling the left column first so
                                    // an odd count leaves the extra card on the left. Config order
                                    // is preserved within each column.
                                    $leftCards = [];
                                    $rightCards = [];
                                    $autoCards = [];
                                    foreach ($cardGroups as $card) {
                                        match ($card['column']) {
                                            'left' => $leftCards[] = $card,
                                            'right' => $rightCards[] = $card,
                                            default => $autoCards[] = $card,
                                        };
                                    }

                                    $autoLeftCount = (int) ceil(count($autoCards) / 2);
                                    $leftCards = array_merge($leftCards, array_slice($autoCards, 0, $autoLeftCount));
                                    $rightCards = array_merge($rightCards, array_slice($autoCards, $autoLeftCount));

                                    usort($leftCards, fn (array $a, array $b) => $a['order'] <=> $b['order']);
                                    usort($rightCards, fn (array $a, array $b) => $a['order'] <=> $b['order']);

                                    $cardColumns = [$leftCards, $rightCards];
                                @endphp

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                    @foreach($cardColumns as $column)
                                        <div class="space-y-6">
                                            @foreach($column as $card)
                                                <div class="section-card">
                                                    <div class="flex items-center gap-3 mb-1">
                                                        <div class="flex items-center justify-center w-9 h-9 rounded-lg {{ $card['color'] ? '' : 'bg-primary/10 text-primary' }}"
                                                             @if($card['color']) style="color: {{ $card['color'] }}; background-color: color-mix(in srgb, {{ $card['color'] }} 10%, transparent);" @endif>
                                                            <i class="{{ $card['icon'] }} text-lg"></i>
                                                        </div>
                                                        <div>
                                                            <h5 class="text-sm font-semibold text-neutral-900 dark:text-neutral-800">{{ __($card['label']) }}</h5>
                                                            @if($card['description'])
                                                                <p class="text-xs text-neutral-500">{{ __($card['description']) }}</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="space-y-4 mt-4">
                                                        @foreach($card['fields'] as $key => $setting)
                                                            @php $visibleIf = $setting['visible_if'] ?? null; @endphp
                                                            <div data-setting-key="{{ $key }}" @if($visibleIf) data-visible-if='@json($visibleIf)' @endif>
                                                                @if(in_array($setting['type'], ['boolean', 'feature']))
                                                                    <div class="flex items-center justify-between gap-4 rounded-xl border border-neutral-100 px-4 py-3">
                                                                        <div>
                                                                            <p class="text-sm font-semibold text-neutral-900">{{ __($setting['label']) }}</p>
                                                                            @if(!empty($setting['hint']))
                                                                                <p class="mt-1 text-xs text-neutral-500">{{ __($setting['hint']) }}</p>
                                                                            @endif
                                                                        </div>
                                                                        <x-forms.toggle
                                                                            name="settings[{{ $key }}]"
                                                                            :checked="(bool) $setting['value']"
                                                                        />
                                                                    </div>
                                                                @elseif($setting['type'] === 'select')
                                                                    <x-forms.select :label="__($setting['label'])"
                                                                        name="settings[{{ $key }}]"
                                                                        :value="$setting['value']">
                                                                        @foreach($setting['options'] ?? [] as $optValue => $optLabel)
                                                                            <option value="{{ $optValue }}" @selected($setting['value'] == $optValue)>{{ __($optLabel) }}</option>
                                                                        @endforeach
                                                                    </x-forms.select>
                                                                @elseif($setting['type'] === 'textarea')
                                                                    <x-forms.textarea :label="__($setting['label'])"
                                                                        name="settings[{{ $key }}]"
                                                                        :value="$setting['value']"
                                                                        :placeholder="$setting['hint'] ?? __('Enter') . ' ' . strtolower(__($setting['label']))"
                                                                        rows="4" />
                                                                @else
                                                                    <x-forms.input :label="__($setting['label'])"
                                                                        name="settings[{{ $key }}]"
                                                                        :type="$setting['type']"
                                                                        :value="$setting['value']"
                                                                        :placeholder="$setting['hint'] ?? __('Enter') . ' ' . strtolower(__($setting['label']))" />
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @else
                            <div class="section-card">
                                <h4 class="settings-section-title">{{ __($group['label']) }}</h4>
                                @if($group['description'])
                                    <p class="settings-section-desc">{{ __($group['description']) }}</p>
                                @endif

                                @php
                                    $featureSettings = $allSettings->where('type', 'feature');
                                    $regularSettings = $allSettings->where('type', '!=', 'feature');
                                @endphp

                                {{-- Regular Settings --}}
                                @if($regularSettings->isNotEmpty())
                                    <div class="settings-section-body">
                                        @foreach($regularSettings as $key => $setting)
                                        @php $visibleIf = $setting['visible_if'] ?? null; @endphp
                                        <div class="{{ in_array(($setting['type'] ?? ''), ['editor', 'tile_select']) ? 'setting-row-full' : 'setting-row' }}" data-setting-key="{{ $key }}" data-setting-label="{{ strtolower(__($setting['label'])) }}" @if($visibleIf) data-visible-if='@json($visibleIf)' @endif>
                                            @unless($setting['type'] === 'tile_select')
                                            <div class="setting-label">
                                                <span class="label">{{ __($setting['label']) }}</span>
                                                @if(!empty($setting['hint']))
                                                    <span class="hint">{{ __($setting['hint']) }}</span>
                                                @endif
                                            </div>
                                            @endunless

                                            <div>
                                                @if($setting['type'] === 'tile_select')
                                                    @php $tiles = $setting['tile_options'] ?? []; @endphp
                                                    <div class="tile-select" data-tile-select>
                                                        <input type="hidden" name="settings[{{ $key }}]" value="{{ $setting['value'] }}" data-tile-select-input>
                                                        @foreach($setting['options'] ?? [] as $optValue => $optLabel)
                                                            @php $tile = $tiles[$optValue] ?? []; @endphp
                                                            <button type="button"
                                                                    class="tile-select-option{{ (string) $setting['value'] === (string) $optValue ? ' active' : '' }}"
                                                                    data-tile-select-option
                                                                    data-value="{{ $optValue }}"
                                                                    @if(!empty($tile['color'])) style="--tile-color: {{ $tile['color'] }};" @endif>
                                                                <span class="tile-select-icon">
                                                                    <i class="{{ $tile['icon'] ?? 'ph ph-circle' }}"></i>
                                                                </span>
                                                                <span class="tile-select-body">
                                                                    <span class="tile-select-label">{{ __($tile['label'] ?? $optLabel) }}</span>
                                                                    @if(!empty($tile['description']))
                                                                        <span class="tile-select-desc">{{ __($tile['description']) }}</span>
                                                                    @endif
                                                                </span>
                                                                <span class="tile-select-check"><i class="ph ph-check-circle"></i></span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                    @error("settings.{$key}")
                                                        <p class="form-error">{{ $message }}</p>
                                                    @enderror
                                                @elseif($setting['type'] === 'boolean')
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
                                                @elseif($setting['type'] === 'checkbox')
                                                    <x-forms.checkbox-group
                                                        name="settings[{{ $key }}]"
                                                        :options="$setting['options'] ?? []"
                                                        :selected="is_array($setting['value']) ? $setting['value'] : explode(',', $setting['value'] ?? '')"
                                                        :columns="$setting['columns'] ?? 2"
                                                    />
                                                @elseif($setting['type'] === 'tags')
                                                    <x-forms.tom-select name="settings[{{ $key }}][]" :selected="$setting['value']" multiple>
                                                        @foreach($setting['options'] ?? [] as $optValue => $optLabel)
                                                            <option value="{{ $optValue }}" @selected(in_array((string) $optValue, is_array($setting['value']) ? $setting['value'] : explode(',', $setting['value'] ?? '')))>{{ __($optLabel) }}</option>
                                                        @endforeach
                                                    </x-forms.tom-select>
                                                @elseif(in_array($setting['type'], ['date', 'date_range', 'datetime', 'time']))
                                                    @php
                                                        $pickerMode = match($setting['type']) {
                                                            'date_range' => 'range',
                                                            'datetime'   => 'datetime',
                                                            'time'       => 'time',
                                                            default      => 'date',
                                                        };
                                                    @endphp
                                                    <x-forms.datepicker
                                                        name="settings[{{ $key }}]"
                                                        :value="$setting['value']"
                                                        :mode="$pickerMode"
                                                    />
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

                                {{-- Feature Switch Tiles --}}
                                @if($featureSettings->isNotEmpty())
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 mt-6">
                                    @foreach($featureSettings as $key => $setting)
                                        @php
                                            $isOn = (bool) $setting['value'];
                                            $visibleIf = $setting['visible_if'] ?? null;
                                        @endphp
                                        <div class="config-tile group bg-neutral-0 relative flex items-start justify-between gap-4 rounded-2xl border border-neutral-100 p-5 transition-colors duration-300 hover:border-neutral-200"
                                             data-setting-key="{{ $key }}"
                                             data-state="{{ $isOn ? 'on' : 'off' }}"
                                             @if($visibleIf) data-visible-if='@json($visibleIf)' @endif>

                                            <div class="flex min-w-0 items-start gap-3">
                                                @if(!empty($setting['icon']))
                                                    @php $tileColor = $setting['color'] ?? 'primary'; @endphp
                                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-{{ $tileColor }}/10 text-{{ $tileColor }}">
                                                        <i class="{{ $setting['icon'] }} text-lg"></i>
                                                    </span>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="text-[15px] font-bold text-neutral-950">{{ __($setting['label']) }}</p>
                                                    @if(!empty($setting['hint']))
                                                        <p class="mt-1 line-clamp-2 text-[13px] leading-relaxed text-neutral-500" title="{{ __($setting['hint']) }}">{{ __($setting['hint']) }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <x-forms.toggle name="settings[{{ $key }}]" :checked="$isOn" class="shrink-0" />
                                        </div>
                                    @endforeach
                                </div>
                                @endif

                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Sticky Footer Bar --}}
                <div class="settings-footer">
                    <x-forms.submit :label="__('Save Changes')" />
                    <x-ui.button type="button" variant="ghost" onclick="window.location.reload()">
                        <i class="ph ph-arrow-counter-clockwise"></i> {{ __('Reset') }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Mobile Bottom Sheet ── --}}
    <div class="settings-sheet-overlay" id="settingsSheetOverlay"></div>

    <button type="button" class="settings-mobile-nav-trigger" id="settingsMobileNavBtn" title="{{ __('Navigate settings') }}">
        <i class="ph ph-gear"></i>
    </button>

    <div class="settings-sheet" id="settingsSheet">
        <div class="settings-sheet-handle"><span></span></div>
        <div class="settings-sheet-header">
            <h3>{{ __('Settings') }}</h3>
            <p>{{ __('Manage your workspace') }}</p>
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
