<?php

namespace App\Modules\PaymentGatewaySettings\Services;

use App\Modules\ManualPaymentMethods\Models\ManualPaymentMethod;
use App\Modules\PaymentGatewaySettings\Models\PaymentGatewaySetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PaymentGatewaySettingsService
{
    protected string $configKey = 'payment-gateway-settings';

    protected string $cacheKey = 'payment_gateway_settings_cache';

    protected int $cacheTtl = 86400;

    /**
     * Get a setting value: DB override → config default → fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $dbValues = $this->getAllFromDb();
        $definition = $this->getDefinition($key);

        if (array_key_exists($key, $dbValues)) {
            return $this->castValue($dbValues[$key], $definition['type'] ?? 'text');
        }

        return $definition['default'] ?? $default;
    }

    /**
     * Set a setting value in the database.
     */
    public function set(string $key, mixed $value): void
    {
        $definition = $this->getDefinition($key);
        $type = $definition['type'] ?? 'text';
        $stored = $this->formatForStorage($value, $type);

        PaymentGatewaySetting::updateOrCreate(['key' => $key], ['value' => $stored]);

        $this->clearCache();
    }

    /**
     * Get all groups with settings and current values merged in (for the settings view).
     */
    public function getGroupedDefinitions(): array
    {
        $dbValues = $this->getAllFromDb();
        $groups = config($this->configKey, []);
        $result = [];

        foreach ($groups as $groupKey => $group) {
            $settings = [];

            foreach ($group['settings'] as $key => $definition) {
                $rawValue = $dbValues[$key] ?? null;

                $settings[$key] = array_merge($definition, [
                    'key' => $key,
                    'value' => $rawValue !== null
                        ? $this->castValue($rawValue, $definition['type'] ?? 'text')
                        : ($definition['default'] ?? null),
                ]);
            }

            $result[$groupKey] = [
                'label' => $group['label'] ?? ucfirst($groupKey),
                'icon' => $group['icon'] ?? 'ph ph-credit-card',
                'description' => $group['description'] ?? '',
                'layout' => $group['layout'] ?? '',
                'webhook_url' => $group['webhook_url'] ?? false,
                'settings' => $settings,
            ];
        }

        return $result;
    }

    /**
     * Find a setting's definition from config.
     */
    public function getDefinition(string $key): ?array
    {
        foreach (config($this->configKey, []) as $group) {
            if (isset($group['settings'][$key])) {
                return $group['settings'][$key];
            }
        }

        return null;
    }

    /**
     * Get all DB values as a flat key => value array (public accessor).
     */
    public function getAllValues(): array
    {
        return $this->getAllFromDb();
    }

    /**
     * Get all DB values as a flat key => value array (cached).
     */
    protected function getAllFromDb(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return PaymentGatewaySetting::pluck('value', 'key')->toArray();
        });
    }

    protected function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean', 'feature' => (bool) $value,
            'number', 'integer' => (int) $value,
            'float' => (float) $value,
            'tags' => is_string($value) ? array_filter(explode(',', $value)) : (array) $value,
            default => $value,
        };
    }

    protected function formatForStorage(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean', 'feature' => $value ? '1' : '0',
            'tags' => is_array($value) ? implode(',', array_filter($value)) : (string) $value,
            default => is_array($value) ? implode(',', $value) : (string) $value,
        };
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Calculate the admin-configured surcharge for a gateway on top of a base amount.
     *
     * @return array{fixed: float, percent: float, fee: float, total: float}
     */
    public function feeFor(string $gateway, float $amount): array
    {
        $fixed = (float) $this->get("{$gateway}_fixed_charge", 0);
        $percent = (float) $this->get("{$gateway}_percent_charge", 0);
        $fee = round($fixed + ($amount * $percent / 100), 2);

        return [
            'fixed' => $fixed,
            'percent' => $percent,
            'fee' => $fee,
            'total' => round($amount + $fee, 2),
        ];
    }

    /**
     * Validate an amount against a gateway's admin-configured min/max limits.
     * A limit of 0 means "no limit", matching the admin field hints.
     */
    public function withinLimits(string $gateway, float $amount): bool
    {
        $min = (float) $this->get("{$gateway}_min_amount", 0);
        $max = (float) $this->get("{$gateway}_max_amount", 0);

        if ($min > 0 && $amount < $min) {
            return false;
        }

        return ! ($max > 0 && $amount > $max);
    }

    /**
     * Get a gateway's display label from its config group, or a manual payment
     * method's name, falling back to a title-cased slug.
     */
    public function labelFor(string $gateway): string
    {
        $label = config("{$this->configKey}.{$gateway}.label");

        if ($label) {
            return $label;
        }

        $manual = ManualPaymentMethod::where('slug', $gateway)->first();

        return $manual?->name ?? Str::headline($gateway);
    }

    /**
     * Get a gateway's config-declared icon class, or a manual payment method's icon, if any.
     */
    public function iconFor(string $gateway): ?string
    {
        return config("{$this->configKey}.{$gateway}.icon")
            ?? ManualPaymentMethod::where('slug', $gateway)->value('icon');
    }
}
