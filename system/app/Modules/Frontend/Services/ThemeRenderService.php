<?php

namespace App\Modules\Frontend\Services;

use App\Modules\Frontend\Models\FrontendSection;
use InvalidArgumentException;

class ThemeRenderService
{
    public function __construct(
        protected ThemeRegistry $themes,
        protected ThemeSettingsService $themeSettings
    ) {}

    public function layoutView(string $themeKey, ?string $layoutKey): string
    {
        $theme = $this->themes->get($themeKey);

        if (! $theme) {
            throw new InvalidArgumentException("Unknown theme [{$themeKey}]");
        }

        $layoutKey = $layoutKey ?: $this->themes->defaultLayoutKey($themeKey);
        $layout = $theme['page_layouts'][$layoutKey] ?? null;

        if ($layout) {
            $candidate = $theme['view_namespace'].'.'.$layout['view'];

            if (view()->exists($candidate)) {
                return $candidate;
            }
        }

        $defaultLayoutKey = $this->themes->defaultLayoutKey($themeKey);
        $defaultLayout = $theme['page_layouts'][$defaultLayoutKey] ?? null;
        $fallback = $defaultLayout ? $theme['view_namespace'].'.'.$defaultLayout['view'] : 'frontend.shared.layouts.page';

        return view()->exists($fallback) ? $fallback : 'frontend.shared.layouts.page';
    }

    public function sectionView(string $themeKey, FrontendSection $section): array
    {
        $theme = $this->themes->get($themeKey);
        $supported = $this->themes->supportsSection($themeKey, $section->type);

        if ($theme && $supported) {
            $themeSpecific = $theme['view_namespace'].'.sections.'.$section->type;

            if (view()->exists($themeSpecific)) {
                return ['view' => $themeSpecific, 'supported' => true];
            }

            $sharedView = 'frontend.shared.sections.'.$section->type;

            if (view()->exists($sharedView)) {
                return ['view' => $sharedView, 'supported' => true];
            }
        }

        return [
            'view' => $theme['fallback_renderer'] ?? 'frontend.shared.sections.unsupported',
            'supported' => false,
        ];
    }

    public function themeVariables(string $themeKey): array
    {
        return [
            'logo_text' => (string) $this->themeSettings->getThemeSetting($themeKey, 'logo_text', $this->themes->get($themeKey)['label'] ?? ucfirst($themeKey)),
            'primary_color' => (string) $this->themeSettings->getThemeSetting($themeKey, 'primary_color', '#111827'),
            'accent_color' => (string) $this->themeSettings->getThemeSetting($themeKey, 'accent_color', '#1f2937'),
            'show_hero_kicker' => (bool) $this->themeSettings->getThemeSetting($themeKey, 'show_hero_kicker', true),
            'uppercase_headings' => (bool) $this->themeSettings->getThemeSetting($themeKey, 'uppercase_headings', false),
        ];
    }
}
