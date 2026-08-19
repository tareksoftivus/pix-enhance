<?php

namespace App\Modules\RenderJobs\Services;

use App\Modules\AiSettings\Services\AiSettingsService;
use App\Modules\RenderJobs\Exceptions\AiRenderException;
use App\Modules\RenderJobs\Models\RenderJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Exceptions\AiException;
use Laravel\Ai\Files\Image as AiImage;
use Laravel\Ai\Image;

/**
 * Renders a job by sending the source image, plus a strict natural-language
 * instruction compiled from the job's tool and settings, to an AI image
 * editing provider (Gemini only today — see AiSettingsService::$imageCapableDrivers).
 */
class AiImageRenderProcessor
{
    public function __construct(
        protected AiSettingsService $aiSettings
    ) {}

    /**
     * Whether this job's model refers to an enabled, image-capable provider model.
     */
    public function canHandle(RenderJob $job): bool
    {
        $model = (string) $job->model;

        return $model !== '' && str_contains($model, ':') && $this->aiSettings->isEnabledImageModel($model);
    }

    /**
     * @return array<string, mixed>
     */
    public function process(RenderJob $job): array
    {
        [$provider, $model] = explode(':', (string) $job->model, 2);

        $prompt = $this->buildPrompt($job);
        $source = AiImage::fromStorage($job->source_path, $job->source_disk);

        try {
            $response = Image::of($prompt)
                ->attachments([$source])
                ->generate($this->labFor($provider), $model);
        } catch (AiException $exception) {
            Log::error('AI render failed', [
                'render_job_uuid' => $job->uuid,
                'provider' => $provider,
                'model' => $model,
                'exception' => $exception->getMessage(),
            ]);

            throw new AiRenderException('The AI provider could not process this image. Please try again or choose a different tool.', previous: $exception);
        }

        if ($response->count() === 0) {
            Log::warning('AI render returned no image', [
                'render_job_uuid' => $job->uuid,
                'provider' => $provider,
                'model' => $model,
            ]);

            throw new AiRenderException('The AI provider declined to edit this image. Please try again or choose a different tool.');
        }

        $image = $response->firstImage();
        $bytes = $image->content();
        $format = $this->supportedFormat($job->output_format);
        $outputName = $this->outputName($job, $format);
        $outputPath = 'render-jobs/'.$job->user_id.'/'.now()->format('Y/m').'/outputs/'.$outputName;
        $disk = config('render-jobs.disk', 'public');

        Storage::disk($disk)->put($outputPath, $bytes);

        $dimensions = @getimagesizefromstring($bytes);

        Log::info('AI render succeeded', [
            'render_job_uuid' => $job->uuid,
            'provider' => $provider,
            'model' => $model,
            'images_returned' => $response->count(),
            'output_mime' => $image->mime,
            'output_size' => strlen($bytes),
        ]);

        return [
            'output_disk' => $disk,
            'output_path' => $outputPath,
            'output_name' => $outputName,
            'output_size' => strlen($bytes),
            'output_mime' => $image->mime ?: $this->mimeForFormat($format),
            'output_width' => is_array($dimensions) ? (int) $dimensions[0] : $job->target_width,
            'output_height' => is_array($dimensions) ? (int) $dimensions[1] : $job->target_height,
        ];
    }

    /**
     * Compile the job's tool and settings into a strict edit instruction.
     */
    protected function buildPrompt(RenderJob $job): string
    {
        $settings = (array) $job->settings;

        $lines = [
            'You are an image editing engine. Apply ONLY the following edits to the attached image and return the edited image.',
            'Do not add commentary, watermarks, borders, or crop the image unless explicitly instructed below.',
            "Tool: {$job->toolLabel()}.",
        ];

        $lines[] = match ($job->tool) {
            'upscaler' => "Upscale the image to {$job->target_width}x{$job->target_height} pixels, sharpening detail without introducing artefacts.",
            'face-restoration' => 'Restore and sharpen facial detail, correcting blur and compression artefacts while keeping the subject natural and unaltered in identity.',
            'background-removal' => 'Remove the background entirely, keeping the main subject intact with clean, precise edges.',
            default => "Enhance the image for the \"{$job->tool}\" tool.",
        };

        if (isset($settings['detail'])) {
            $lines[] = "Detail enhancement strength: {$settings['detail']}/100.";
        }

        if (isset($settings['fidelity'])) {
            $lines[] = "Preserve original likeness/fidelity at strength: {$settings['fidelity']}/100.";
        }

        if (isset($settings['edge'])) {
            $lines[] = "Edge refinement strength: {$settings['edge']}/100.";
        }

        if (! empty($settings['face'])) {
            $lines[] = 'Restore and sharpen any faces present.';
        }

        if (! empty($settings['denoise'])) {
            $lines[] = 'Remove noise and compression artefacts.';
        }

        if (! empty($settings['colour'])) {
            $lines[] = 'Boost and correct colour balance naturally.';
        }

        if (! empty($settings['hair'])) {
            $lines[] = 'Restore fine hair detail.';
        }

        if (! empty($settings['shadow'])) {
            $lines[] = 'Preserve natural shadow and lighting consistency.';
        }

        if (! empty($settings['backdrop'])) {
            $lines[] = match ($settings['backdrop']) {
                'transparent' => 'Replace the removed background with a transparent background.',
                'white' => 'Replace the removed background with a solid white background.',
                'blur' => 'Replace the removed background with a softly blurred version of the original background.',
                default => "Replace the removed background with: {$settings['backdrop']}.",
            };
        }

        $lines[] = "Output format: {$job->output_format}.";
        $lines[] = 'Return only the final edited image.';

        return implode("\n", array_filter($lines));
    }

    protected function labFor(string $provider): Lab
    {
        return Lab::from($provider);
    }

    protected function supportedFormat(string $format): string
    {
        return in_array($format, ['png', 'jpg', 'webp'], true) ? $format : 'png';
    }

    protected function mimeForFormat(string $format): string
    {
        return match ($format) {
            'jpg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }

    protected function outputName(RenderJob $job, string $extension): string
    {
        $base = Str::slug(pathinfo($job->source_name, PATHINFO_FILENAME)) ?: 'render';

        return $base.'-'.$job->uuid.'.'.$extension;
    }
}
