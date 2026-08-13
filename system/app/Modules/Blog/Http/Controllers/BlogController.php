<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Services\BlogPostService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        protected BlogPostService $service
    ) {}

    /**
     * Public blog listing, optionally filtered by category slug.
     */
    public function index(Request $request): View
    {
        $activeCategory = null;

        if ($slug = $request->query('category')) {
            $activeCategory = BlogCategory::active()->where('slug', $slug)->first();
        }

        $posts = $this->service->publishedForPublic($activeCategory?->id);

        return view('blog::public.index', [
            'posts' => $posts,
            'categories' => BlogCategory::active()->orderBy('sort_order')->orderBy('name')->get(),
            'activeCategory' => $activeCategory,
        ]);
    }

    /**
     * XML sitemap of the blog index and every published post.
     */
    public function sitemap(): Response
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'published_at']);

        return response()
            ->view('blog::public.sitemap', ['posts' => $posts])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Public single-post page.
     */
    public function show(string $slug): View
    {
        $post = BlogPost::published()
            ->with(['category', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = BlogPost::published()
            ->where('blog_category_id', $post->blog_category_id)
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog::public.show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}
