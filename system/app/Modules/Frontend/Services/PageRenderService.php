<?php

namespace App\Modules\Frontend\Services;

use App\Modules\Frontend\Models\Page;

class PageRenderService
{
    public function __construct(
        protected ThemeRenderService $themeRender,
        protected ThemeRegistry $themes,
        protected MenuRenderService $menus
    ) {}

    public function payload(Page $page, string $themeKey): array
    {
        $page->loadMissing(['sections', 'pageSections']);

        $resolvedSections = [];

        foreach ($page->sections as $section) {
            $resolvedSections[] = array_merge(
                $this->themeRender->sectionView($themeKey, $section),
                ['section' => $section]
            );
        }

        return [
            'themeKey' => $themeKey,
            'theme' => $this->themes->get($themeKey),
            'themeVars' => $this->themeRender->themeVariables($themeKey),
            'layoutView' => $this->themeRender->layoutView($themeKey, $page->default_layout),
            'page' => $page,
            'resolvedMenus' => $this->menus->resolveForTheme($themeKey),
            'resolvedSections' => $resolvedSections,
        ];
    }
}
