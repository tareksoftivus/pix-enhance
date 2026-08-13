@php
    $viewErrors = $errors ?? new Illuminate\Support\ViewErrorBag;
    $errorTitle = $errorTitle ?? __('Something needs attention');
@endphp

@if(session('success'))
    <div class="alert alert-success">
        <span class="alert__icon" aria-hidden="true"><i data-lucide="circle-check"></i></span>
        <div>
            <p class="alert__title">{{ __('Success') }}</p>
            <p class="alert__text">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('status'))
    <div class="alert alert-info">
        <span class="alert__icon" aria-hidden="true"><i data-lucide="info"></i></span>
        <div>
            <p class="alert__title">{{ __('Notice') }}</p>
            <p class="alert__text">{{ session('status') }}</p>
        </div>
    </div>
@endif

@if(session('error') || $viewErrors->any())
    <div class="alert alert-danger">
        <span class="alert__icon" aria-hidden="true"><i data-lucide="circle-alert"></i></span>
        <div>
            <p class="alert__title">{{ $errorTitle }}</p>
            <p class="alert__text">{{ session('error') ?: $viewErrors->first() }}</p>
        </div>
    </div>
@endif
