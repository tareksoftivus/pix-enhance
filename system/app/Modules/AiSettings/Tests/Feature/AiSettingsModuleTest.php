<?php

namespace App\Modules\AiSettings\Tests\Feature;

use App\Modules\AiSettings\Services\AiSettingsService;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AiSettingsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_settings_module_is_registered(): void
    {
        $module = app(ModuleRegistry::class)->find('ai-settings');

        $this->assertNotNull($module);
        $this->assertNotNull($module['descriptor']);
        $this->assertTrue(Route::has('admin.ai-settings.index'));
    }

    public function test_it_lists_gemini_and_ollama_groups_from_config(): void
    {
        $groups = app(AiSettingsService::class)->getGroupedDefinitions();

        $this->assertArrayHasKey('general', $groups);
        $this->assertArrayHasKey('gemini', $groups);
        $this->assertArrayHasKey('ollama', $groups);
    }

    public function test_enabling_gemini_surfaces_the_api_key_in_provider_config(): void
    {
        $service = app(AiSettingsService::class);
        $service->set('gemini_enabled', true);
        $service->set('gemini_api_key', 'test-gemini-key');

        $providers = $service->getProviderConfig();

        $this->assertSame('test-gemini-key', $providers['gemini']['key'] ?? null);
    }

    public function test_it_lists_enabled_gemini_image_models(): void
    {
        $service = app(AiSettingsService::class);
        $service->set('gemini_enabled', true);
        $service->set('gemini_image_models', ['gemini-2.5-flash-image']);

        $models = $service->getEnabledImageModels();

        $this->assertSame([
            'value' => 'gemini:gemini-2.5-flash-image',
            'label' => 'Google Gemini — gemini-2.5-flash-image',
            'provider' => 'gemini',
            'model' => 'gemini-2.5-flash-image',
        ], $models[0] ?? null);
    }

    public function test_it_excludes_ollama_from_enabled_image_models(): void
    {
        $service = app(AiSettingsService::class);
        $service->set('ollama_enabled', true);

        $models = $service->getEnabledImageModels();

        $providers = array_column($models, 'provider');

        $this->assertNotContains('ollama', $providers);
    }
}
