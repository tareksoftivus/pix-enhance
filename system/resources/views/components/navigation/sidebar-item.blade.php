@props([
    'label'      => '',
    'icon'       => 'ph-circle',
    'route'      => '',
    'permission' => null,
    'children'   => [],
])

@php
    // Determine active state for the main item
    $active = $route ? request()->routeIs($route) : false;

    // Determine if any child is active
    $childActive = false;
    if (!empty($children)) {
        foreach ($children as $child) {
            if (isset($child['route']) && request()->routeIs($child['route'])) {
                $childActive = true;
                break;
            }
        }
    }

    // Build the href for items without children
    if ($route && empty($children)) {
        $routeBase = explode('.*', $route)[0];
        $routeName = str_contains($route, '.*') ? $routeBase . '.index' : $routeBase;

        try {
            $href = route($routeName);
        } catch (\Exception $e) {
            $href = '#';
        }
    } else {
        $href = '#';
    }
@endphp

@if($permission)
    @can($permission)
        @if(empty($children))
            {{-- Simple nav item (no submenu) --}}
            <a href="{{ $href }}"
               class="nav-item {{ $active ? 'active' : '' }}"
               aria-label="{{ $label }}">
                <i class="ph {{ $icon }} nav-item-icon"></i>
                <span class="nav-item-text">{{ __($label) }}</span>
            </a>
        @else
            {{-- Nav item with submenu --}}
            <div class="nav-item-wrapper {{ $childActive ? 'expanded' : '' }}">
                <button type="button"
                        class="nav-item w-full {{ $childActive ? 'active' : '' }}"
                        data-action="toggle-submenu"
                        aria-label="{{ $label }}">
                    <i class="ph {{ $icon }} nav-item-icon"></i>
                    <span class="nav-item-text">{{ __($label) }}</span>
                    <i class="ph ph-caret-down submenu-icon ms-auto transition-transform duration-200"></i>
                </button>
                <div class="submenu max-h-0 overflow-hidden transition-all duration-300">
                    <div class="space-y-1.5 pt-3 pb-1 pr-2 pl-4">
                        @foreach($children as $child)
                            @php
                                $childRouteBase = explode('.*', $child['route'] ?? '')[0];
                                $childRouteName = str_contains($child['route'] ?? '', '.*')
                                    ? $childRouteBase . '.index'
                                    : $childRouteBase;

                                try {
                                    $childHref = route($childRouteName);
                                } catch (\Exception $e) {
                                    $childHref = '#';
                                }

                                $isChildActive = isset($child['route']) && request()->routeIs($child['route']);
                            @endphp

                            @if(isset($child['permission']))
                                @can($child['permission'])
                                    <a href="{{ $childHref }}"
                                       class="submenu-item {{ $isChildActive ? 'active' : '' }}">
                                        <i class="ph {{ $child['icon'] ?? 'ph-dot-outline' }} submenu-item-icon"></i>
                                        <span>{{ __($child['label']) }}</span>
                                    </a>
                                @endcan
                            @else
                                <a href="{{ $childHref }}"
                                   class="submenu-item {{ $isChildActive ? 'active' : '' }}">
                                    <i class="ph {{ $child['icon'] ?? 'ph-dot-outline' }} submenu-item-icon"></i>
                                    <span>{{ __($child['label']) }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endcan
@else
    @if(empty($children))
        {{-- Simple nav item (no submenu) --}}
        <a href="{{ $href }}"
           class="nav-item {{ $active ? 'active' : '' }}"
           aria-label="{{ $label }}">
            <i class="ph {{ $icon }} nav-item-icon"></i>
            <span class="nav-item-text">{{ __($label) }}</span>
        </a>
    @else
        {{-- Nav item with submenu --}}
        <div class="nav-item-wrapper {{ $childActive ? 'expanded' : '' }}">
            <button type="button"
                    class="nav-item w-full {{ $childActive ? 'active' : '' }}"
                    data-action="toggle-submenu"
                    aria-label="{{ $label }}">
                <i class="ph {{ $icon }} nav-item-icon"></i>
                <span class="nav-item-text">{{ __($label) }}</span>
                <i class="ph ph-caret-down submenu-icon ms-auto transition-transform duration-200"></i>
            </button>
            <div class="submenu max-h-0 overflow-hidden transition-all duration-300">
                <div class="space-y-1.5 pt-3 pb-1 pr-4 pl-7">
                    @foreach($children as $child)
                        @php
                            $childRouteBase = explode('.*', $child['route'] ?? '')[0];
                            $childRouteName = str_contains($child['route'] ?? '', '.*')
                                ? $childRouteBase . '.index'
                                : $childRouteBase;

                            try {
                                $childHref = route($childRouteName);
                            } catch (\Exception $e) {
                                $childHref = '#';
                            }

                            $isChildActive = isset($child['route']) && request()->routeIs($child['route']);
                        @endphp

                        @if(isset($child['permission']))
                            @can($child['permission'])
                                <a href="{{ $childHref }}"
                                   class="submenu-item {{ $isChildActive ? 'active' : '' }}">
                                    {{ __($child['label']) }}
                                </a>
                            @endcan
                        @else
                            <a href="{{ $childHref }}"
                               class="submenu-item {{ $isChildActive ? 'active' : '' }}">
                                {{ __($child['label']) }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif
