<?php

use App\Models\Admin;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function blogAdmin(): Admin
{
    $role = Role::findOrCreate('super-admin', 'admin');

    $admin = Admin::query()->create([
        'name' => 'Blog Admin',
        'email' => 'blog-admin@example.com',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $admin->assignRole($role);

    return $admin;
}

it('registers the blog module and its routes', function () {
    expect(Route::has('admin.blog-posts.index'))->toBeTrue()
        ->and(Route::has('admin.blog-categories.index'))->toBeTrue()
        ->and(Route::has('blog.index'))->toBeTrue()
        ->and(Route::has('blog.show'))->toBeTrue();
});

it('creates a post with a generated unique slug and captures the author', function () {
    $admin = blogAdmin();
    $category = BlogCategory::create(['name' => 'News', 'slug' => 'news', 'is_active' => true]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.blog-posts.store'), [
            'title' => 'Hello World',
            'blog_category_id' => $category->id,
            'excerpt' => 'A first post.',
            'body' => '<p>Body</p>',
            'status' => 'published',
        ])
        ->assertRedirect(route('admin.blog-posts.index'));

    $post = BlogPost::first();

    expect($post->slug)->toBe('hello-world')
        ->and($post->author_id)->toBe($admin->id)
        ->and($post->status)->toBe('published')
        ->and($post->published_at)->not->toBeNull();
});

it('disambiguates duplicate slugs', function () {
    $admin = blogAdmin();

    foreach (['First', 'Second'] as $title) {
        $this->actingAs($admin, 'admin')->post(route('admin.blog-posts.store'), [
            'title' => 'Same Title',
            'body' => '<p>x</p>',
            'status' => 'draft',
        ]);
    }

    expect(BlogPost::pluck('slug')->all())->toEqual(['same-title', 'same-title-2']);
});

it('sets and clears published_at as status changes', function () {
    $admin = blogAdmin();

    // Draft first — no published_at.
    $this->actingAs($admin, 'admin')->post(route('admin.blog-posts.store'), [
        'title' => 'Lifecycle',
        'body' => '<p>x</p>',
        'status' => 'draft',
    ]);
    $post = BlogPost::first();
    expect($post->published_at)->toBeNull();

    // Publish — published_at set.
    $this->actingAs($admin, 'admin')->put(route('admin.blog-posts.update', $post), [
        'title' => 'Lifecycle',
        'body' => '<p>x</p>',
        'status' => 'published',
    ]);
    $firstPublishedAt = $post->fresh()->published_at;
    expect($firstPublishedAt)->not->toBeNull();

    // Unpublish — published_at cleared.
    $this->actingAs($admin, 'admin')->put(route('admin.blog-posts.update', $post), [
        'title' => 'Lifecycle',
        'body' => '<p>x</p>',
        'status' => 'draft',
    ]);
    expect($post->fresh()->published_at)->toBeNull();
});

it('stores an uploaded cover image', function () {
    Storage::fake('public');
    $admin = blogAdmin();

    $this->actingAs($admin, 'admin')->post(route('admin.blog-posts.store'), [
        'title' => 'With Cover',
        'body' => '<p>x</p>',
        'status' => 'draft',
        'cover_image' => UploadedFile::fake()->image('cover.jpg'),
    ]);

    $post = BlogPost::first();
    expect($post->cover_image)->not->toBeNull();
    Storage::disk('public')->assertExists($post->cover_image);
});

it('shows only published posts on the public listing', function () {
    $admin = blogAdmin();
    BlogPost::create(['title' => 'Live Post', 'slug' => 'live-post', 'body' => '<p>x</p>', 'status' => 'published', 'published_at' => now()->subDay(), 'author_id' => $admin->id]);
    BlogPost::create(['title' => 'Hidden Draft', 'slug' => 'hidden-draft', 'body' => '<p>x</p>', 'status' => 'draft', 'author_id' => $admin->id]);

    $this->get(route('blog.index'))
        ->assertSuccessful()
        ->assertSee('Live Post')
        ->assertDontSee('Hidden Draft');
});

it('renders a single published post and 404s a draft', function () {
    $admin = blogAdmin();
    $published = BlogPost::create(['title' => 'Readable', 'slug' => 'readable', 'body' => '<p>Article body</p>', 'status' => 'published', 'published_at' => now()->subDay(), 'author_id' => $admin->id]);
    $draft = BlogPost::create(['title' => 'Secret', 'slug' => 'secret', 'body' => '<p>x</p>', 'status' => 'draft', 'author_id' => $admin->id]);

    $this->get(route('blog.show', $published->slug))
        ->assertSuccessful()
        ->assertSee('Readable')
        ->assertSee('Article body', false);

    $this->get(route('blog.show', $draft->slug))->assertNotFound();
});

it('filters the public listing by category', function () {
    $admin = blogAdmin();
    $news = BlogCategory::create(['name' => 'News', 'slug' => 'news', 'is_active' => true]);
    $tips = BlogCategory::create(['name' => 'Tips', 'slug' => 'tips', 'is_active' => true]);

    BlogPost::create(['title' => 'A News Item', 'slug' => 'a-news-item', 'body' => '<p>x</p>', 'status' => 'published', 'published_at' => now()->subDay(), 'blog_category_id' => $news->id, 'author_id' => $admin->id]);
    BlogPost::create(['title' => 'A Handy Tip', 'slug' => 'a-handy-tip', 'body' => '<p>x</p>', 'status' => 'published', 'published_at' => now()->subDay(), 'blog_category_id' => $tips->id, 'author_id' => $admin->id]);

    $this->get(route('blog.index', ['category' => 'news']))
        ->assertSuccessful()
        ->assertSee('A News Item')
        ->assertDontSee('A Handy Tip');
});

it('emits full SEO meta on a published post', function () {
    $admin = blogAdmin();
    $category = BlogCategory::create(['name' => 'News', 'slug' => 'news', 'is_active' => true]);

    $post = BlogPost::create([
        'title' => 'SEO Post',
        'slug' => 'seo-post',
        'excerpt' => 'Fallback description.',
        'body' => '<p>Body</p>',
        'meta_title' => 'Custom Meta Title',
        'meta_description' => 'Custom meta description for engines.',
        'status' => 'published',
        'published_at' => now()->subDay(),
        'blog_category_id' => $category->id,
        'author_id' => $admin->id,
    ]);

    $response = $this->get(route('blog.show', $post->slug))->assertSuccessful();

    $response->assertSee('<title>Custom Meta Title', false)
        ->assertSee('name="description" content="Custom meta description for engines."', false)
        ->assertSee('rel="canonical" href="'.route('blog.show', $post->slug).'"', false)
        ->assertSee('property="og:type" content="article"', false)
        ->assertSee('property="article:published_time"', false)
        ->assertSee('name="twitter:card"', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type":"BlogPosting"', false);
});

it('falls back to title and excerpt when meta fields are empty', function () {
    $admin = blogAdmin();
    $post = BlogPost::create([
        'title' => 'Fallback Post',
        'slug' => 'fallback-post',
        'excerpt' => 'The excerpt becomes the description.',
        'body' => '<p>Body</p>',
        'status' => 'published',
        'published_at' => now()->subDay(),
        'author_id' => $admin->id,
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertSuccessful()
        ->assertSee('<title>Fallback Post', false)
        ->assertSee('name="description" content="The excerpt becomes the description."', false);
});

it('serves an xml sitemap of published posts only', function () {
    $admin = blogAdmin();
    BlogPost::create(['title' => 'In Map', 'slug' => 'in-map', 'body' => '<p>x</p>', 'status' => 'published', 'published_at' => now()->subDay(), 'author_id' => $admin->id]);
    BlogPost::create(['title' => 'Not In Map', 'slug' => 'not-in-map', 'body' => '<p>x</p>', 'status' => 'draft', 'author_id' => $admin->id]);

    $this->get(route('blog.sitemap'))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee(route('blog.show', 'in-map'), false)
        ->assertDontSee(route('blog.show', 'not-in-map'), false)
        ->assertSee('<urlset', false);
});

it('blocks blog admin routes without permission', function () {
    $admin = Admin::query()->create([
        'name' => 'Weak', 'email' => 'weak-blog@example.com', 'password' => 'password',
        'is_active' => true, 'email_verified_at' => now(),
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.blog-posts.index'))
        ->assertForbidden();
});
