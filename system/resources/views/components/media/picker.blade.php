@props(['name', 'value' => null, 'label' => '', 'accept' => 'image', 'hint' => ''])

@php
    $media = $value ? \App\Modules\Media\Models\Media::find($value) : null;
@endphp

<div class="media-picker" data-media-picker data-media-accept="{{ $accept }}">
    @if($label)
        <div class="media-picker-header">
            <p class="form-label">{{ $label }}</p>
        </div>
    @endif

    {{-- Hidden Input --}}
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-media-picker-input>

    {{-- Dropzone card: click opens the media library, dropping a file uploads it --}}
    <div class="media-picker-dropzone" data-media-picker-trigger data-media-picker-dropzone>
        {{-- Current selection preview --}}
        <div class="media-picker-preview" data-media-picker-preview>
            @if($media)
                @if($media->isImage())
                    <img src="{{ $media->url }}" alt="{{ $media->alt ?? $media->name }}">
                @else
                    <div class="media-picker-file-icon">
                        <i class="ph ph-file-text"></i>
                        <span>{{ $media->original_name }}</span>
                    </div>
                @endif
            @endif
        </div>

        {{-- Empty placeholder (shown when no media is selected) --}}
        <div class="media-picker-placeholder" data-media-picker-placeholder @if($media) hidden @endif>
            <i class="ph ph-image"></i>
        </div>

        <div class="media-picker-dropzone-text">
            <p class="media-picker-cta" data-media-picker-cta>{{ __('Click or drop files') }}</p>
            @if($hint)
                <p class="media-picker-hint">{{ $hint }}</p>
            @endif
        </div>

        {{-- Clear selection --}}
        <button type="button" class="media-picker-remove" data-media-picker-remove title="{{ __('Remove') }}" @if(!$media) hidden @endif>
            <i class="ph ph-x"></i>
        </button>
    </div>

    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
