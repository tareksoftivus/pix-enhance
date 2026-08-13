<?php

namespace App\Modules\AiSettings\Services;

use App\Modules\AiSettings\Models\AiSetting;
use Illuminate\Support\Facades\Cache;

class AiSettingsService
{
    protected string $configKey = 'ai-settings';

    protected string $cacheKey = 'ai_settings_cache';

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

        AiSetting::updateOrCreate(['key' => $key], ['value' => $stored]);

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
                'icon' => $group['icon'] ?? 'ph ph-robot',
                'description' => $group['description'] ?? '',
                'layout' => $group['layout'] ?? '',
                'card_groups' => $group['card_groups'] ?? false,
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
     * Build the provider config array for the Laravel AI SDK.
     *
     * Reads enabled providers from DB settings and returns an array
     * in the format config('ai.providers') expects.
     *
     * @return array<string, array{driver: string, key?: string, url?: string}>
     */
    public function getProviderConfig(): array
    {
        // Map our config group keys to the SDK's driver names
        $driverMap = [
            'openai' => 'openai',
            'anthropic' => 'anthropic',
            'gemini' => 'gemini',
            'azure-openai' => 'azure',
            'groq' => 'groq',
            'xai' => 'xai',
            'deepseek' => 'deepseek',
            'mistral' => 'mistral',
            'ollama' => 'ollama',
            'elevenlabs' => 'eleven',
            'cohere' => 'cohere',
            'jina' => 'jina',
            'voyageai' => 'voyageai',
        ];

        $providers = [];
        $configGroups = config($this->configKey, []);

        foreach ($configGroups as $groupKey => $group) {
            if ($groupKey === 'general' || ! isset($driverMap[$groupKey])) {
                continue;
            }

            $slug = str_replace('-', '_', $groupKey);
            $enabled = $this->get("{$slug}_enabled", false);

            if (! $enabled) {
                continue;
            }

            $sdkDriver = $driverMap[$groupKey];

            $providerConfig = [
                'driver' => $sdkDriver,
            ];

            // Ollama has no API key (local provider)
            if ($groupKey !== 'ollama') {
                $apiKey = $this->get("{$slug}_api_key");
                if ($apiKey) {
                    $providerConfig['key'] = $apiKey;
                }
            }

            $baseUrl = $this->get("{$slug}_base_url");
            if ($baseUrl) {
                $providerConfig['url'] = $baseUrl;
            }

            // Azure OpenAI specific fields
            if ($groupKey === 'azure-openai') {
                $version = $this->get('azure_openai_api_version');
                $deployment = $this->get('azure_openai_deployment');

                if ($version) {
                    $providerConfig['api_version'] = $version;
                }

                if ($deployment) {
                    $providerConfig['deployment'] = $deployment;
                }
            }

            // Use SDK's provider key, not our config group key
            $providers[$sdkDriver] = $providerConfig;
        }

        return $providers;
    }

    /**
     * Get all DB values as a flat key => value array (cached).
     */
    protected function getAllFromDb(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return AiSetting::pluck('value', 'key')->toArray();
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
}
