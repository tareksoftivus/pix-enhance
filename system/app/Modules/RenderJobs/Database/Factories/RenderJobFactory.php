<?php

namespace App\Modules\RenderJobs\Database\Factories;

use App\Models\User;
use App\Modules\RenderJobs\Models\RenderJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RenderJob>
 */
class RenderJobFactory extends Factory
{
    protected $model = RenderJob::class;

    public function definition(): array
    {
        $uuid = (string) Str::ulid();

        return [
            'uuid' => $uuid,
            'user_id' => User::factory(),
            'tool' => 'upscaler',
            'status' => 'completed',
            'progress' => 100,
            'model' => 'enhance-xl',
            'scale' => 2,
            'output_format' => 'png',
            'settings' => [],
            'credits_cost' => 1,
            'source_disk' => 'public',
            'source_path' => 'render-jobs/sources/'.$uuid.'.png',
            'source_name' => 'source.png',
            'source_mime' => 'image/png',
            'source_size' => fake()->numberBetween(50_000, 2_000_000),
            'source_width' => 800,
            'source_height' => 600,
            'target_width' => 1600,
            'target_height' => 1200,
            'output_disk' => 'public',
            'output_path' => 'render-jobs/outputs/'.$uuid.'.png',
            'output_name' => 'output.png',
            'output_mime' => 'image/png',
            'output_size' => fake()->numberBetween(50_000, 2_000_000),
            'output_width' => 1600,
            'output_height' => 1200,
            'started_at' => now()->subMinutes(2),
            'completed_at' => now(),
            'duration_ms' => fake()->numberBetween(500, 5000),
        ];
    }

    public function queued(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'queued',
            'progress' => 0,
            'started_at' => null,
            'completed_at' => null,
            'duration_ms' => null,
            'output_disk' => null,
            'output_path' => null,
            'output_name' => null,
            'output_mime' => null,
            'output_size' => null,
            'output_width' => null,
            'output_height' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'failed',
            'progress' => 0,
            'completed_at' => null,
            'failed_at' => now(),
            'error_message' => 'Processing failed.',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'cancelled',
            'progress' => 0,
            'completed_at' => null,
            'cancelled_at' => now(),
        ]);
    }
}
