<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Services\BlogPostService;
use App\Modules\Frontend\Services\MenuRenderService;
use App\Modules\Frontend\Services\ThemeRegistry;
use App\Modules\Frontend\Services\ThemeRenderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        protected BlogPostService $service,
        protected ThemeRegistry $themes,
        protected ThemeRenderService $themeRender,
        protected MenuRenderService $menus,
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

        $title = $activeCategory ? $activeCategory->name.' - '.__('Blog') : __('Blog');
        $description = $activeCategory
            ? __('Articles, workflows and product notes in :category.', ['category' => $activeCategory->name])
            : __('AI image enhancement guides, product updates and practical workflows from :name.', ['name' => config('app.name')]);

        return $this->renderEnhancePage(
            ['blog_hero', 'blog_posts'],
            [
                'title' => $title,
                'meta_title' => $title,
                'meta_description' => $description,
            ],
            [
                'posts' => $posts,
                'categories' => BlogCategory::active()->orderBy('sort_order')->orderBy('name')->get(),
                'activeCategory' => $activeCategory,
                'canonicalUrl' => $activeCategory ? route('blog.index', ['category' => $activeCategory->slug]) : route('blog.index'),
            ],
        );
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
            ->view('blog::sitemap', ['posts' => $posts])
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

        $wordCount = str_word_count(strip_tags($post->body ?? ''));
        $readingMinutes = max(1, (int) ceil($wordCount / 220));
        $articleImage = $post->coverImageUrl() ?: asset('assets/frontend/enhance/img/samples/valley-after.webp');
        $openGraphImage = str_starts_with($articleImage, 'http://') || str_starts_with($articleImage, 'https://')
            ? $articleImage
            : url($articleImage);

        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->seoTitle(),
            'description' => $post->seoDescription(),
            'url' => route('blog.show', $post->slug),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.show', $post->slug)],
            'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
        ];

        if ($post->author) {
            $structuredData['author'] = ['@type' => 'Person', 'name' => $post->author->name];
        }

        if ($post->category) {
            $structuredData['articleSection'] = $post->category->name;
        }

        $structuredData['image'] = $openGraphImage;

        return $this->renderEnhancePage(
            ['blog_details_hero', 'blog_details_article', 'blog_details_related'],
            [
                'title' => $post->title,
                'meta_title' => $post->seoTitle(),
                'meta_description' => $post->seoDescription(),
            ],
            [
                'post' => $post,
                'related' => $related,
                'readingMinutes' => $readingMinutes,
                'articleImage' => $articleImage,
                'canonicalUrl' => route('blog.show', $post->slug),
                'openGraphType' => 'article',
                'openGraphImage' => $openGraphImage,
                'publishedTime' => $post->published_at?->toIso8601String(),
                'modifiedTime' => $post->updated_at?->toIso8601String(),
                'authorName' => $post->author?->name,
                'structuredData' => $structuredData,
            ],
        );
    }

    /**
     * Render blog routes through the active Enhance frontend layout.
     *
     * @param  array<int, string>  $sections
     * @param  array<string, mixed>  $pageAttributes
     * @param  array<string, mixed>  $data
     */
    protected function renderEnhancePage(array $sections, array $pageAttributes, array $data = []): View
    {
        $themeKey = 'enhance';

        return view($this->themeRender->layoutView($themeKey, 'page'), array_merge([
            'themeKey' => $themeKey,
            'theme' => $this->themes->get($themeKey),
            'themeVars' => $this->themeRender->themeVariables($themeKey),
            'page' => (object) array_merge([
                'title' => __('Blog'),
                'meta_title' => __('Blog'),
                'meta_description' => __('AI image enhancement guides, product updates and practical workflows.'),
            ], $pageAttributes),
            'resolvedMenus' => $this->menus->resolveForTheme($themeKey),
            'resolvedSections' => $this->resolvedEnhanceSections($sections),
            'mainClass' => 'blog-page',
        ], $data));
    }

    /**
     * @param  array<int, string>  $sections
     * @return array<int, array{view: string, supported: bool, section: object}>
     */
    protected function resolvedEnhanceSections(array $sections): array
    {
        return Collection::make($sections)
            ->map(fn (string $section) => [
                'view' => "frontend.themes.enhance.sections.{$section}",
                'supported' => true,
                'section' => (object) ['type' => $section, 'data' => []],
            ])
            ->all();
    }
}
