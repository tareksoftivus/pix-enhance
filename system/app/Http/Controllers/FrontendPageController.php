<?php

namespace App\Http\Controllers;

use App\Modules\Frontend\Services\ActiveThemeResolver;
use App\Modules\Frontend\Services\FrontendPageService;
use App\Modules\Frontend\Services\PageRenderService;
use Illuminate\Support\Facades\Schema;
use Throwable;

class FrontendPageController extends Controller
{
    public function __construct(
        protected FrontendPageService $pages,
        protected ActiveThemeResolver $activeThemeResolver,
        protected PageRenderService $renderer
    ) {}

    public function home()
    {
        try {
            if (! Schema::hasTable('pages')) {
                return view('welcome');
            }

            $page = $this->pages->homePage();

            if (! $page) {
                return view('welcome');
            }

            $payload = $this->renderer->payload($page, $this->activeThemeResolver->resolve());

            return response()->view($payload['layoutView'], $payload);
        } catch (Throwable) {
            return view('welcome');
        }
    }

    public function show(string $slug)
    {
        try {
            if (! Schema::hasTable('pages')) {
                abort(404);
            }

            $page = $this->pages->findBySlug($slug);
        } catch (Throwable) {
            abort(404);
        }

        abort_if(! $page, 404);

        $payload = $this->renderer->payload($page, $this->activeThemeResolver->resolve());

        return response()->view($payload['layoutView'], $payload);
    }
}
