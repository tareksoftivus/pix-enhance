<?php

namespace App\Modules\UserWorkspace\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'render_finished' => ['sometimes', 'boolean'],
            'credits_low' => ['sometimes', 'boolean'],
            'weekly_summary' => ['sometimes', 'boolean'],
            'product_news' => ['sometimes', 'boolean'],
            'desktop_notifications_enabled' => ['sometimes', 'boolean'],
            'completion_sound_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
