<?php

namespace App\Modules\AiSettings\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\AiSettings\Services\AiSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AiSettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ai-settings.view', only: ['index']),
            new Middleware('permission:ai-settings.edit', only: ['update']),
        ];
    }

    public function __construct(
        protected AiSettingsService $service
    ) {}

    public function index(): View
    {
        $groups = $this->service->getGroupedDefinitions();

        return view('ai-settings::admin.index', compact('groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $request->input('settings', []);
        $rules = [];
        $attributes = [];

        foreach (config('ai-settings', []) as $group) {
            foreach ($group['settings'] as $key => $definition) {
                if (isset($definition['rules'])) {
                    $rules["settings.{$key}"] = $definition['rules'];
                    $attributes["settings.{$key}"] = $definition['label'];
                }
            }
        }

        $request->validate($rules, [], $attributes);

        foreach ($settings as $key => $value) {
            $this->service->set($key, $value);
        }

        $tab = $request->input('_active_tab', array_key_first(config('ai-settings', [])));

        return redirect()->to(route('admin.ai-settings.index').'#'.$tab)
            ->with('success', __('AI settings updated successfully.'));
    }
}
