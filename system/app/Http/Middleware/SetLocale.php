<?php

namespace App\Http\Middleware;

use App\Modules\Languages\Services\LanguagesService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        $activeLanguages = collect();
        $direction = 'ltr';

        App::setLocale($locale);

        // Try to load languages from DB for switcher UI and direction
        try {
            $languagesService = app(LanguagesService::class);
            $activeLanguages = $languagesService->getActive();
            $currentLanguage = $activeLanguages->firstWhere('code', $locale);
            $direction = $currentLanguage?->direction ?? 'ltr';
        } catch (\Exception $e) {
            // DB not available (pre-migration) — fall back gracefully
        }

        // Share with all views
        View::share('currentLocale', $locale);
        View::share('currentDirection', $direction);
        View::share('activeLanguages', $activeLanguages);

        return $next($request);
    }

    protected function resolveLocale(Request $request): string
    {
        // Priority 1: Session (user explicitly switched)
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // Priority 2: Browser Accept-Language matched against available JSON files
        $availableLocales = $this->getAvailableLocales();
        $browserLocale = $request->getPreferredLanguage($availableLocales);
        if ($browserLocale && in_array($browserLocale, $availableLocales)) {
            return $browserLocale;
        }

        // Fallback to config
        return config('app.locale', 'en');
    }

    /**
     * Check if a locale has a corresponding lang/{locale}.json file.
     */
    protected function isValidLocale(string $locale): bool
    {
        return file_exists(lang_path("{$locale}.json"));
    }

    /**
     * Get available locales by scanning the lang/ directory for JSON files.
     */
    protected function getAvailableLocales(): array
    {
        $files = glob(lang_path('*.json'));

        return array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $files ?: []);
    }
}
