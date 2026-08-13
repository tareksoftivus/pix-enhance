<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds demo blog categories and published posts for presentation.
 *
 * Idempotent: categories key on slug, posts key on slug.
 */
class DemoBlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = Admin::query()->orderBy('id')->first();

        $categories = [];
        foreach ($this->categories() as $index => $name) {
            $categories[$name] = BlogCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true, 'sort_order' => $index + 1]
            );
        }

        foreach ($this->posts() as $index => $data) {
            BlogPost::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'blog_category_id' => $categories[$data['category']]->id ?? null,
                    'author_id' => $author?->id,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'body' => $data['body'],
                    'status' => 'published',
                    'published_at' => now()->subDays(count($this->posts()) - $index),
                ]
            );
        }
    }

    /**
     * @return array<int, string>
     */
    protected function categories(): array
    {
        return ['Product', 'Engineering', 'Company', 'Guides'];
    }

    /**
     * @return array<int, array{title: string, category: string, excerpt: string, body: string}>
     */
    protected function posts(): array
    {
        return [
            [
                'title' => 'Introducing Our New Dashboard',
                'category' => 'Product',
                'excerpt' => 'A faster, cleaner way to track revenue, payments and activity from one focused view.',
                'body' => '<p>We are excited to announce a completely redesigned dashboard. It brings your most important metrics — revenue, payment health, and recent activity — into a single focused view.</p><p>Everything loads faster and adapts to your screen, so you can keep an eye on what matters from any device.</p>',
            ],
            [
                'title' => 'How We Scaled to Millions of Requests',
                'category' => 'Engineering',
                'excerpt' => 'A look behind the scenes at the architecture powering our platform.',
                'body' => '<p>Scaling a platform is never a single change — it is a series of deliberate decisions. In this post we walk through how we approached caching, queueing, and database indexing.</p><p>The result is a system that stays responsive even under heavy load.</p>',
            ],
            [
                'title' => 'Meet the Team Behind the Product',
                'category' => 'Company',
                'excerpt' => 'The people who build, support and care about what we ship every day.',
                'body' => '<p>Great products are built by great teams. We would like to introduce the people who make it happen, from engineering to support.</p><p>Their dedication is what keeps us moving forward.</p>',
            ],
            [
                'title' => 'Getting Started: Your First 10 Minutes',
                'category' => 'Guides',
                'excerpt' => 'A quick walkthrough to get you up and running in no time.',
                'body' => '<p>New here? This guide covers the essentials — setting up your account, inviting your team, and configuring your first workflow.</p><p>By the end you will have a working setup and know where everything lives.</p>',
            ],
            [
                'title' => 'Five Tips to Get the Most Out of Reports',
                'category' => 'Guides',
                'excerpt' => 'Small habits that turn raw numbers into real insight.',
                'body' => '<p>Reports are only useful when you know how to read them. Here are five practical tips to help you spot trends, compare periods, and export exactly what you need.</p>',
            ],
            [
                'title' => 'What is New This Quarter',
                'category' => 'Product',
                'excerpt' => 'A roundup of the features and improvements we shipped recently.',
                'body' => '<p>Over the last quarter we shipped dozens of improvements based on your feedback. Here is a roundup of the highlights, from a redesigned dashboard to faster exports.</p>',
            ],
        ];
    }
}
