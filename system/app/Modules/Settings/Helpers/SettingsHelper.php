<?php

use App\Modules\Settings\Services\SettingsService;

if (!function_exists('setting')) {
    /**
     * Get a setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date using the system date_format setting.
     *
     * @param  \DateTimeInterface|string|null  $date
     * @param  bool  $withTime  Append time (h:i A) after the date format
     * @return string
     */
    function format_date(\DateTimeInterface|string|null $date, bool $withTime = false): string
    {
        if (!$date) {
            return '';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        $format = setting('date_format', 'd M, Y');

        if ($withTime) {
            $format .= ' \a\t h:i A';
        }

        return $date->format($format);
    }
}
