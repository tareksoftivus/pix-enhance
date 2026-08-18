<?php

namespace App\Modules\RenderJobs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRenderJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tools = array_keys(config('render-jobs.tools', []));
        $formats = config('render-jobs.output_formats', ['png', 'jpg', 'webp']);
        $maxUpload = (int) config('render-jobs.max_upload_kb', 51200);

        return [
            'source' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,image/avif', 'max:'.$maxUpload],
            'tool' => ['required', 'string', Rule::in($tools)],
            'model' => ['nullable', 'string', 'max:80'],
            'scale' => ['nullable', 'integer', 'min:1', 'max:16'],
            'format' => ['nullable', 'string', Rule::in($formats)],
            'output_format' => ['nullable', 'string', Rule::in($formats)],
            'detail' => ['nullable', 'integer', 'min:0', 'max:100'],
            'fidelity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'edge' => ['nullable', 'integer', 'min:0', 'max:100'],
            'face' => ['nullable', 'boolean'],
            'denoise' => ['nullable', 'boolean'],
            'colour' => ['nullable', 'boolean'],
            'hair' => ['nullable', 'boolean'],
            'shadow' => ['nullable', 'boolean'],
            'backdrop' => ['nullable', 'string', Rule::in(['transparent', 'white', 'blur'])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $validated = $this->validated();

        foreach (['face', 'denoise', 'colour', 'hair', 'shadow'] as $key) {
            $validated[$key] = $this->boolean($key);
        }

        $validated['output_format'] = $validated['output_format'] ?? $validated['format'] ?? 'png';

        return $validated;
    }
}
