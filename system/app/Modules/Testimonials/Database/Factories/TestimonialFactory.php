<?php

namespace App\Modules\Testimonials\Database\Factories;

use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'company_name' => fake()->company(),
            'designation' => fake()->jobTitle(),
            'quote' => fake()->paragraph(),
            'rating' => fake()->numberBetween(3, 5),
            'sort_order' => fake()->numberBetween(0, 20),
            'active' => true,
        ];
    }
}
