<?php

namespace App\Modules\Frontend\Services;

use App\Modules\Frontend\Models\Page;
use App\Modules\Frontend\Models\PageSection;

class PageComposerService
{
    public function __construct(
        protected ThemeRegistry $themes
    ) {}

    public function syncSections(Page $page, array $sections): void
    {
        $page->pageSections()->delete();

        foreach (array_values($sections) as $index => $sectionId) {
            if (! $sectionId) {
                continue;
            }

            PageSection::create([
                'page_id' => $page->id,
                'frontend_section_id' => (int) $sectionId,
                'sort_order' => $index,
                'visibility_rules' => null,
            ]);
        }
    }

    public function compatibleThemes(Page $page): array
    {
        $sectionTypes = $page->sections->pluck('type')->filter()->unique()->values()->all();
        $compatible = [];

        foreach ($this->themes->all() as $themeKey => $theme) {
            $supportsAll = true;

            foreach ($sectionTypes as $type) {
                if (! $this->themes->supportsSection($themeKey, $type)) {
                    $supportsAll = false;
                    break;
                }
            }

            if ($supportsAll) {
                $compatible[] = $themeKey;
            }
        }

        return $compatible;
    }
}
