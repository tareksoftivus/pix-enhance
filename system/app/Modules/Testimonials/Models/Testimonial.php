<?php

namespace App\Modules\Testimonials\Models;

use App\Modules\Media\Models\Media;
use App\Modules\Testimonials\Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    protected $fillable = [
        'client_name',
        'company_name',
        'designation',
        'quote',
        'rating',
        'sort_order',
        'active',
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function newFactory(): TestimonialFactory
    {
        return TestimonialFactory::new();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! filled($this->avatar)) {
            return null;
        }

        if (is_numeric($this->avatar)) {
            return Media::find($this->avatar)?->url;
        }

        $path = ltrim((string) $this->avatar, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'assets/') && file_exists(public_path($path))) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}
