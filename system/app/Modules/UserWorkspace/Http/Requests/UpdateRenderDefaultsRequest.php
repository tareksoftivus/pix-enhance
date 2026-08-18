<?php

namespace App\Modules\UserWorkspace\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRenderDefaultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'default_model' => ['required', 'string', Rule::in(['auto', 'enhance-xl', 'photo-real', 'illustration'])],
            'default_scale' => ['required', 'integer', Rule::in([2, 4, 8])],
            'default_format' => ['required', 'string', Rule::in(['png', 'jpg', 'webp', 'tiff'])],
            'face_restoration' => ['sometimes', 'boolean'],
            'auto_download' => ['sometimes', 'boolean'],
            'source_retention_days' => ['required', 'integer', Rule::in([1, 7, 30, 90])],
        ];
    }
}
