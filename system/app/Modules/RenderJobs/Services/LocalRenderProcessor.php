<?php

namespace App\Modules\RenderJobs\Services;

use App\Modules\RenderJobs\Models\RenderJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalRenderProcessor
{
    /**
     * @return array<string, mixed>
     */
    public function process(RenderJob $job): array
    {
        $sourcePath = Storage::disk($job->source_disk)->path($job->source_path);
        $format = $this->supportedFormat($job->output_format);
        $extension = $format === 'jpg' ? 'jpg' : $format;
        $outputName = $this->outputName($job, $extension);
        $outputPath = 'render-jobs/'.$job->user_id.'/'.now()->format('Y/m').'/outputs/'.$outputName;
        $absoluteOutputPath = Storage::disk(config('render-jobs.disk', 'public'))->path($outputPath);

        $this->ensureDirectory(dirname($absoluteOutputPath));

        $result = $this->renderWithGd($job, $sourcePath, $absoluteOutputPath, $format)
            ?? $this->copyOriginal($job, $sourcePath, $absoluteOutputPath);

        return array_merge($result, [
            'output_disk' => config('render-jobs.disk', 'public'),
            'output_path' => $outputPath,
            'output_name' => $outputName,
            'output_size' => filesize($absoluteOutputPath) ?: 0,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function renderWithGd(RenderJob $job, string $sourcePath, string $outputPath, string $format): ?array
    {
        $source = $this->loadImage($sourcePath, (string) $job->source_mime);

        if (! $source) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        [$targetWidth, $targetHeight] = $this->boundedDimensions(
            $job->target_width ?: $sourceWidth,
            $job->target_height ?: $sourceHeight
        );

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($format === 'jpg') {
            $background = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $background);
        } else {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
            imagefill($canvas, 0, 0, $transparent);
        }

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        $saved = match ($format) {
            'jpg' => imagejpeg($canvas, $outputPath, 92),
            'webp' => function_exists('imagewebp') ? imagewebp($canvas, $outputPath, 90) : false,
            default => imagepng($canvas, $outputPath, 6),
        };

        imagedestroy($source);
        imagedestroy($canvas);

        if (! $saved) {
            return null;
        }

        return [
            'output_mime' => $this->mimeForFormat($format),
            'output_width' => $targetWidth,
            'output_height' => $targetHeight,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function copyOriginal(RenderJob $job, string $sourcePath, string $outputPath): array
    {
        copy($sourcePath, $outputPath);

        return [
            'output_mime' => $job->source_mime,
            'output_width' => $job->source_width,
            'output_height' => $job->source_height,
        ];
    }

    protected function loadImage(string $path, string $mime): mixed
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            'image/avif' => function_exists('imagecreatefromavif') ? @imagecreatefromavif($path) : false,
            default => false,
        };
    }

    /**
     * @return array{0: int, 1: int}
     */
    protected function boundedDimensions(int $width, int $height): array
    {
        $maxPixels = (int) config('render-jobs.max_local_output_pixels', 12000000);

        if ($width * $height <= $maxPixels) {
            return [$width, $height];
        }

        $ratio = sqrt($maxPixels / ($width * $height));

        return [
            max(1, (int) floor($width * $ratio)),
            max(1, (int) floor($height * $ratio)),
        ];
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

    protected function ensureDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
